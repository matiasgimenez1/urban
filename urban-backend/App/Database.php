<?php
namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
    public function __construct() {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'pgsql',
            'host' => 'autorack.proxy.rlwy.net',
            'port' => '47478',                   
            'database' => 'railway',
            'username' => 'postgres',
            'password' => 'EilJhYIdChoIIxhJKbIgqONEzCrCxDPw',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
