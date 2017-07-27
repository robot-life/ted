<?php

use PHPUnit\Framework\TestCase;
use App\Parsers\Filters\Hyperlink;
use App\Tweet;

class HyperlinkTest extends TestCase
{
    public function textDataProvider()
    {
        return [
            [
                "This is gonna be a major cleanup.",
                false,
            ],
            [
                "Why @jetstar do I need a reciept when I have an electronic boarding pass such a waste of a tree and just pointless in general https://t.co/PRBUCeHsvA",
                true,
            ],
            [
                "This magazine was a major influence on my career in IT, and introduced me to many concepts such as relational databa…https://t.co/SS5gTX3gpa",
                false,
            ],
        ];
    }

    /**
     * @dataProvider textDataProvider
     */
    public function test_filters_hyperlinks($text, $expected)
    {
        $tweet = new Tweet;
        $tweet->text = $text;

        $actual = (new Hyperlink)->filters($tweet);

        $this->assertSame($expected, $actual);
    }
}
