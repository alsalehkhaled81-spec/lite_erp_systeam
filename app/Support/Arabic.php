<?php

namespace App\Support;

use Arphp\Glyphs;

class Arabic
{
    protected static ?Glyphs $instance = null;

    public static function glyph(): Glyphs
    {
        return static::$instance ??= new Glyphs();
    }

    public static function shape(string $text): string
    {
        if ($text === '') {
            return '';
        }

        return static::glyph()->utf8Glyphs($text);
    }
}
