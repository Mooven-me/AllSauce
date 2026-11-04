<?php

namespace App\Sauce\Question;

use App\Entity\Sauce;
use App\Enum\SauceServiceEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[AutoconfigureTag]
abstract class AbstractDownloaderManager {

    protected HttpClientInterface $httpClient;
    protected Filesystem $filesystem;
    protected EntityManagerInterface $em;
    public static string $DOWNLOAD_PATH = '/tmp/allsauce';

    #[Required]
    public function setHttpClient(HttpClientInterface $httpClient){
        $this->httpClient = $httpClient;
    }

    #[Required]
    public function setFilesystem(Filesystem $filesystem){
        $this->filesystem = $filesystem;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->em = $em;
    }

    abstract public function getSauceServiceEnum() : SauceServiceEnum;

    protected function download(string $url, ?string $fileName = null, string $method = 'GET') : void {
        $response = $this->httpClient->request($method, $url);

        // generate the file name and full path
        if(!$fileName){
            $fileName = $this->getSauce()->getSlug();
        }
        
        if(!$this->filesystem->exists(self::$DOWNLOAD_PATH)){
            $this->filesystem->mkdir(self::$DOWNLOAD_PATH);
        }
        
        $filePath = self::$DOWNLOAD_PATH . '/' . $fileName;

        // store the new file (this method allow streaming directly)
        $fileHandle = fopen($filePath.'.tgz', 'w');
        foreach ($this->httpClient->stream($response) as $chunk) {
            if ($chunk->isTimeout() || $chunk->isFirst()) {
                continue;
            }
            if ($chunk->isLast()) {
                break;
            }
            fwrite($fileHandle, $chunk->getContent());
        }

        fclose($fileHandle);
    }

    protected function extract(?string $fileName = null) : void {
        $filePath = self::$DOWNLOAD_PATH . '/' . $fileName;

        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->mkdir($filePath);
        }

        // generate the file name and full path
        if(!$fileName){
            $fileName = $this->getSauce()->getSlug();
        }

        $process = Process::fromShellCommandline('tar -xzf ' . escapeshellarg($filePath.'.tgz') . ' -C ' . escapeshellarg($filePath) . ' --no-same-owner --no-same-permissions');
        $process->setTimeout(3600);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // delete the archive
        unlink($filePath.'.tgz');
    }

    abstract public function run() : void;

    protected function getSauce() : Sauce {
        return $this->em->getRepository(Sauce::class)->findOneBySauceServiceEnum($this->getSauceServiceEnum());
    }
}