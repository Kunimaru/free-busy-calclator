<?php

namespace FreeBusyCalculator;

use InvalidArgumentException;

class FreeBusyCalculator
{
    protected $busyRanges;

    public function __construct()
    {
        $this->busyRanges = new DatetimeRangeGroup();
    }

    public function addBusyRanges($datetimeRanges)
    {
        if (! is_array($datetimeRanges)) {
            throw new InvalidArgumentException(
                "The argument must be nested array whose children have two datetime strings.\n"
            );
        }

        foreach ($datetimeRanges as &$datetimeRange) {
            $this->addBusyRange($datetimeRange);
        }

        return $this;
    }

    public function getFreetime($filter)
    {
        if (! isset($filter[0]) || ! isset($filter[1])) {
            throw new InvalidArgumentException(
                "The argument must be an array which has two datetime strings.\n"
            );
        }

        return (new DatetimeRangeGroup())
            ->add(DatetimeRange::fromString($filter[0], $filter[1]))
            ->subtractGroup($this->busyRanges)
            ->get();
    }

    public function getBusytime($filter = null)
    {
        if ($filter === null) {
            return $this->busyRanges->get();
        }

        if (! isset($filter[0]) || ! isset($filter[1])) {
            throw new InvalidArgumentException(
                "The argument must be an array which has two datetime strings.\n"
            );
        }

        $freetime = (new DatetimeRangeGroup())
            ->add(DatetimeRange::fromString($filter[0], $filter[1]))
            ->subtractGroup($this->busyRanges);
        return (new DatetimeRangeGroup())
            ->add(DatetimeRange::fromString($filter[0], $filter[1]))
            ->subtractGroup($freetime)
            ->get();
    }

    private function addBusyRange($datetimeRange)
    {
        if (! isset($datetimeRange[0]) || ! isset($datetimeRange[1])) {
            throw new InvalidArgumentException(
                "The argument must be an array which has two datetime strings.\n"
            );
        }

        $this->busyRanges->add(
            DatetimeRange::fromString($datetimeRange[0], $datetimeRange[1])
        );
    }
}
