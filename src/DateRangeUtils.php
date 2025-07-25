<?php

namespace Ogzhncrt\DateRangeHelper;

use Ogzhncrt\DateRangeHelper\DateRange;

class DateRangeUtils
{
    public static function sortRangesByStart(array $ranges): array
    {
        usort($ranges, fn(DateRange $a, DateRange $b) => $a->getStart() <=> $b->getStart());
        return $ranges;
    }

    public static function mergeRanges(array $ranges): array
    {
        if (count($ranges) <= 1) {
            return $ranges;
        }

        $sorted = self::sortRangesByStart($ranges);
        $merged = [];
        $current = array_shift($sorted);

        foreach ($sorted as $next) {
            if ($current->overlaps($next) || $current->getEnd()->modify('+1 day') == $next->getStart()) {
                $current = DateRange::createFromObjects(
                    $current->getStart() < $next->getStart() ? $current->getStart() : $next->getStart(),
                    $current->getEnd() > $next->getEnd() ? $current->getEnd() : $next->getEnd()
                );
            } else {
                $merged[] = $current;
                $current = $next;
            }
        }

        $merged[] = $current;
        return $merged;
    }
}
