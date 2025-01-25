<?php
// Archivo: CORS.php (Puedes crearlo en una carpeta, por ejemplo, en App/Middleware)

// Archivo: CORS.php
namespace App\Middleware;

// Archivo: CORS.php
//namespace App\Middleware;

class CORS {
    public function __invoke($request, $handler) {
        $response = $handler->handle($request);

        // Agregar encabezados CORS a todas las respuestas
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*') // para que ingrese el login
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, access-token')
            ->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
