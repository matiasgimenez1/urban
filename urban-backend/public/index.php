<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database;
use Slim\Factory\AppFactory;
use App\Middleware\CORS;  

// Cargar las variables de entorno desde .env
// $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
// $dotenv->load();

// Inicializar la base de datos
new Database();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Paso 3: Manejar todas las solicitudes OPTIONS para permitir CORS
// index.php

// $app->options('/{routes:.+}', function ($request, $response) {
//     return $response
//         //->withHeader('Access-Control-Allow-Origin', 'http://localhost:5173') // Cambia este valor según el origen de tu frontend
//         ->withHeader('Access-Control-Allow-Origin', 'http://urban-production-edbd.up.railway.app') 
//         ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//         ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With') // No incluyas 'Access-Control-Allow-Origin' aquí
//         ->withHeader('Access-Control-Allow-Credentials', 'true');
// });


// Registrar el middleware de CORS
// $app->add(new CORS());

// Cargar las rutas de la aplicación
(require __DIR__ . '/../routes/routes.php')($app);

$app->run();
