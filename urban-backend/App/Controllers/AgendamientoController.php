<?php
namespace App\Controllers;

use App\Models\AgendamientoCab;
use App\Models\AgendamientoDet;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as Capsule;

class AgendamientoController
{
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

    public function show(Request $request, Response $response, $args)
{
    try {
        // Obtener el ID del agendamiento desde los parámetros de la URL
        $id = $args['id'];

        // Buscar el agendamiento con sus detalles relacionados
        $agendamiento = AgendamientoCab::with('detalles')->find($id);

        if (!$agendamiento) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404)->getBody()->write(json_encode([
                'error' => true,
                'message' => 'Agendamiento no encontrado.'
            ]));
        }

        // Formatear la respuesta
        $responseData = [
            'data' => [
                'type' => 'agendamiento',
                'id' => $agendamiento->id_agendamiento,
                'attributes' => [
                    'id_agendamiento' => $agendamiento->id_agendamiento,
                    'id_partida' => $agendamiento->id_partida,
                    'fecha' => $agendamiento->fecha,
                    'hora_ini' => $agendamiento->hora_ini,
                    'hora_fin' => $agendamiento->hora_fin,
                    'estado' => $agendamiento->estado,
                    'usuario_agenda' => $agendamiento->usuario_agenda,
                    'fecha_alta' => (string) $agendamiento->fecha_alta ?? null,
                    'usuario_alta' => (string) $agendamiento->usuario_alta ?? null,
                    'fecha_modif' => (string) $agendamiento->fecha_modif ?? null,
                    'usuario_modif' => (string) $agendamiento->usuario_modif ?? null
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
                                    'jugador' => $detalle->jugador
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

    
    // Obtener todos los agendamientos con detalles (paginación opcional)
    public function index(Request $request, Response $response) {
       try { 
        $queryParams = $request->getQueryParams();
        $pagina = isset($queryParams['pagina']) ? (int)$queryParams['pagina'] : 1;
        $top = 20; // Número de registros por página
        $offset = $top * ($pagina - 1); // Calcular OFFSET
    
        // Obtener solo los registros de la cabecera con paginación 
        // Obtener solo los registros de la cabecera con paginación
        $agendamientos = AgendamientoCab::orderBy('fecha', 'desc') // Ordenar por fecha en orden descendente
            ->orderBy('hora_ini', 'desc') // Ordenar por hora_ini en orden ascendente
            ->skip($offset) // Aplicar el OFFSET
            ->take($top) // Aplicar el límite (LIMIT)
            ->get();
           
        $total_records = AgendamientoCab::count(); // Total de registros en la tabla
    
        // Formatear los datos según el formato requerido
        $data = $agendamientos->map(function ($agendamiento) {
            return [
                'type' => 'agendamiento',
                'id' => $agendamiento->id_agendamiento,
                'attributes' => [
                    'id_partida' => $agendamiento->id_partida,
                    'fecha' => $agendamiento->fecha,
                    'hora_ini' => $agendamiento->hora_ini,
                    'hora_fin' => $agendamiento->hora_fin,
                    'estado' => $agendamiento->estado,
                    'usuario_agenda' => $agendamiento->usuario_agenda,
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
                    'message' => 'Error al obtener los agendamientos: ' . $e->getMessage()
                ];
                $response->getBody()->write(json_encode($error));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
    }
    
    public function create(Request $request, Response $response)
    {
        // Obtener el cuerpo del POST
       // $data = $request->getParsedBody();
       $rawBody = $request->getBody()->getContents();
       $data = json_decode($rawBody, true);

        // Validar el cuerpo de la solicitud
        if (!$data || !isset($data['master']) || !isset($data['detail'])) {
            $response->getBody()->write(json_encode([
                'error' => 'El cuerpo de la petición debe contener las claves "master" y "detail".'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        // Validar que 'detail' sea un array no vacío
        if (!is_array($data['detail']) || empty($data['detail'])) {
            $response->getBody()->write(json_encode([
                'error' => 'La clave "detail" debe ser un array no vacío.'
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    
        try {
            // Iniciar la transacción
            Capsule::connection()->beginTransaction();
    
            // Crear la cabecera
            $masterData = $data['master'];
            $cabecera = AgendamientoCab::create($masterData);
    
            // Crear los detalles
            foreach ($data['detail'] as $detalle) {
                AgendamientoDet::create([
                    'id_agendamiento' => $cabecera->id_agendamiento,
                    'item' => $detalle['item'],
                    'jugador' => $detalle['jugador']
                ]);
            }
    
            // Confirmar la transacción
            Capsule::connection()->commit();
    
            $response->getBody()->write(json_encode([
                'message' => 'Agendamiento creado exitosamente',
                'id_agendamiento' => $cabecera->id_agendamiento
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            Capsule::connection()->rollBack();
    
            $response->getBody()->write(json_encode([
                'error' => 'Error al crear el agendamiento',
                'message' => $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
        

    // // Actualizar un agendamiento existente
    // public function update(Request $request, Response $response, $args)
    // {
    //     $idAgendamiento = $args['id'];
    //     $data = $request->getParsedBody();

    //     try {

    //           // Validar si 'master' existe
    //     if (!isset($data['master']) || $data['master'] === null) {
    //         return $response->withJson(['error' => '"master" no está presente o es nulo'], 400);
    //     }


    //         // Iniciar transacción
    //         Capsule::connection()->beginTransaction();
    
    //         // Actualizar la cabecera
    //         $cabecera = AgendamientoCab::findOrFail($idAgendamiento);
  

    //         $cabecera->update($data['master']);

    //         $response->getBody()->write(json_encode([
    //             $data['master']
    //         ]));
    
    
    //         // Actualizar los detalles
    //         AgendamientoDet::where('id_agendamiento', $idAgendamiento)->delete(); // Eliminar los detalles existentes
    //         foreach ($data['detail'] as $index => $detalle) {
    //             AgendamientoDet::create([
    //                 'item' => $index + 1,
    //                 'id_agendamiento' => $idAgendamiento,
    //                 'jugador' => $detalle['jugador']
    //             ]);
    //         }
    
    //         // Confirmar transacción
    //         Capsule::connection()->commit();
    
    //         $response->getBody()->write(json_encode([
    //             'message' => 'Agendamiento actualizado exitosamente'
    //         ]));
    
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    //     } catch (\Exception $e) {
    //         // Revertir transacción en caso de error
    //         Capsule::connection()->rollBack();
    
    //         $response->getBody()->write(json_encode([
    //             'error' => 'Error al actualizar el agendamiento',
    //             'message' => $e->getMessage()
    //         ]));
    
    //         return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    //     }
    // }
    

    public function update(Request $request, Response $response, $args)
{
    $idAgendamiento = $args['id'];
    $rawBody = $request->getBody()->getContents();
    $data = json_decode($rawBody, true);

    // Validar el cuerpo de la solicitud
    if (!$data || !isset($data['master']) || !isset($data['detail'])) {
        $response->getBody()->write(json_encode([
            'error' => 'El cuerpo de la petición debe contener las claves "master" y "detail".'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // Validar que 'detail' sea un array no vacío
    if (!is_array($data['detail']) || empty($data['detail'])) {
        $response->getBody()->write(json_encode([
            'error' => 'La clave "detail" debe ser un array no vacío.'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
        // Iniciar la transacción
        Capsule::connection()->beginTransaction();

        // Actualizar la cabecera
        $masterData = $data['master'];
        $cabecera = AgendamientoCab::findOrFail($idAgendamiento); // Buscar cabecera existente
        $cabecera->update($masterData); // Actualizar con los nuevos datos

        // Eliminar los detalles existentes
        AgendamientoDet::where('id_agendamiento', $idAgendamiento)->delete();

        // Crear los nuevos detalles
        foreach ($data['detail'] as $detalle) {
            AgendamientoDet::create([
                'id_agendamiento' => $idAgendamiento,
                'item' => $detalle['item'],
                'jugador' => $detalle['jugador']
            ]);
        }

        // Confirmar la transacción
        Capsule::connection()->commit();

        $response->getBody()->write(json_encode([
            'message' => 'Agendamiento actualizado exitosamente',
            'id_agendamiento' => $idAgendamiento
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        Capsule::connection()->rollBack();

        $response->getBody()->write(json_encode([
            'error' => 'Error al actualizar el agendamiento',
            'message' => $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}


    // Eliminar un agendamiento y sus detalles
    public function delete(Request $request, Response $response, $args)
    {
        $idAgendamiento = $args['id'];

        try {
            AgendamientoDet::where('id_agendamiento', $idAgendamiento)->delete();
            AgendamientoCab::destroy($idAgendamiento);

            $response->getBody()->write(json_encode([
                'message' => 'Agendamiento eliminado exitosamente'
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Error al eliminar el agendamiento',
                'message' => $e->getMessage()
            ]));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
