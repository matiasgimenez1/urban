<?php
// valida contra la tabla de usuarios
namespace App\Controllers;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;
use Exception;

class AuthController {
    public function generateToken(Request $request, Response $response) {
        try {
            // Obtener los datos enviados en el cuerpo de la solicitud usando json_decode
            $params = json_decode(file_get_contents('php://input'), true);

            // Verifica que se reciban el nombre de usuario y la contraseña
            if (empty($params['username']) || empty($params['password'])) {
                $response->getBody()->write(json_encode(['error' => 'Usuario o contraseña faltante']));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }

            $username = $params['username'];
            $password = $params['password'];
            
            // Buscar al usuario en la base de datos
            $user = User::where('username', $username)->first();

            // Verificar si el usuario existe y la contraseña es correcta
            if (!$user || !password_verify($password, $user->password)) {
                $response->getBody()->write(json_encode(['error' => 'Credenciales inválidas']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

            // Generar el token JWT si las credenciales son válidas
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // 1 hora de validez
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'data' => [
                    'userId' => $user->id,
                    'username' => $user->username
                ]
            ];

            // Generar el token usando la clave secreta
            $secretKey = $_ENV['JWT_SECRET'];
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // Devolver el token en la respuesta
            $response->getBody()->write(json_encode(['token' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            // Registrar el error y devolver una respuesta con el mensaje de error
            error_log($e->getMessage());
            $response->getBody()->write(json_encode(['error' => 'Application error', 'message' => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
