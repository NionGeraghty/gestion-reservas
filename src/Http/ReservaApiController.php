<?php
declare(strict_types=1);
namespace App\Http;

use App\Excepcion\DatosNoValidos;
use App\Servicio\ReservaServicio;

class ReservaApiController
{
    public function __construct(
        private ReservaServicio $servicio
    ){}

    private function responder(mixed $datos, int $codigo = 200): void
    {
        http_response_code($codigo);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($datos,JSON_UNESCAPED_UNICODE);
    }

    public function listar(array $filtros): void
    {
        $reservas = $this->servicio->listar($filtros);
        $this->responder($reservas);
    }

    public function obtener(string $id): void
    {
        $id = intval($id);
        $reserva = $this->servicio->obtener($id);
        $this->responder($reserva);
    }

    public function crear(): void
    {
        $valores = json_decode(file_get_contents('php://input'), true);
        if(!is_array($valores))
            {
                throw new DatosNoValidos(["JSON"=>"json inválido"]);
            }
        $reserva = $this->servicio->crear($valores);
        $this->responder($reserva, 201);
    }

    public function cambiarEstado(string $id): void
    {
        $id = intval($id);
        $cuerpo = json_decode(file_get_contents('php://input'), true);
        if(!is_array($cuerpo))
            {
                throw new DatosNoValidos(["JSON"=>"json inválido"]);
            }
        if(!array_key_exists("estado", $cuerpo))
            {
                throw new DatosNoValidos(["estado"=>"se necesita un estado"]);
            }
        if(!is_string($cuerpo["estado"]))
            {
                throw new DatosNoValidos(["estado"=>"formato del estado no válido"]);
            }

        $reserva = $this->servicio->cambiarEstado($id, $cuerpo["estado"]);
        $this->responder($reserva);
    }
}