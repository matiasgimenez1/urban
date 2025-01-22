<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Producto;

class ProductoController {
    public function index(Request $request, Response $response) {
        try {
            $queryParams = $request->getQueryParams();
            $pagina = isset($queryParams['pagina']) ? (int)$queryParams['pagina'] : 1;
            $top = 20; // Número de registros por página
            $offset = $top * ($pagina - 1); // Calcular OFFSET
    
            // Obtener todos los productos
           $productos = Producto::orderBy('nombre', 'asc') // Ordenar por fecha en orden descendente
            ->skip($offset) // Aplicar el OFFSET
            ->take($top) // Aplicar el límite (LIMIT)
            ->get();
            
            $total_records = Producto::count(); // Contar el total de registros
    
            // Formatear los datos según el formato requerido
            $data = $productos->map(function ($producto) {
                return [
                    'type' => 'producto',
                    'id' => $producto->id_producto,
                    'attributes' => [
                        'nombre' => $producto->nombre,
                        'precio_venta' => $producto->precio_venta,
                        'tipo' => $producto->tipo,
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
                'message' => 'Error al obtener los productos: ' . $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
    public function dropdown(Request $request, Response $response) {
        try {
             
            // Obtener todos los productos necesarios
            $productos = Producto::select('id_producto', 'nombre','precio_venta')->get();
    
            // 'code' => (string) $producto->id_producto, para enviar como numero el id de producto, en code
            // Formatear los datos
            $data = $productos->map(function ($producto) {
                return [
                    'code' => $producto->id_producto,
                    'name' => $producto->id_producto . ' - ' . $producto->nombre,
                    'precio' => $producto->precio_venta
                ];
            });
    
            // Responder con los datos formateados
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (\Exception $e) {
            // Manejo de errores
            $response->getBody()->write(json_encode([
                'error' => 'Error al obtener los productos',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
 
    // public function show(Request $request, Response $response, $args) {
    //     $productoId = $args['id']; // Obtener el ID del producto desde los argumentos de la ruta
    
    //     try {
    //         // Intentar encontrar el producto
    //         $producto = Producto::findOrFail($productoId);
    
    //         // Formatear la respuesta en el formato esperado
    //         $data = [
    //             'type' => 'producto',
    //             'id' => $producto->id_producto,
    //             'attributes' => [
    //                 'nombre' => $producto->nombre,
    //                 'precio_venta' => $producto->precio_venta,
    //                 'tipo' => $producto->tipo,
    //                 'estado' => $producto->estado,
    //             ]
    //         ];
    
    //         // Respuesta con los datos del producto
    //         $response->getBody()->write(json_encode(['data' => $data]));
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         // Manejo del error si el producto no existe
    //         $response->getBody()->write(json_encode([
    //             'error' => 'Registro no encontrado'
    //         ]));
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    
    //     } catch (\Exception $e) {
    //         // Manejo de otros errores
    //         $response->getBody()->write(json_encode([
    //             'error' => 'Error al obtener el producto',
    //             'message' => $e->getMessage()
    //         ]));
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    //     }
    // }

    public function show(Request $request, Response $response, $args) {
        $productoId = $args['id']; // Obtener el ID del producto desde los argumentos de la ruta
    
        try {
            // Intentar encontrar el producto
            $producto = Producto::findOrFail($productoId);
    
            // Formatear la respuesta en el formato solicitado
            $data = [
                'type' => 'producto',
                'id' => (string) $producto->id_producto, // ID como cadena
                'attributes' => [
                    'nombre' => $producto->nombre ?? null, 
                    'precio_venta' => (string) $producto->precio_venta ?? null,
                    'estado' => (string) $producto->estado ?? null
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
                'error' => 'Error al obtener el producto',
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
            // Crear el producto
            $producto = Producto::create($data);
    
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
            $producto = Producto::findOrFail($productoId);
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
    
        $producto = Producto::findOrFail($productoId); // Encuentra el producto o lanza un error si no existe
        $producto->delete(); // Elimina el producto
    
        $response->getBody()->write(json_encode(['message' => 'Eliminado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
