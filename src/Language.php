<?php

namespace Povils\PHPMND;


interface Language
{
    /*
     * Returns an array of words which
     */
    public function parse(int $number): array;
}