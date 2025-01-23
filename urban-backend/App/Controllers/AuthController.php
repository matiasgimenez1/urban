<?php
 namespace App\Controllers;

 use Psr\Http\Message\ResponseInterface as Response;
 use Psr\Http\Message\ServerRequestInterface as Request;
 use Firebase\JWT\JWT;  // Asegúrate de tener la librería JWT de Firebase
 use PDO;
 
 class AuthController {
     public function generateToken(Request $request, Response $response) {
         try {
             // Obtener los datos enviados en el cuerpo de la solicitud
             $params = json_decode(file_get_contents('php://input'), true);
 
             // Verifica que se reciban el nombre de usuario y la contraseña
             if (empty($params['username']) || empty($params['password'])) {
                 $response->getBody()->write(json_encode(['error' => 'Usuario o contraseña faltante']));
                 return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
             }
 
             $username = $params['username'];
             $password = $params['password'];
 
          // Intentar conectarse a la base de datos con las credenciales proporcionadas
            $dsn = "pgsql:host=autorack.proxy.rlwy.net;port:47478;dbname=railway;user=postgres;password=EilJhYIdChoIIxhJKbIgqONEzCrCxDPw"; // Ajusta 'localhost' y 'paintball' según sea necesario
            try {
                $db = new PDO($dsn, $username, $password); // Ajusta el nombre de usuario y contraseña de la base de datos
               // $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configura el modo de errores para excepciones

            } catch (Exception $e) {
                // Si la conexión falla, captura la excepción y responde con el mensaje adecuado
                $response->getBody()->write(json_encode([
                    'message' => 'Servicio no disponible. Intenta más tarde.',
                    'error' => $e->getMessage()  // Incluye el mensaje de error detallado
                ]));
                return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
            }

 
            // Buscar el usuario en la base de datos
            $stmt = $db->prepare("SELECT * FROM public.usuarios WHERE nombre = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user && !password_verify($password, $user['contrasena'])) {
                // Si no se encuentra el usuario o la contraseña no es válida
                $response->getBody()->write(json_encode(['message' => 'Credenciales Incorrectas']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }
            
 
            // Validaciones
            if ( $user['estado'] !== 'A') {
                // Si no se encuentra el usuario, la contraseña no es válida o el estado no es 'A' (activo)
                $response->getBody()->write(json_encode(['message' => 'Usuario no autorizado']));
                return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
            }

             // Si la conexión es exitosa y las credenciales son válidas, generar el token JWT
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
 
             return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
 
         } catch (Exception $e) {
             // Registrar el error y devolver una respuesta con el mensaje de error
             error_log($e->getMessage());
             $response->getBody()->write(json_encode(['error' => 'Application error', 'message' => $e->getMessage()]));
             return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
         }
     }
 }
 
