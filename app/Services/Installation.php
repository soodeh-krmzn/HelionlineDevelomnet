<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class Installation
{
    public function migrate()
    {
        Artisan::call('migrate');
    }
}
