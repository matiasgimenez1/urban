<?php
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\ProductoController;
use App\Controllers\SolicitudController;
use App\Controllers\AgendamientoController;
use App\Controllers\ConsumisionController;

return function ($app) {
     // Ruta para generar el token JWT
     $app->post('/api/auth/token', AuthController::class . ':generateToken');

    // Rutas para los usuarios
    $app->get('/users', UserController::class . ':index');

    // Rutas para productos
    $app->get('/api/productos', ProductoController::class . ':index');             // Obtener todos los productos
    $app->get('/api/productos/dropdown', ProductoController::class . ':dropdown');
    $app->get('/api/productos/{id}', ProductoController::class . ':show');             // Obtener todos los productos
    $app->post('/api/productos', ProductoController::class . ':create');    // Crear (alta) un nuevo producto
    $app->put('/api/productos/{id}', ProductoController::class . ':update'); // Modificar un producto existente
    $app->delete('/api/productos/{id}', ProductoController::class . ':delete'); // Eliminar (baja) un producto
 
     // Rutas para solicitudes (reservas)
     $app->get('/api/solicitudes', SolicitudController::class . ':index');             
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
       $app->get('/api/consumisiones', ConsumisionController::class . ':index');             
       $app->get('/api/consumisiones/{id}', ConsumisionController::class . ':show');          
       $app->post('/api/consumisiones', ConsumisionController::class . ':create');     
       $app->put('/api/consumisiones/{id}', ConsumisionController::class . ':update');  
       $app->delete('/api/consumisiones/{id}', ConsumisionController::class . ':delete'); 


     
       
      
};
