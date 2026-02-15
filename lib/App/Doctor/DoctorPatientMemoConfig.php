<?php

namespace App\Doctor;

class DoctorPatientMemoConfig
{
    /**
     * Returns the default configuration for doctor-patient memos.
     *
     * @param array $customConfig Additional configurations to merge with defaults.
     * @return array Merged configuration array.
     */
    public static function get(array $customConfig = []): array
    {
        $defaultConfig = [
            'TABLE' => 'pttest',
            'ALLOW_SORT' => true,
            'DEFAULT_SORT' => 'OrderDate',
            'COLS' => ['a0', 'a1', 'a2', 'a3', 'a4', 'Type', 'comment', 'title'],
            'LCOLS' => ['a0', 'a1', 'a2', 'a3', 'a4', 'Type', 'comment', 'title'],
            'DCOLS' => ['a0', 'a1', 'a2', 'a3', 'a4', 'Type', 'comment', 'title'],
            'ECOLS' => [
                ['Column' => 'Type', 'Label' => '分類', 'Draw' => 'enum', 'Enum' => []],
                ['Column' => 'OrderDate', 'Label' => '注文日', 'Draw' => 'date', 'Option' => ['validate' => 'nonnull,date']],
                ['Column' => 'comment', 'Label' => 'コメント', 'Draw' => 'textarea'],
            ]
        ];

        return array_merge($defaultConfig, $customConfig);
    }
}
