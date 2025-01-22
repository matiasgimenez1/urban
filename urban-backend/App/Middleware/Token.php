<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Exception;

class Token {
    public function __invoke(Request $request, Response $response, RequestHandler $handler): Response {
        // Obtener el encabezado de autorización
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (!$authHeader) {
            // Si no hay encabezado de autorización, devolver un error
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // El token está en el formato "Bearer <token>"
        $token = str_replace('Bearer ', '', $authHeader);
        
        if (empty($token)) {
            // Si no se proporciona el token después de "Bearer"
            $response->getBody()->write(json_encode(['error' => 'Token vacío']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        try {
            // Verificar el token
            $secretKey = $_ENV['JWT_SECRET']; // Tu clave secreta para verificar el token
            $decoded = JWT::decode($token, $secretKey, ['HS256']);
            
            // Se puede agregar el payload decodificado en el request para ser usado en los controladores
            $request = $request->withAttribute('user', (array) $decoded->data);

        } catch (Exception $e) {
            // Si el token es inválido o ha expirado
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado', 'message' => $e->getMessage()]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Continuar con la siguiente fase de la solicitud (manejarla con el siguiente middleware o controlador)
        return $handler->handle($request);
    }
}
