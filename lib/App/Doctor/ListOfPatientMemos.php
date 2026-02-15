<?php
namespace App\Doctor;

use App\Base\ListOfPPAObjects;

/**
 * Class for managing a list of patient memos.
 */
class ListOfPatientMemos extends ListOfPPAObjects
{
    public function __construct(string $prefix, array $config = [])
    {
        $config = DoctorPatientMemoConfig::get($config);
        parent::__construct($prefix, $config);
    }
}
