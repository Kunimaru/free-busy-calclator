<?php

namespace FreeBusyCalculator;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;

class DatetimeRangeGroup
{
    protected $ranges;

    public function __construct()
    {
        $this->ranges = [];
    }

    public function add(DatetimeRange $newRange = null)
    {
        if ($newRange === null) {
            return $this;
        }

        $rangesNumber = count($this->ranges);
        if ($rangesNumber <= 0) {
            $this->ranges[] = [$newRange->startTs, $newRange->endTs];

            return $this;
        }

        foreach ($this->ranges as $index => &$range) {
            // Terminate
            if ($newRange->endTs < $range[0]) {
                $insertPosition = $index;

                break;
            }

            // New range includes old one
            if ($newRange->startTs <= $range[0]
                && $range[1] <= $newRange->endTs
            ) {
                $overlapExists = true;
                unset($this->ranges[$index]);
                $insertPosition = $index;

                continue;
            }

            // New range start overlaps old range
            if ($range[0] <= $newRange->startTs
                && $newRange->startTs <= $range[1]
            ) {
                $overlapExists = true;
                $start = $range[0];
                unset($this->ranges[$index]);
                $insertPosition = $index;
            }

            // New range end overlaps old range
            if ($range[0] <= $newRange->endTs
                && $newRange->endTs <= $range[1]
            ) {
                $overlapExists = true;
                $end = $range[1];
                unset($this->ranges[$index]);
                $insertPosition = $index;

                break;
            }
        }

        if ($overlapExists ?? false) {
            $this->ranges[$insertPosition] = [
                $start ?? $newRange->startTs,
                $end ?? $newRange->endTs,
            ];

            $this->ranges = array_values($this->ranges);

            return $this;
        }

        array_splice(
            $this->ranges,
            $insertPosition ?? $rangesNumber,
            0,
            [[$newRange->startTs, $newRange->endTs]]
        );

        return $this;
    }

    public function addMany(array $datetimeRanges)
    {
        foreach ($datetimeRanges as &$range) {
            if (! $range instanceof DatetimeRange) {
                throw new InvalidArgumentException(
                    "The argument must be an array of DatetimeRange.\n"
                );
            }

            $this->add($range);
        }

        return $this;
    }

    public function addGroup(DatetimeRangeGroup $newRangeGroup)
    {
        $newRanges = $newRangeGroup->get();
        $this->addMany($newRanges);

        return $this;
    }

    public function subtract(DatetimeRange $deleteRange = null)
    {
        if ($deleteRange === null) {
            return $this;
        }

        $rangesNumber = count($this->ranges);
        if ($rangesNumber <= 0) {
            return $this;
        }

        foreach ($this->ranges as $index => &$range) {
            // Terminate
            if ($deleteRange->endTs < $range[0]) {
                break;
            }

            // Old range includes delete one
            if ($range[0] <= $deleteRange->startTs
                && $deleteRange->endTs <= $range[1]
            ) {
                $overlapExists = true;
                if ($deleteRange->startTs === $range[0]
                    && $deleteRange->endTs === $range[1]) {
                    unset($this->ranges[$index]);

                    break;
                } elseif ($deleteRange->startTs === $range[0]) {
                    array_splice(
                        $this->ranges,
                        $index,
                        1,
                        [
                            [$deleteRange->endTs, $range[1]],
                        ]
                    );

                    break;
                } elseif ($deleteRange->endTs === $range[1]) {
                    array_splice(
                        $this->ranges,
                        $index,
                        1,
                        [
                            [$range[0], $deleteRange->startTs],
                        ]
                    );

                    break;
                }
                array_splice(
                    $this->ranges,
                    $index,
                    1,
                    [
                        [$range[0], $deleteRange->startTs],
                        [$deleteRange->endTs, $range[1]],
                    ]
                );

                break;
            }

            // Delete range includes old one
            if ($deleteRange->startTs <= $range[0]
                && $range[1] <= $deleteRange->endTs
            ) {
                $overlapExists = true;
                unset($this->ranges[$index]);

                continue;
            }

            // Delete range start overlaps old range
            if ($range[0] <= $deleteRange->startTs
                && $deleteRange->startTs <= $range[1]
            ) {
                $overlapExists = true;
                $this->ranges[$index] = [
                    $range[0], $deleteRange->startTs
                ];
            }

            // Delete range end overlaps old range
            if ($range[0] <= $deleteRange->endTs
                && $deleteRange->endTs <= $range[1]
            ) {
                $overlapExists = true;
                $this->ranges[$index] = [$deleteRange->endTs, $range[1]];

                break;
            }
        }

        if ($overlapExists ?? false) {
            $this->ranges = array_values($this->ranges);
        }

        return $this;
    }

    public function subtractMany(array $datetimeRanges)
    {
        foreach ($datetimeRanges as &$range) {
            if (! $range instanceof DatetimeRange) {
                throw new InvalidArgumentException(
                    "The argument must be an array of DatetimeRange.\n"
                );
            }

            $this->subtract($range);
        }

        return $this;
    }

    public function subtractGroup(DatetimeRangeGroup $deleteRangeGroup)
    {
        $deleteRanges = $deleteRangeGroup->get();
        $this->subtractMany($deleteRanges);

        return $this;
    }

    public function get($format = DateTime::ISO8601)
    {
        $datetimeRanges = [];
        foreach ($this->ranges as &$range) {
            $datetimeRanges[] = DatetimeRange::fromTs($range[0], $range[1], $format);
        }

        return $datetimeRanges;
    }

    public function toArray()
    {
        return $this->ranges;
    }
}
