<?php

function convertToNumber($formatterNumber) {
    $number = str_replace(',', '', $formatterNumber);

    return $number;
}
