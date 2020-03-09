<?php

use PHPUnit\Framework\TestCase;

use FreeBusyCalculator\DatetimeRange;
use FreeBusyCalculator\DatetimeRangeGroup;

class DatetimeRangeGroupTest extends TestCase
{
    public function testConstruct()
    {
        $object = new DatetimeRangeGroup();
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals([], $target->getValue($object));
    }

    /**
     * @dataProvider dataProviderAdd
     * @depends testConstruct
     */
    public function testAdd($a, $b, $expectedA)
    {
        $object = (new DatetimeRangeGroup())->add($a)->add($b);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderAdd()
    {
        return [
            [
                null,
                null,
                [],
            ],
            [
                null,
                DatetimeRange::fromTs(1, 4),
                [[1, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                null,
                [[1, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(2, 3),
                [[1, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(3, 5),
                [[1, 5]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(0, 2),
                [[0, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(0, 5),
                [[0, 5]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(5, 6),
                [[1, 4], [5, 6]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderAddMany
     * @depends testConstruct
     * @depends testAdd
     */
    public function testAddMany($a, $b, $expectedA)
    {
        $object = (new DatetimeRangeGroup())->add($a)->addMany($b);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderAddMany()
    {
        return [
            [
                null,
                [
                    DatetimeRange::fromTs(1, 4),
                    DatetimeRange::fromTs(5, 6),
                ],
                [[1, 4], [5, 6]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                [
                    DatetimeRange::fromTs(0, 1),
                    DatetimeRange::fromTs(4, 5),
                ],
                [[0, 5]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderAddGroup
     * @depends testConstruct
     * @depends testAdd
     */
    public function testAddGroup($a, $b, $expectedA)
    {
        $object = (new DatetimeRangeGroup())->add($a)->addGroup($b);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderAddGroup()
    {
        return [
            [
                null,
                new DatetimeRangeGroup(),
                [],
            ],
            [
                null,
                (new DatetimeRangeGroup())->add(
                    DatetimeRange::fromTs(1, 4)
                ),
                [[1, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                new DatetimeRangeGroup(),
                [[1, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                (new DatetimeRangeGroup())->add(
                    DatetimeRange::fromTs(1, 4)
                ),
                [[1, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                (new DatetimeRangeGroup())->add(
                    DatetimeRange::fromTs(5, 6)
                ),
                [[1, 4], [5, 6]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSubtract
     * @depends testConstruct
     * @depends testAdd
     */
    public function testSubtract($a, $b, $expectedA)
    {
        $object = (new DatetimeRangeGroup())->add($a)->subtract($b);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderSubtract()
    {
        return [
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(1, 4),
                [],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(2, 3),
                [[1, 2], [3, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(3, 5),
                [[1, 3]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(0, 2),
                [[2, 4]],
            ],
            [
                DatetimeRange::fromTs(1, 4),
                DatetimeRange::fromTs(0, 5),
                [],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSubtractMany
     * @depends testConstruct
     * @depends testAdd
     * @depends testSubtract
     */
    public function testSubtractMany($a, $b, $expectedA)
    {
        $object = (new DatetimeRangeGroup())->add($a)->subtractMany($b);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderSubtractMany()
    {
        return [
            [
                DatetimeRange::fromTs(1, 4),
                [
                    DatetimeRange::fromTs(0, 2),
                    DatetimeRange::fromTs(3, 5),
                ],
                [[2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderSubtractGroup
     * @depends testConstruct
     * @depends testAdd
     * @depends testSubtract
     */
    public function testSubtractGroup($a, $b, $expectedA)
    {
        $object = (new DatetimeRangeGroup())->addMany($a)->subtractGroup($b);
        $reflection = new ReflectionClass($object);
        $target = $reflection->getProperty('ranges');
        $target->setAccessible(true);

        $this->assertEquals($expectedA, $target->getValue($object));
    }

    public function dataProviderSubtractGroup()
    {
        return [
            [
                [DatetimeRange::fromTs(1, 4)],
                (new DatetimeRangeGroup())->add(
                    DatetimeRange::fromTs(2, 3)
                ),
                [[1, 2], [3, 4]],
            ],
            [
                [DatetimeRange::fromTs(0, 5)],
                (new DatetimeRangeGroup())->addMany([
                    DatetimeRange::fromTs(0, 1),
                    DatetimeRange::fromTs(4, 5),
                ]),
                [[1, 4]],
            ],
            [
                [
                    DatetimeRange::fromTs(0, 1),
                    DatetimeRange::fromTs(2, 4),
                    DatetimeRange::fromTs(5, 6),
                ],
                (new DatetimeRangeGroup())->addMany([
                    DatetimeRange::fromTs(3, 6),
                    DatetimeRange::fromTs(7, 8),
                ]),
                [[0, 1], [2, 3]],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderGet
     * @depends testConstruct
     * @depends testAdd
     * @depends testAddMany
     */
    public function testGet($a, $expectedA)
    {
        $methodName = is_array($a) ? 'addMany' : 'add';
        $this->assertEquals(
            $expectedA,
            (new DatetimeRangeGroup())->$methodName($a)->get()
        );
    }

    public function dataProviderGet()
    {
        return [
            [null, []],
            [
                DatetimeRange::fromTs(1, 4),
                [DatetimeRange::fromTs(1, 4)],
            ],
            [
                [
                    DatetimeRange::fromTs(1, 4),
                    DatetimeRange::fromTs(5, 6),
                ],
                [
                    DatetimeRange::fromTs(1, 4),
                    DatetimeRange::fromTs(5, 6),
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderToArray
     * @depends testConstruct
     * @depends testAdd
     * @depends testAddMany
     */
    public function testToArray($a, $expectedA)
    {
        $methodName = is_array($a) ? 'addMany' : 'add';
        $this->assertEquals(
            $expectedA,
            (new DatetimeRangeGroup())->$methodName($a)->toArray()
        );
    }

    public function dataProviderToArray()
    {
        return [
            [null, []],
            [
                DatetimeRange::fromTs(1, 4),
                [[1, 4]],
            ],
            [
                [
                    DatetimeRange::fromTs(1, 4),
                    DatetimeRange::fromTs(5, 6),
                ],
                [[1, 4], [5, 6]],
            ],
        ];
    }
}
