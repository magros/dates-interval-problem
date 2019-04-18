<?php

class DateIntervalCalculator
{
    private $intervals = [];

    /**
     * This method fills empty indexes with -1 to work with second case
     * @param $ranges
     * @return array
     */
    private function fillEmptySpaces($ranges)
    {
        $lastKey = key(array_slice($ranges, -1, 1, TRUE));

        for ($i = 1; $i <= $lastKey; $i++) {
            if (!isset($ranges[$i])) {
                $ranges[$i] = -1;
            }
        }

        ksort($ranges);

        return $ranges;
    }

    /**
     * This method will add a new record to the intervals array and recalculates new intervals
     * @param $dateStart
     * @param $dateEnd
     * @param $price
     * @return array
     */
    public function addInterval($dateStart, $dateEnd, $price)
    {
        $ranges = $this->getCurrentRanges();

        for ($i = $dateStart; $i <= $dateEnd; $i++) {
            $ranges[$i] = $price;
        }

        $ranges = $this->fillEmptySpaces($ranges);

        $start = 1;
        $newIntervals = [];

        foreach ($ranges as $index => $value) {
            if ($value == -1) {
                $start++;
                continue;
            }
            if ($index + 1 > count($ranges)) {
                $newIntervals[] = ['date_start' => $start, 'date_end' => $index, 'price' => $value];
            } else if ($value !== $ranges[$index + 1]) {
                $newIntervals[] = ['date_start' => $start, 'date_end' => $index, 'price' => $value];
                $start = $index + 1;
            }
        }

        $this->intervals = $newIntervals;

        return $this->intervals;
    }

    /**
     * Converts current intervals to an array with ranges
     * @return array
     */
    private function getCurrentRanges()
    {
        $array = [];
        $currentRecords = $this->intervals;

        foreach ($currentRecords as $record) {
            for ($i = $record['date_start']; $i <= $record['date_end']; $i++) {
                $array[$i] = $record['price'];
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getIntervals()
    {
        return $this->intervals;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = "";
        foreach ($this->intervals as $interval) {
            $string .= "({$interval['date_start']}-{$interval['date_end']}:{$interval['price']}), ";
        }
        return rtrim($string, ', ');
    }

    /**
     *
     */
    public function saveToDatabase()
    {
        $this->clearTable();

        foreach ($this->intervals as $interval) {
            //TODO: you need to store it in database
            // In this point you have an array with next structure: ['date_start' => 1, 'date_end' => 10, 'price' => 50]
            // Query: INSERT INTO Intervals value(null, $interval['date_start'], $interval['date_end'], $interval['price'])
        }

    }

    public function clearTable()
    {
        //TODO: Clear all current records on intervals table
        // Query: DELETE FROM Intervals
    }

    /**
     *
     */
    public function readFromDatabase()
    {
        //TODO: you need to read all current intervals from database
        // Query: SELECT * from Interval
        // You have to map results to the intervals variable:
        // $this->intervals = array_map( function($result) { return ['date_start' => $result->date_start, 'date_end' => $result->date_end, 'price' => $result->price]; }, $queryResults);
    }

}