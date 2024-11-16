<?php

namespace App\Models\MyModels;

use App\Http\Controllers\PaymentController;
use App\Services\Database;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Main extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $db = new Database();
        $db->connect();
    }

}
