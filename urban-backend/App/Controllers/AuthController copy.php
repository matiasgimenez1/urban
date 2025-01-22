<?php
namespace App\Controllers;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Exception;

class AuthController {
    public function generateToken(Request $request, Response $response) {
        try {
            // Obtener los datos enviados en el cuerpo de la solicitud
            $params = json_decode(file_get_contents('php://input'), true);

            // Verifica que se reciban el nombre de usuario y la contraseña
            if (empty($params['username']) || empty($params['password'])) {
                $response->getBody()->write(json_encode(['error' => 'Usuario o sontraseña faltante']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $username = $params['username'];
            $password = $params['password'];

            // Intentar conectarse a la base de datos con las credenciales proporcionadas
            $dsn = "pgsql:host=localhost;dbname=parcial2"; // Ajusta 'localhost' y 'parcial2' según sea necesario
            try {
                $db = new \PDO($dsn, $username, $password);
            } catch (Exception $e) {
                // Si la conexión falla, las credenciales son incorrectas
                $response->getBody()->write(json_encode(['message' => 'Credenciales Incorrectas']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            // Si la conexión es exitosa, generar el token JWT
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // 1 hora de validez
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'data' => [
                    'username' => $username
                ]
            ];

            // Generar el token usando la clave secreta
            $secretKey = $_ENV['JWT_SECRET'];
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Devolver el token en la respuesta
            $response->getBody()->write(json_encode([
                 
                'auth' => true,
                'token' => $jwt
            ]));

            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            // Registrar el error y devolver una respuesta con el mensaje de error
            error_log($e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Application error', 'message' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
