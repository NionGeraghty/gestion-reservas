<?php
declare(strict_types=1);
namespace App\Http;

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
}