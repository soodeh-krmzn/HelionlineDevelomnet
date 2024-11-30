<?php

namespace App\Traits;

use App\Models\Sync;
use Illuminate\Support\Str;

trait Syncable
{
    protected static $syncing = false;

    public static function bootSyncable()
    {
        static::created(function ($model) {
            if (!self::$syncing) {
                $model->syncLog();
            }
        });

        static::updated(function ($model) {
            if (!self::$syncing) {
                $model->syncLog();
            }
        });
    }

    public function syncLog()
    {
        if (get_class($this) === 'App\Models\Game' && $this->status == 0) {
            return;
        }

        if (get_class($this) === 'App\Models\GameMeta' && is_null($this->end)) {
            return;
        }
        
        $syncData = [
            'model' => get_class($this),
            'm_id' => $this->id,
            'status' => '0',
        ];
        if (array_key_exists('uuid', $this->attributes)) {
            $syncData['m_uuid'] = $this->uuid;
        }
        Sync::logSync($syncData);
    }

    public static function withoutSyncing(callable $callback)
    {
        self::$syncing = true;
        try {
            $callback();
        } finally {
            self::$syncing = false;
        }
    }
}
