<?php
namespace Povils\PHPMND\Languages;

use Povils\PHPMND\Language;

class En extends Language
{
    public static function providesLanguages(): array
    {
        return [
            'en',
            'en_GB',
            'en_US'
        ];
    }

    public function specialNumbers(): array
    {
        return [
            2 => [
                'half',
            ],
            3 => [
                'third',
            ],
            7 => [
                'week',
            ],
            10 => [
                'tenth',
                'decile',
            ],
            24 => [
                'hours',
            ],
            28 => [
                'February',
            ],
            60 => [
                'second',
                'minute',
            ],
            100 => [
                'percent',
                'centile'
            ],
        ];
    }
}
