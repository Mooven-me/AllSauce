<?php

namespace App\Sauce\Question\lolsauce;

use App\Enum\LanguageEnum;
use App\Sauce\Question\AbstractDownloaderManager;
use App\Service\Question\JsonFileReader;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class LoLSauceUtilManager {

    public static array $mapLanguageEnumToLoL = [
        LanguageEnum::FR->value => 'fr_FR',
        LanguageEnum::EN->value => 'en_GB'
    ];

    public function __construct(
        private CacheInterface $cache,
        private LoLSauceUpdater $lolSauceUpdater,
        private JsonFileReader $json,
    ){
        $json->setFolderName('lolsauce/'.$this->getVersion());
    }

    public function getVersion() : string {
        return $this->cache->get('lolsauce_version', function(ItemInterface $item) : string {
            $item->expiresAfter(60);
            return $this->lolSauceUpdater->getVersion();
        });
    }

    public function getChampions(string $language = 'fr_FR') : array {
        static $champions = [];

        if (isset($champions[$language])) {
            return $champions;
        }

        return $champions[$language] = $this->json->read('/data/'.$language.'/champion.json')['data'];
    }

    public function getChampionById(string $championId, string $language = 'fr_FR') {
        return $this->json->read('/data/'.$language.'/champion/'.$championId.'.json')['data'][$championId];
    }

    public function getChampionNameByLanguage(string $championId, LanguageEnum $language) : string {
        return $this->getChampionById(
            $championId, 
            self::$mapLanguageEnumToLoL[$language->value]
        )['name'];
    }
}