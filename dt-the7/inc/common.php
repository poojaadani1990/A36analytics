<?php

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}

function searchForId($id, $array) {
    foreach ($array as $key => $val) {
        if ($val['PLAN_YEAR'] == $id) {
            return $key;
        }
    }
    return null;
}
