<?php
namespace Povils\PHPMND\Languages;

use Povils\PHPMND\Language;

class En implements Language
{
    protected $specialNumbers = [
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

    protected $numberMapping = [
        'zero',
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
        'seventeen',
        'eighteen',
        'nineteen',
        'twenty',
        30 => 'thirty',
        40 => 'forty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
    ];

    public function parse(int $number): array
    {

        end($this->numberMapping);
        $final = $this->specialNumbers[$number] ?? [];

        if ($number < 0) {
            $final [] = 'minus';
            $final [] = 'negative';

            $number = -$number;
        }

        while (prev($this->numberMapping) !== false && $number > 0) {
            $key = key($this->numberMapping);

            if ($number < $key) {
                continue;
            }
            $multiple = 1;

            if ($key * 2 < $number && $key > 0) {
                $multiple = floor($number / $key);

                $final = array_merge($final, $this->parse($multiple));
            }

            $final[] = current($this->numberMapping);
            $number -= $key * $multiple;
        }

        return $final;
    }
}
