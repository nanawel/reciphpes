<?php

namespace App\Service;

class Transliterator
{
    protected static ?\Transliterator $sortNameTransliterator = null;

    public static function sortNameTransliterator(): \Transliterator
    {
        if (!self::$sortNameTransliterator instanceof \Transliterator) {
            self::$sortNameTransliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC');
        }

        return self::$sortNameTransliterator;
    }
}
