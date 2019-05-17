<?php
namespace Povils\PHPMND;

class GenericLanguage extends Language
{
    public static function providesLanguages(): array
    {
        return [
            'all'
        ];
    }

    public function specialNumbers(): array
    {
        return [];
    }
}
