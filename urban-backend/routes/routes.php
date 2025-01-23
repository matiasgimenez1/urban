<?php
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\ProductoController;
use App\Controllers\SolicitudController;
use App\Controllers\AgendamientoController;
use App\Controllers\ConsumicionController; 
// use App\Middleware\Token; 

return function ($app) {
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
     // Ruta para generar el token JWT
     $app->post('/api/auth/token', AuthController::class . ':generateToken');

    // Rutas para los usuarios
    $app->get('/api/usuarios', UserController::class . ':index');             // Obtener todos los usuarios
    $app->get('/api/usuarios/dropdown', UserController::class . ':dropdown');
    $app->get('/api/usuarios/{id}', UserController::class . ':show');             // Obtener todos los usuarios
    $app->post('/api/usuarios', UserController::class . ':create');    // Crear (alta) un nuevo usuario
    $app->put('/api/usuarios/{id}', UserController::class . ':update'); // Modificar un usuario existente
    $app->delete('/api/usuarios/{id}', UserController::class . ':delete'); // Eliminar (baja) un usuario
   
    // Rutas para productos
    $app->get('/api/productos', ProductoController::class . ':index');             // Obtener todos los productos
    $app->get('/api/productos/dropdown', ProductoController::class . ':dropdown');
    $app->get('/api/productos/{id}', ProductoController::class . ':show');             // Obtener todos los productos
    $app->post('/api/productos', ProductoController::class . ':create');    // Crear (alta) un nuevo producto
    $app->put('/api/productos/{id}', ProductoController::class . ':update'); // Modificar un producto existente
    $app->delete('/api/productos/{id}', ProductoController::class . ':delete'); // Eliminar (baja) un producto
 
     // Rutas para solicitudes (reservas)
     $app->get('/api/solicitudes', SolicitudController::class . ':index');      
     $app->get('/api/solicitudes/dropdown', SolicitudController::class . ':dropdown');       
     $app->get('/api/solicitudes/{id}', SolicitudController::class . ':show');          
     $app->post('/api/solicitudes', SolicitudController::class . ':create');     
     $app->put('/api/solicitudes/{id}', SolicitudController::class . ':update');  
     $app->delete('/api/solicitudes/{id}', SolicitudController::class . ':delete'); 

     // Rutas para partidos (agendamientos)
     $app->get('/api/agendamientos/maxid', AgendamientoController::class . ':getMaxId'); 
     $app->get('/api/agendamientos', AgendamientoController::class . ':index');
     $app->get('/api/agendamientos/{id}', AgendamientoController::class . ':show'); // Obtener un agendamiento específico
     $app->post('/api/agendamientos', AgendamientoController::class . ':create'); // Crear un nuevo agendamiento
     $app->put('/api/agendamientos/{id}', AgendamientoController::class . ':update'); // Actualizar un agendamiento existente
     $app->delete('/api/agendamientos/{id}', AgendamientoController::class . ':delete'); // Eliminar un agendamiento

       // Rutas para consumicion (reservas)
       $app->get('/api/consumiciones', ConsumicionController::class . ':index');             
       $app->get('/api/consumiciones/{id_agendamiento}/{item}', ConsumicionController::class . ':show');          
       $app->post('/api/consumiciones', ConsumicionController::class . ':create');     
       $app->put('/api/consumiciones/{id}', ConsumicionController::class . ':update');  
       $app->delete('/api/consumiciones/{id}', ConsumicionController::class . ':delete'); 


     $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
         $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
         return $handler($req, $res);
     });
       
      
};
