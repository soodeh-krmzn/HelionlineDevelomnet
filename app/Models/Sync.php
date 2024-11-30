<?php

namespace App\Models;

use App\Models\MyModels\Main;

class Sync extends Main
{
    protected $fillable = ['m_uuid', 'm_id', 'model', 'status'];
    protected $table = "sync";

    public static function logSync(array $data)
    {
        self::updateOrCreate(
            [
                'model' => $data['model'],
                'm_id' => $data['m_id'],
            ],
            [
                'm_uuid' => $data['m_uuid'] ?? null,
                'status' => $data['status'],
            ]
        );
    }
}
