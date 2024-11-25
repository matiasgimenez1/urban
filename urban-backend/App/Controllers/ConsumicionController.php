<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Consumicion;

class ConsumicionController {
    // Listar consumiciones con paginación
    public function index(Request $request, Response $response) {
        try {
            $queryParams = $request->getQueryParams();
            $pagina = isset($queryParams['pagina']) ? (int)$queryParams['pagina'] : 1;
            $top = 20; // Número de registros por página
            $offset = $top * ($pagina - 1); // Calcular OFFSET

            $consumiciones = Consumicion::orderBy('id_item', 'asc') // Ordenar por ID
                ->skip($offset)
                ->take($top)
                ->get();

            $total_records = Consumicion::count(); // Contar el total de registros

            // Formatear los datos
            $data = $consumiciones->map(function ($item) {
                return [
                    'type' => 'consumicion',
                    'id' => $item->id_item,
                    'attributes' => [
                        'id_agendamiento' => $item->id_agendamiento,
                        'producto' => $item->producto,
                        'cantidad' => $item->cantidad,
                        'precio_venta' => $item->precio_venta
                    ]
                ];
            });

            $response_data = [
                'data' => $data,
                'meta' => [
                    'total_records' => $total_records
                ]
            ];

            $response->getBody()->write(json_encode($response_data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al obtener las consumiciones',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Mostrar una consumición por ID
    public function show(Request $request, Response $response, $args) {
        $itemId = $args['id'];

        try {
            $consumicion = Consumicion::findOrFail($itemId);

            $data = [
                'type' => 'consumicion',
                'id' => $consumicion->id_item,
                'attributes' => [
                    'id_agendamiento' => $consumicion->id_agendamiento,
                    'producto' => $consumicion->producto,
                    'cantidad' => $consumicion->cantidad,
                    'precio_venta' => $consumicion->precio_venta
                ]
            ];

            $response->getBody()->write(json_encode(['data' => [$data]]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Registro no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al obtener la consumición',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Crear una nueva consumición
    public function create(Request $request, Response $response) {
        $data = json_decode((string)$request->getBody(), true);

        if (is_null($data) || !is_array($data)) {
            $response->getBody()->write(json_encode([
                'error' => 'No se recibieron datos válidos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $consumicion = Consumicion::create($data);

            $response->getBody()->write(json_encode([
                'message' => 'Consumición creada exitosamente',
                'data' => $consumicion
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al crear la consumición',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Actualizar una consumición
    public function update(Request $request, Response $response, $args) {
        $itemId = $args['id'];
        $data = json_decode((string)$request->getBody(), true);

        if (is_null($data) || !is_array($data)) {
            $response->getBody()->write(json_encode([
                'error' => 'No se recibieron datos válidos'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $consumicion = Consumicion::findOrFail($itemId);
            $consumicion->update($data);

            $response->getBody()->write(json_encode([
                'message' => 'Consumición actualizada exitosamente',
                'data' => $consumicion
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Registro no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al actualizar la consumición',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Eliminar una consumición
    public function delete(Request $request, Response $response, $args) {
        $itemId = $args['id'];

        try {
            $consumicion = Consumicion::findOrFail($itemId);
            $consumicion->delete();

            $response->getBody()->write(json_encode([
                'message' => 'Consumición eliminada exitosamente'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Registro no encontrado'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al eliminar la consumición',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
