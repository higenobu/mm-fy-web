<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Doctor\ListOfPatientMemos;

try {
    $prefix = 'memo_';
    $config = [
        'TABLE' => 'pttest',
        'Patient_ObjectID' => '12345'
    ];

    $list = new ListOfPatientMemos($prefix, $config);
    echo "ListOfPatientMemos initialized successfully.\n";
    print_r($list->getConfig());
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
