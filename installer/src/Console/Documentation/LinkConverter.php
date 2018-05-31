<?php declare(strict_types=1);

namespace Cocotte\Console\Documentation;

final class LinkConverter
{

    /**
     * https://stackoverflow.com/a/10002262/1476197
     * And I added a look behind assertion to not match already converted urls.
     */
    const PATTERN = '#(?<!\[|\()(?xi)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|'.
    '\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#i';

    public function convert($string): string
    {
        return preg_replace_callback(
            self::PATTERN,
            $this->callback(),
            $string
        );
    }

    private function callback(): \Closure
    {
        return function ($matches) {
            $input = $matches[0];

            return "[$input]($input)";
        };
    }
}