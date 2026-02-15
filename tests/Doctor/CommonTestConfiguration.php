<?php

namespace Tests\Doctor;

trait CommonTestConfiguration
{
    public function getTestConfig(): array
    {
        return [
            'TABLE' => 'pttest',
            'ALLOW_SORT' => true,
            'DEFAULT_SORT' => 'OrderDate',
            'COLS' => ['a0', 'a1', 'a2', 'Type', 'comment', 'title'],
            'ECOLS' => [
                ['Column' => 'Type', 'Label' => '分類', 'Draw' => 'enum', 'Enum' => []],
                ['Column' => 'comment', 'Label' => 'コメント', 'Draw' => 'textarea']
            ]
        ];
    }
}
