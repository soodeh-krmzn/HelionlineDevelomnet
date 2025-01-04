<?php

namespace App\Models;

use App\Models\MyModels\Main;

class Sync extends Main
{
    protected $fillable = ['m_uuid', 'm_id', 'model', 'status', 'is_created', 'user_id'];
    protected $table = "sync";

    public static function logSync(array $data)
    {
        // self::firstOrCreate(
        //     [
        //         'model' => $data['model'],
        //         'm_id' => $data['m_id'],
        //     ],
        //     [
        //         'm_uuid' => $data['m_uuid'] ?? null,
        //         'status' => $data['status'],
        //     ]
        // );
        self::create($data);
    }
}
