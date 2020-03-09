<?php

namespace FreeBusyCalculator;

use Datetime;
use InvalidArgumentException;

class DatetimeRange
{
    protected $startTs;

    protected $endTs;

    protected $start;

    protected $end;

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }

        return null;
    }

    public static function fromString($start, $end, $format = DateTime::ISO8601)
    {
        if (strtotime($start) > strtotime($end)) {
            throw new InvalidArgumentException(
                'First datetime must be before or same as second datetime.'
            );
        }

        $newRange = new self();

        $newRange->startTs = strtotime($start);
        $newRange->endTs = strtotime($end);

        $newRange->start = (new Datetime($start))->format($format);
        $newRange->end = (new Datetime($end))->format($format);

        return $newRange;
    }

    public static function fromTs($start, $end, $format = DateTime::ISO8601)
    {
        if ($start > $end) {
            throw new InvalidArgumentException(
                'First datetime must be before or same as second datetime.'
            );
        }

        $newRange = new self();

        $newRange->startTs = $start;
        $newRange->endTs = $end;

        $newRange->start = (new Datetime())->setTimestamp($start)
            ->format($format);
        $newRange->end = (new Datetime())->setTimestamp($end)->format($format);

        return $newRange;
    }
}
