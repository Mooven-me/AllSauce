<?php

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Formable
{
    public function __construct(public string $path)
    {
    }
}