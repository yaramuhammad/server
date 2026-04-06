<?php

namespace App\Services;

use ArPHP\I18N\Arabic;

class ArabicTextService
{
    private Arabic $arabic;

    public function __construct()
    {
        $this->arabic = new Arabic();
    }

    /**
     * Shape Arabic text for correct rendering in DomPDF.
     * Returns the original text if it contains no Arabic characters.
     */
    public function shape(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        // Check if text contains Arabic characters
        if (!preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $text)) {
            return $text;
        }

        return $this->arabic->utf8Glyphs($text);
    }
}
