<?php

namespace Povils\PHPMND;

use NumberFormatter;

abstract class Language
{
    /**
     * @var NumberFormatter
     */
    protected $formatter;

    public function __construct($language)
    {
        $this->formatter = new NumberFormatter($language, NumberFormatter::SPELLOUT);
    }

    /*
     * Returns an array of words which
     */
    public function parse(int $number): array {
        $words = $this->specialNumbers()[$number] ?? [];
        $formatted = $this->formatter->format($number);

        $formatted = str_replace(['-', ','], ' ', $formatted);
        $formatted = explode(' ', $formatted);

        return array_merge($words, $formatted);
    }

    abstract public static function providesLanguages(): array;

    abstract public function specialNumbers(): array;
}
