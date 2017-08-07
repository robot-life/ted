<?php

use PHPUnit\Framework\TestCase;
use App\Parsers\Hydrators\Empath;

class EmpathTest extends TestCase
{
    public function textDataProvider()
    {
        return [
            [
                "I hate this api so much!",
                -0.275,
            ],
        ];
    }

    /**
     * @dataProvider textDataProvider
     */
    public function test_assesses_sentiment($text, $expected)
    {
        $actual = (new Empath)->query($text);

        $this->assertSame($expected, $actual);
    }
}
