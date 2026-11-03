<?php

namespace App\Command;

use App\Sauce\Question\AbstractDownloaderManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'allsauce:load:questions',
    description: 'Download and load every questions of allsauce sauces',
)]
class AllsauceLoadQuestionsCommand{
    public function __invoke(
        SymfonyStyle $io,
        /** @var iterable<AbstractDownloaderManager> $saucesDownloadManager */
        #[AutowireIterator(AbstractDownloaderManager::class)]
        iterable $saucesDownloadManager
    ): int {
        set_time_limit(-1);
        ini_set('memory_limit', '-1');

        foreach($saucesDownloadManager as $sauceDownloadManager){
            $io->info('runnning : '.$sauceDownloadManager::class);
            $sauceDownloadManager->run();
            $io->info('end of : '.$sauceDownloadManager::class);
        }

        $io->success('end of every sauces !');
        return Command::SUCCESS;
    }
}
