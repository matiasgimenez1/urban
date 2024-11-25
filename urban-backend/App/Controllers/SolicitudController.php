<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Solicitud;

class SolicitudController {
    public function index(Request $request, Response $response) {
        try {
            $queryParams = $request->getQueryParams();
            $pagina = isset($queryParams['pagina']) ? (int)$queryParams['pagina'] : 1;
            $top = 20; // Número de registros por página
            $offset = $top * ($pagina - 1); // Calcular OFFSET
    
            // Consulta con paginación
           // $solicitudes = Solicitud::skip($offset)->take($top)->get();
           // Consulta con paginación y ordenación
            $solicitudes = Solicitud::orderBy('fecha_agenda', 'desc') // Ordenar por fecha en orden descendente
            ->skip($offset) // Aplicar el OFFSET
            ->take($top) // Aplicar el límite (LIMIT)
            ->get();
            
            $total_records = Solicitud::count(); // Total de registros
    
            $data = $solicitudes->map(function ($solicitud) {
                return [
                    'type' => 'solicitud',
                    'id' => $solicitud->id_solicitud,
                    'attributes' => [
                        'nombre_cliente' => $solicitud->nombre_cliente,
                        'fecha_agenda' => $solicitud->fecha_agenda,
                        'hora_inicio' => $solicitud->hora_inicio,
                        'hora_fin' => $solicitud->hora_fin,
                        'cant_horas' => $solicitud->cant_horas,
                        'telefono' => $solicitud->telefono,
                        'fecha_registro' => $solicitud->fecha_registro,
                        'estado_reserva' => $solicitud->estado_reserva,
                    ]
                ];
            });
    
            $response_data = [
                'data' => $data,
                'meta' => [
                    'total_records' => $total_records, 
                ]
            ];
    
            $response->getBody()->write(json_encode($response_data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (\Exception $e) {
            $error = [
                'error' => true,
                'message' => 'Error al obtener las solicitudes: ' . $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    
 
    public function show(Request $request, Response $response, $args) {
        $solicitudId = $args['id']; // Obtener el ID del solicitud desde los argumentos de la ruta
    
        try {
            // Intentar encontrar la solicitud
            $solicitud = Solicitud::findOrFail($solicitudId);
    
            // Formatear la respuesta en el formato solicitado
            $data = [
                'type' => 'solicitud',
                'id' => (string) $solicitud->id_solicitud, // ID como cadena
                'attributes' => [ 
                    'nombre_cliente' => $solicitud->nombre_cliente,
                    'fecha_agenda' => $solicitud->fecha_agenda,
                    'hora_inicio' => $solicitud->hora_inicio,
                    'hora_fin' => $solicitud->hora_fin,
                    'cant_horas' => $solicitud->cant_horas,
                    'telefono' => $solicitud->telefono,
                    'fecha_registro' => $solicitud->fecha_registro,
                    'estado_reserva' => $solicitud->estado_reserva,
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
                'error' => 'Error al obtener la solicitud',
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
            // Crear la solicitud
            $solicitud = Solicitud::create($data);
    
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
        $solicitudId = $args['id'];
        $rawData = (string) $request->getBody();
        $data = json_decode($rawData, true);
    
        if (is_null($data) || !is_array($data) || empty($data)) {
            $response->getBody()->write(json_encode([
                'error' => 'No se recibieron datos válidos para actualizar'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        try {
            // Intentar encontrar la solicitud
            $solicitud = Solicitud::findOrFail($solicitudId);
            $solicitud->update($data);
    
            $response->getBody()->write(json_encode([
                'message' => 'Actualizado exitosamente',
                'solicitud' => $solicitud->toArray()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Manejo del error si la solicitud no existe
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
        $solicitudId = $args['id']; // Cambia 'id' a 'solicitud'
    
        $solicitud = Solicitud::findOrFail($solicitudId); // Encuentra la solicitud o lanza un error si no existe
        $solicitud->delete(); // Elimina la solicitud
    
        $response->getBody()->write(json_encode(['message' => 'Eliminado exitosamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
