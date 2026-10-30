<?php

namespace App\Sauce\Css;

class LoLSauceCss implements CssInterface
{
    public function getCss(): array
    {
        return [
            '.left-bar' => [
                'background-color' => 'rgba(9, 55, 64, 0.25)'
            ],
            '.top-bar' => [
                'background-color' => 'rgba(13, 202, 240, 0.25)'
            ],
            '.animated-background' => [
                // animated background
                "position" => "fixed",
                "right" => "0",
                "bottom" => "0",
                "z-index" => "-1",
                "transform" => "rotate(25deg)",
                "background-image" => "linear-gradient(#0b0046ce,#0b0046ce),url(/assets/background/lolsauce_background.png)",
                "background-size" => "1536px 906px",
                "width" => "220%",
                "height" => "220%",
                "top" => "-60%",
                "left" => "-60%",
                "background-repeat" => "repeat",
                "animation" => "scroll-diag 60s linear infinite",
            ],
            ':root' => [
                '--bs-border-color' => 'rgb(13,110,253)',
                '--bs-btn-active-bg' => '#22253dcc',
                '--bs-primary' => '#22253dcc',
                '--bs-body-bg' => 'transparent',
                '--bs-body-color' => 'rgb(21, 106, 181)',
            ],
            '.btn-primary' => [
                '--bs-btn-color' => '#fff',
                '--bs-btn-bg' => '#22253dcc',
                '--bs-btn-border-color' => '#6c757d',
                '--bs-btn-hover-color' => '#fff',
                '--bs-btn-hover-bg' => '#5c636a',
                '--bs-btn-hover-border-color' => '#565e64',
                '--bs-btn-focus-shadow-rgb' => '130, 138, 145',
                '--bs-btn-active-color' => '#fff',
                '--bs-btn-active-bg' => '#565e64',
                '--bs-btn-active-border-color' => '#51585e',
                '--bs-btn-active-shadow' => 'inset 0 3px 5px rgba(0, 0, 0, 0.125)',
                '--bs-btn-disabled-color' => '#fff',
                '--bs-btn-disabled-bg' => '#6c757d',
                '--bs-btn-disabled-border-color' => '#6c757d',
                'box-shadow' => 'var(--bs-box-shadow)',
            ],
            '.lobby-title' => [
                'color' => 'white'
            ]
        ];
    }
}