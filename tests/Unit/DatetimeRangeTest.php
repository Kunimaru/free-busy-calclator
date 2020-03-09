<?php

use PHPUnit\Framework\TestCase;

use FreeBusyCalculator\DatetimeRange;

class DatetimeRangeTest extends TestCase
{
    /**
     * @dataProvider dataProviderFromString
     */
    public function testFromString($a, $b, $expectedA, $expectedB, $expectedC, $expectedD)
    {
        $range = DatetimeRange::fromString($a, $b);

        $this->assertEquals($expectedA, $range->startTs);
        $this->assertEquals($expectedB, $range->endTs);
        $this->assertEquals($expectedC, $range->start);
        $this->assertEquals($expectedD, $range->end);
    }

    public function dataProviderFromString()
    {
        return [
            [
                '2019-01-01T00:00:00+0000',
                '2019-03-31T23:59:59+0000',
                1546300800,
                1554076799,
                '2019-01-01T00:00:00+0000',
                '2019-03-31T23:59:59+0000',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderFromTs
     */
    public function testFromTs($a, $b, $expectedA, $expectedB, $expectedC, $expectedD)
    {
        $range = DatetimeRange::fromTs($a, $b);

        $this->assertEquals($expectedA, $range->startTs);
        $this->assertEquals($expectedB, $range->endTs);
        $this->assertEquals($expectedC, $range->start);
        $this->assertEquals($expectedD, $range->end);
    }

    public function dataProviderFromTs()
    {
        return [
            [
                1546300800,
                1554076799,
                1546300800,
                1554076799,
                '2019-01-01T00:00:00+0000',
                '2019-03-31T23:59:59+0000',
            ],
        ];
    }
}
