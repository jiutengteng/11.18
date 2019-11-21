<?php
    function MiddleCheck($value) {
        $array = [1,3,4,6,9,33,55,66,77,88,456];
        $count = 0;
        for( $i = 0; $i < count($array); $i ++ ) {
            $count ++;
            if( $value == $array[$i] ) {
                return $count;
            }
        }
        return $count;
    }

$value = 69;

echo MiddleCheck($value);
