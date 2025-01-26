<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\User;

class UserController {

    public function getPersonasPdf(Request $request, Response $response) {
        try {
            $queryParams = $request->getQueryParams();
            $desde = isset($queryParams['desde']) ? $queryParams['desde'] : 'a';
            $hasta = isset($queryParams['hasta']) ? $queryParams['hasta'] : 'z';

            // Consultar los datos con Eloquent
            $personas = User::whereBetween('nombre', [$desde, $hasta])
                ->orderBy('nombre', 'asc')
                ->get();

            // Formatear la respuesta en JSON
            $data = $personas->map(function ($persona) {
                return [
                    'type' => 'persona',
                    'id' => $persona->id_usuario,
                    'attributes' => [
                    'nombre' => $persona->nombre,
                    'id_usuario' => $persona->id_usuario,
                    'contrasena' => $persona->contrasena,
                    'estado' => $persona->estado,
                    'fecha_alta' => $persona->fecha_alta,
                    'usuario_alta' => $persona->usuario_alta
                    ]
                ];
            });


            $response->getBody()->write(json_encode(['data' => $data]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);

        } catch (\Exception $e) {
            $error = [
                'error' => true,
                'message' => 'Error al generar los datos: ' . $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function index(Request $request, Response $response) {
        try {
            $queryParams = $request->getQueryParams();
            $pagina = isset($queryParams['pagina']) ? (int)$queryParams['pagina'] : 1;
            $top = 20; // Número de registros por página
            $offset = $top * ($pagina - 1); // Calcular OFFSET
    
            // Obtener todos los productos
           $productos = User::orderBy('nombre', 'asc') // Ordenar por fecha en orden descendente
            ->skip($offset) // Aplicar el OFFSET
            ->take($top) // Aplicar el límite (LIMIT)
            ->get();
            
            $total_records = User::count(); // Contar el total de registros
    
            // Formatear los datos según el formato requerido
            $data = $productos->map(function ($producto) {
                return [
                    'type' => 'usuario',
                    'id' => $producto->id_usuario,
                    'attributes' => [
                        'nombre' => $producto->nombre,
                        'contrasena' => $producto->contrasena,
                        'estado' => $producto->estado
                    ]
                ];
            });
    
            // Respuesta final
            $response_data = [
                'data' => $data,
                'meta' => [
                    'total_records' => $total_records
                ]
            ];
    
            // Escribir la respuesta en formato JSON
            $response->getBody()->write(json_encode($response_data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (Exception $e) {
            // Manejo de errores
            $error = [
                'error' => true,
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
     
    public function show(Request $request, Response $response, $args) {
        $productoId = $args['id']; // Obtener el ID del producto desde los argumentos de la ruta
    
        try {
            // Intentar encontrar el producto
            $producto = User::findOrFail($productoId);
    
            // Formatear la respuesta en el formato solicitado
            $data = [
                'type' => 'usuario',
                'id' => (string) $producto->id_usuario, // ID como cadena
                'attributes' => [
                    'nombre' => $producto->nombre ?? null, 
                    'contrasena' => (string) $producto->contrasena ?? null,
                    'estado' => (string) $producto->estado ?? null,
                    'fecha_alta' => (string) $producto->fecha_alta ?? null,
                    'usuario_alta' => (string) $producto->usuario_alta ?? null,
                    'fecha_modif' => (string) $producto->fecha_modif ?? null,
                    'usuario_modif' => (string) $producto->usuario_modif ?? null
                ]
            ];
    
            // Respuesta con los datos del producto
            $response->getBody()->write(json_encode(['data' => [$data]]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejo del error si el producto no existe
            $response->getBody()->write(json_encode([
                'error' => 'Registro no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    
        } catch (\Exception $e) {
            // Manejo de otros errores
            $response->getBody()->write(json_encode([
                'error' => 'Error al obtener el usuario',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    
    public function create(Request $request, Response $response) {
        // Obtener el cuerpo crudo y decodificarlo en un array
        $rawData = (string) $request->getBody();
        $data = json_decode($rawData, true);
    
        // Validación para verificar que los datos existen y son válidos
        if (is_null($data) || !is_array($data) || empty($data)) {
            $response->getBody()->write(json_encode([
                'error' => 'No se recibieron datos válidos para dar de alta'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        try {
            // Crear el usuario
            $producto = User::create($data);
    
            // Respuesta de éxito
            $response->getBody()->write(json_encode([
                'message' => 'Creado exitosamente',
                #'producto' => $producto->toArray()
            ]));
    
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    
        } catch (\Exception $e) {
            // Manejo de errores en caso de excepción
            $response->getBody()->write(json_encode([
                'error' => 'Error al dar de alta',
                'message' => $e->getMessage()
            ]));
    
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    public function update(Request $request, Response $response, $args) {
        $productoId = $args['id'];
        $rawData = (string) $request->getBody();
        $data = json_decode($rawData, true);
    
        if (is_null($data) || !is_array($data) || empty($data)) {
            $response->getBody()->write(json_encode([
                'error' => 'No se recibieron datos válidos para actualizar'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        try {
            // Intentar encontrar el producto
            $producto = User::findOrFail($productoId);
            $producto->update($data);
    
            $response->getBody()->write(json_encode([
                'message' => 'Actualizado exitosamente',
                'producto' => $producto->toArray()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejo del error si el producto no existe
            $response->getBody()->write(json_encode([
                'error' => 'Registro no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al actualizar',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    
    
    public function delete(Request $request, Response $response, $args) {
        $productoId = $args['id']; // Cambia 'id' a 'producto'
    
        $producto = User::findOrFail($productoId); // Encuentra el producto o lanza un error si no existe
        $producto->delete(); // Elimina el producto
    
        $response->getBody()->write(json_encode(['message' => 'Eliminado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
