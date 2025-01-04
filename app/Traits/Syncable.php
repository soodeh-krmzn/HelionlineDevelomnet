<?php

namespace App\Traits;

use App\Models\Sync;
use Illuminate\Support\Str;

trait Syncable
{
    protected static $syncing = true;
    public static function disableSyncLog()
    {
        self::$syncing = false;
    }
    public static function bootSyncable()
    {
        static::created(function ($model) {
            if (self::$syncing) {
                $model->syncLog(true);
            }
        });

        static::updated(function ($model) {
            if (self::$syncing) {
                $model->syncLog(false);
            }
        });
    }

    public function syncLog($isCreating)
    {
        if (get_class($this) === 'App\Models\Game' && $this->status == 0) {
            return;
        }

        if (get_class($this) === 'App\Models\GameMeta' && is_null($this->end)) {
            return;
        }

        if (get_class($this) === 'App\Models\Factor' && $this->closed == 0) {
            return;
        }
        
        if (get_class($this) === 'App\Models\CounterItem' && $this->end == 0) {
            return;
        }

        $syncData = [
            'model' => get_class($this),
            'is_created' => $isCreating ? 1 : 0,
            'm_id' => $this->id,
            'status' => '0',
            'user_id' => auth()->id(),
        ];

        if (array_key_exists('uuid', $this->attributes)) {
            $syncData['m_uuid'] = $this->uuid;
        }

        Sync::logSync($syncData);
    }

    public static function withoutSyncing(callable $callback)
    {
        self::$syncing = false;
        try {
            $callback();
        } finally {
            self::$syncing = true;
        }
    }
}