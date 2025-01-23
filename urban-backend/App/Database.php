<?php
namespace App;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {
   // public function __construct() {
     public function  __construct($username, $password) {  //matias
    $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'pgsql',
            //'host' => 'localhost',
            //'database' => 'paintball',
            'host' => 'autorack.proxy.rlwy.net',
            'port' => '47478',                    
            'database' => 'railway',
            //'username' => 'postgres',
            //'password' => '123',
            //'password' => 'EilJhYIdChoIIxhJKbIgqONEzCrCxDPw',
            'username' => $username, // Usuario proporcionado desde el front matias
            'password' => $password, // Contraseña proporcionada desde el front matias                    
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
