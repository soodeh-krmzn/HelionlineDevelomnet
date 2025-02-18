<?php

namespace App\Services;

use PDO;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class Database
{
    protected $db_name;
    protected $db_user;
    protected $db_pass;

    public function __construct($db_name = null, $db_user = null, $db_pass = null)
    {
        $this->db_name = $db_name ?? session('db_name');
        $this->db_user = $db_user ?? session('db_user');
        $this->db_pass = $db_pass ?? session('db_pass');
    }

    public function decrypt()
    {
        $key = base64_decode(Config::get('app.custom_key'));
        $encrypter = new Encrypter($key, Config::get('app.cipher'));
        $name = $encrypter->decryptString($this->db_name);
        $user = $encrypter->decryptString($this->db_user);
        $pass = $encrypter->decryptString($this->db_pass);

        return compact('name', 'user', 'pass');
    }

    public function connect()
    {
        // dd($this->db_pass,$this->db_name,$this->db_user);

        if ($this->db_name == "" || $this->db_user == "" || $this->db_pass == "") {
            abort(500, "خطای پیکربندیt! لطفا با پشتیبان سیستم تماس بگیرید.");
        }

        $decrypted = $this->decrypt();
        DB::purge('mysql');
        Config::set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => env('DB_HOST'),
            'database' => $decrypted['name'],
            'username' => $decrypted['user'],
            'password' => $decrypted['pass'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => false,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ]);
        //dd( config('database.connections.mysql'));
        try {
            DB::connection()->getPdo();
            if (DB::connection()->getDatabaseName()) {
                return 'ok';
            } else {
                abort(500, "خطای پیکربندی! لطفا با پشتیبان سیستم تماس بگیرید.");
            }
        } catch (\Exception $e) {
            abort(500, "خطای پیکربندی! لطفا با پشتیبان سیستم تماس بگیرید. " . $e->getMessage());
        }
    }

    public function getTables()
    {
        $tables = DB::select('SHOW TABLES');
        if (!(count($tables) > 0)) {
            Artisan::call('migrate --seed');
        }
    }

    public function dbName()
    {
        return $this->decrypt()['name'];
    }
}
