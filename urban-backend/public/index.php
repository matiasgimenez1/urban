<?php
require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database;
use Slim\Factory\AppFactory;
use App\Middleware\CORS;  

// Cargar las variables de entorno desde .env
//$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
//$dotenv->load();

// Inicializar la base de datos
new Database();

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Paso 3: Manejar todas las solicitudes OPTIONS para permitir CORS
// index.php
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', 'https://urban-production-edbd.up.railway.app')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


// Registrar el middleware de CORS
$app->add(new CORS());

// Cargar las rutas de la aplicación
(require __DIR__ . '/../routes/routes.php')($app);

$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});

$app->run();
