<?php

namespace App\Sauce\Question;

abstract class AbstractUpdateManager extends AbstractDownloaderManager {
    abstract function getVersion() : string;
}