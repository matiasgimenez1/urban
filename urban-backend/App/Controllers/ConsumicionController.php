<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Consumicion;

class ConsumicionController {

    public function getMaxId(Request $request, Response $response) {
        try {
            // Obtener el valor máximo de id_agendamiento
            $maxId = AgendamientoCab::max('id_agendamiento');
    
            // Preparar la respuesta en el formato solicitado
            $response_data = [
                'data' => [
                    [
                        'type' => 'maxAgendamiento',
                        'attributes' => [
                            'maxID' => $maxId ?? 0 // Si no hay registros, devolver 0
                        ]
                    ]
                ]
            ];
    
            // Escribir la respuesta en el cuerpo
            $response->getBody()->write(json_encode($response_data));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            // Manejo de errores
            $errorResponse = [
                'error' => true,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($errorResponse));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

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

    // public function show(Request $request, Response $response, $args)
    // {
    //     try {
    //         $idAgendamiento = $args['id_agendamiento'];
    //         $item = $args['item'];
    
    //         // Busca la consumición con ambos parámetros
    //         $consumicion = Consumicion::where('id_agendamiento', $idAgendamiento)
    //                       ->where('item', $item)
    //                       ->orderBy('item', 'asc') // Si necesitas orden específico
    //                       ->first();
                          
    //         // Verificar si se encontró la consumición
    //         if (!$consumicion) {
    //             $response->getBody()->write(json_encode([
    //                 'data' => null,
    //                 'message' => 'Consumición no encontrada'
    //             ]));
    //             return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    //         }
    
    //         // Formatear la respuesta
    //         $responseData = [
    //             'data' => [
    //                 'type' => 'consumicion',
    //                 'id' => "{$consumicion->id_agendamiento}-{$consumicion->item}", // Clave compuesta como ID
    //                 'attributes' => [
    //                     'id_agendamiento' => $consumicion->id_agendamiento,
    //                     'item' => $consumicion->item
    //                 ],
    //                 'relationships' => [
    //                     'detalles' => [
    //                         'data' => [
    //                             [
    //                                 'type' => 'detalle',
    //                                 'id' => "{$consumicion->id_agendamiento}-{$consumicion->item}", // Clave compuesta como ID
    //                                 'attributes' => [
    //                                     'producto' => $consumicion->producto,
    //                                     'cantidad' => $consumicion->cantidad,
    //                                     'precio_venta' => $consumicion->precio_venta
    //                                 ]
    //                             ]
    //                         ]
    //                     ]
    //                 ]
    //             ]
    //         ];
    
    //         $response->getBody()->write(json_encode($responseData));
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    //     } catch (\Exception $e) {
    //         $response->getBody()->write(json_encode([
    //             'error' => 'Error al obtener la consumición',
    //             'message' => $e->getMessage()
    //         ]));
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    //     }
    // }
    
    public function show(Request $request, Response $response, $args)
    {
        try {
            // Obtener los parámetros desde la URL
            $idAgendamiento = $args['id_agendamiento'];
            $item = $args['item'];
    
            // Buscar el agendamiento con sus detalles
            $agendamiento = Consumicion::with('detalles')
                ->where('id_agendamiento', $idAgendamiento)
                ->whereHas('detalles', function ($query) use ($item) {
                    $query->where('item', $item);
                })
                ->first();
    
            if (!$agendamiento) {
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->getBody()->write(json_encode([
                    'error' => true,
                    'message' => 'Agendamiento o detalle no encontrado.'
                ]));
            }
    
            // Formatear la respuesta
            $responseData = [
                'data' => [
                    'type' => 'agendamiento',
                    'id' => $agendamiento->id_agendamiento,
                    'attributes' => [
                        'id_agendamiento' => $idAgendamiento,
                        'item' => $item
                    ],
                    'relationships' => [
                        'detalles' => [
                            'data' => $agendamiento->detalles->map(function ($detalle) {
                                return [
                                    'type' => 'detalle',
                                    'id' => $detalle->item,
                                    'attributes' => [
                                        'id_agendamiento' => $detalle->id_agendamiento,
                                        'item' => $detalle->item,
                                        'producto' => $detalle->producto,
                                        'cantidad' => $detalle->cantidad,
                                        'precio_venta' => $detalle->precio_venta
                                    ]
                                ];
                            })
                        ]
                    ]
                ]
            ];
    
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            // Manejo de errores
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => 'Error al obtener el agendamiento.',
                'details' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
    

    // Crear una nueva consumición
    public function create(Request $request, Response $response) {
        $input = json_decode($request->getBody(), true);
    
        if (!isset($input['detail']) || !is_array($input['detail'])) {
            $response->getBody()->write(json_encode([
                'error' => 'Datos no válidos',
                'message' => 'El campo detail es requerido y debe ser un array'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        $errors = [];
        $success = [];
    
        foreach ($input['detail'] as $detail) {
            try {
                // Validar campos requeridos
                if (!isset($detail['id_agendamiento'], $detail['item'], $detail['producto'], $detail['cantidad'], $detail['precio_venta'], $detail['id_item'])) {
                    $errors[] = [
                        'detail' => $detail,
                        'error' => 'Faltan campos requeridos'
                    ];
                    continue;
                }
    
                // Crear la consumición
                $consumicion = new Consumicion();
                $consumicion->id_agendamiento = $detail['id_agendamiento'];
                $consumicion->item = $detail['item'];
                $consumicion->producto = $detail['producto'];
                $consumicion->cantidad = $detail['cantidad'];
                $consumicion->precio_venta = $detail['precio_venta'];
                $consumicion->id_item = $detail['id_item'];
                $consumicion->save();
    
                $success[] = [
                    'id' => $consumicion->id,
                    'message' => 'Consumición creada correctamente'
                ];
            } catch (\Exception $e) {
                $errors[] = [
                    'detail' => $detail,
                    'error' => $e->getMessage()
                ];
            }
        }
    
        $response->getBody()->write(json_encode([
            'success' => $success,
            'errors' => $errors
        ]));
    
        return $response->withHeader('Content-Type', 'application/json')->withStatus(count($errors) ? 207 : 201);
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
