<?php

use PHPUnit\Framework\TestCase;

use FreeBusyCalculator\FreeBusyCalculator;
use FreeBusyCalculator\DatetimeRange;
use FreeBusyCalculator\DatetimeRangeGroup;

class FreeBusyCalculatorTest extends TestCase
{
    public function testConstruct()
    {
        $object = new FreeBusyCalculator();
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('busyRanges');
        $target->setAccessible(true);

        $this->assertEquals(new DatetimeRangeGroup(), $target->getValue($object));
    }

    /**
    * @dataProvider dataProviderAddBusyRange
    * @depends testConstruct
    */
    public function testAddBusyRange($a, $expectedA)
    {
        $object = new FreeBusyCalculator();
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod('addBusyRange');
        $method->setAccessible(true);
        $method->invokeArgs($object, [$a]);
        $target = $reflection->getProperty('busyRanges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderAddBusyRange()
    {
        return [
            [
                ['2019-01-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                (new DatetimeRangeGroup())->add(
                    DatetimeRange::fromTs(1546300800, 1554076799)
                ),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderAddBusyRanges
     * @depends testConstruct
     * @depends testAddBusyRange
     */
    public function testAddBusyRanges($a, $expectedA)
    {
        $object = (new FreeBusyCalculator())->addBusyRanges($a);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('busyRanges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderAddBusyRanges()
    {
        return [
            [
                [
                    ['2019-01-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                    ['2019-04-01T00:00:00+0000', '2019-06-30T23:59:59+0000'],
                ],
                (new DatetimeRangeGroup())->addMany([
                    DatetimeRange::fromTs(1546300800, 1554076799),
                    DatetimeRange::fromTs(1554076800, 1561939199),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetFreetime
     * @depends testConstruct
     * @depends testAddBusyRange
     * @depends testAddBusyRanges
     */
    public function testGetFreetime($a, $b, $expectedA)
    {
        $this->assertEquals(
            $expectedA,
            (new FreeBusyCalculator())->addBusyRanges($a)->getFreetime($b)
        );
    }

    public function dataProviderGetFreetime()
    {
        return [
            [
                [
                    ['2019-01-01T00:00:00+0000', '2019-01-31T23:59:59+0000'],
                    ['2019-03-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                ],
                ['2019-01-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                [DatetimeRange::fromTs(1548979199, 1551398400)],
            ],
            [
                [
                    ['2019-01-01T00:00:00+0900', '2019-01-01T01:00:00+0900'],
                    ['2019-02-01T00:00:00+0900', '2019-02-01T01:00:00+0900'],
                ],
                ['2019-01-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                [DatetimeRange::fromTs(1548979199, 1551398400)],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGetBusytime
     * @depends testConstruct
     * @depends testAddBusyRange
     * @depends testAddBusyRanges
     * @depends testGetFreetime
     */
    public function testGetBusytime($a, $b, $expectedA)
    {
        $this->assertEquals(
            $expectedA,
            (new FreeBusyCalculator())->addBusyRanges($a)->getBusytime($b)
        );
    }

    public function dataProviderGetBusytime()
    {
        return [
            [
                [
                    ['2019-01-01T00:00:00+0000', '2019-01-31T23:59:59+0000'],
                    ['2019-03-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                ],
                null,
                [
                    DatetimeRange::fromTs(1546300800, 1548979199),
                    DatetimeRange::fromTS(1551398400, 1554076799),
                ],
            ],
            [
                [
                    ['2019-01-01T00:00:00+0000', '2019-01-31T23:59:59+0000'],
                    ['2019-03-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                ],
                ['2019-01-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                [
                    DatetimeRange::fromTs(1546300800, 1548979199),
                    DatetimeRange::fromTS(1551398400, 1554076799),
                ],
            ],
            [
                [
                    ['2019-01-01T00:00:00+0000', '2019-01-31T23:59:59+0000'],
                    ['2019-03-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                ],
                ['2019-01-31T00:00:00+0000', '2019-03-01T23:59:59+0000'],
                [
                    DatetimeRange::fromTs(1548892800, 1548979199),
                    DatetimeRange::fromTs(1551398400, 1551484799),
                ],
            ],
            [
                [
                    ['2019-01-01T00:00:00+0000', '2019-01-31T23:59:59+0000'],
                    ['2019-03-01T00:00:00+0000', '2019-03-31T23:59:59+0000'],
                ],
                ['2019-02-01T00:00:00+0000', '2019-02-28T23:59:59+0000'],
                [],
            ],
        ];
    }
}
