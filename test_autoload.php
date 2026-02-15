<?php

require_once 'vendor/autoload.php';

use App\Doctor\ListOfPatientMemos;

if (class_exists('App\Doctor\ListOfPatientMemos')) {
    echo "Class ListOfPatientMemos loaded successfully.\n";
} else {
    echo "Class ListOfPatientMemos not found.\n";
}
