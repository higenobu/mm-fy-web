<?php

require 'vendor/autoload.php';

use App\Models\PatientMemo;

try {
    // Test Insert
    PatientMemo::create("Test Memo", "This is a test memo comment.");
    echo "Insert successful.\n";

    // Test Update
    PatientMemo::update(1, "Updated Test Memo", "This is an updated comment.");
    echo "Update successful.\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
