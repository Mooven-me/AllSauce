<?php

namespace App\Sauce\Question\lolsauce;

use App\Sauce\Question\AbstractUpdateManager;

class LoLSauceUpdater extends AbstractUpdateManager {
    
    public function getVersion() : string {
        $response = $this->httpClient->request('GET', 'https://ddragon.leagueoflegends.com/api/versions.json');
        return $response->toArray()[0];
    }

    public function run() : void {
        $fileName = 'lolsauce';
        #$this->download('https://ddragon.leagueoflegends.com/cdn/dragontail-'.$this->getVersion().'.tgz', $fileName);
        $this->extract($fileName);
    }
}