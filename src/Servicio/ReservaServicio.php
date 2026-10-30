<?php
declare(strict_types=1);

namespace App\Servicio;

use App\Excepcion\RecursoNoEncontrado;
use App\Repositorio\EventoRepositorio;
use App\Repositorio\ReservaRepositorio;
use App\Validacion\ReservaValidator;
use PDO;

class ReservaServicio
{
    public function __construct
    (
        private PDO $pdo,
        private ReservaRepositorio $reservas,
        private EventoRepositorio $eventos,
        private ReservaValidator $validator,
    ){}

    public function obtener(int $id): array
    {
        $reserva = $this->reservas->buscarPorId($id);

        if($reserva===null)
            {
                throw new RecursoNoEncontrado("La reserva con id:{$id} no existe");
            }
        $reserva["eventos"] = $this->eventos->buscarPorReserva($id);
        return $reserva;
    }
}