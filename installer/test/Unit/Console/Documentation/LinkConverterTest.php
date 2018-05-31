<?php declare(strict_types=1);

namespace Cocotte\Test\Unit\Console\Documentation;

use Cocotte\Console\Documentation\LinkConverter;
use PHPUnit\Framework\TestCase;

final class LinkConverterTest extends TestCase
{

    public function test_it_converts_multiple_links()
    {
        $text = <<<EOF
Lorem Ipsum https://cocotte.rocks/installer/docs/console.html
Dolor sit amet https://goo.gl/SJnw2c
EOF;

        $expected = <<<EOF
Lorem Ipsum [https://cocotte.rocks/installer/docs/console.html](https://cocotte.rocks/installer/docs/console.html)
Dolor sit amet [https://goo.gl/SJnw2c](https://goo.gl/SJnw2c)
EOF;

        $converter = new LinkConverter();
        self::assertSame($expected, $converter->convert($text));
    }

    public function test_it_does_not_convert_converted()
    {
        $text = <<<EOF
Lorem Ipsum [https://cocotte.rocks/installer/docs/console.html](https://cocotte.rocks/installer/docs/console.html)
Dolor sit amet https://goo.gl/SJnw2c
EOF;

        $expected = <<<EOF
Lorem Ipsum [https://cocotte.rocks/installer/docs/console.html](https://cocotte.rocks/installer/docs/console.html)
Dolor sit amet [https://goo.gl/SJnw2c](https://goo.gl/SJnw2c)
EOF;

        $converter = new LinkConverter();
        self::assertSame($expected, $converter->convert($text));
    }
}
