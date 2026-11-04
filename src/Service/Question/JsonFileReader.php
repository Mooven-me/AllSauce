<?php

namespace App\Service\Question;

use App\Entity\Sauce;
use App\Sauce\Question\AbstractDownloaderManager;

class JsonFileReader extends FileReader {

    private string $folderName;

    public function setFolderName(string $folderName){
        $this->folderName = $folderName;
    }

    public function read(string $file) : array {
        $fileContent = file_get_contents(AbstractDownloaderManager::$DOWNLOAD_PATH.'/'.$this->folderName.'/'.$file);
        return json_decode($fileContent, true);
    }
}