<?php

class DateIntervalCalculatorWithDates
{
    private $intervals = [];

    /**
     * This method fills empty indexes with -1 to work with second case
     * @param $ranges
     * @return array
     */
    private function fillEmptySpaces($ranges)
    {
        ksort($ranges);

        $last = key(array_slice($ranges, -1, 1, TRUE));
        $first = key(array_slice($ranges, 0, 1, TRUE));

        while (true) {
            $first = date('Y/m/d', strtotime($first . ' +1 day'));

            if ($first == $last) {
                break;
            }
            if (!isset($ranges[$first])) {
                $ranges[$first] = -1;
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

        while (true) {
            $ranges[$dateStart] = $price;

            $dateStart = date('Y/m/d', strtotime($dateStart . ' +1 day'));

            if ($dateStart == $dateEnd) {
                $ranges[$dateStart] = $price;
                break;
            }
        }

        $ranges = $this->fillEmptySpaces($ranges);

        $start = key(array_slice($ranges, 0, 1, TRUE));
        $last = key(array_slice($ranges, -1, 1, TRUE));

        $newIntervals = [];

        foreach ($ranges as $index => $value) {
            if ($value == -1) {
                $start = date('Y/m/d', strtotime($start . ' +1 day'));
                continue;
            }
            if ($index == $last) {
                $newIntervals[] = ['date_start' => $start, 'date_end' => $index, 'price' => $value];
            } else if (isset($ranges[date('Y/m/d', strtotime($index . ' +1 day'))]) && $value !== $ranges[date('Y/m/d', strtotime($index . ' +1 day'))]) {
                $newIntervals[] = ['date_start' => $start, 'date_end' => $index, 'price' => $value];
                $start = date('Y/m/d', strtotime($index . ' +1 day'));
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

            $dateStart = date($record['date_start']);
            $dateEnd = date($record['date_end']);

            while (true) {

                $array[$dateStart] = $record['price'];

                $dateStart = date('Y/m/d', strtotime($dateStart . ' +1 day'));

                if ($dateStart == $dateEnd) {

                    $array[$dateStart] = $record['price'];

                    break;
                }
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
            // In this point you have an array with next structure: ['date_start' => '2018/12/27', 'date_end' => '2018/01/05', 'price' => 50]
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