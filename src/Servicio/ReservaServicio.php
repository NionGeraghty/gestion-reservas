<?php
declare(strict_types=1);

namespace App\Servicio;

use App\Excepcion\DatosNoValidos;
use App\Excepcion\RecursoNoEncontrado;
use App\Excepcion\TransicionNoPermitida;
use App\Repositorio\EventoRepositorio;
use App\Repositorio\ReservaRepositorio;
use App\Validacion\ReservaValidator;
use App\Modelo\EstadoReserva;
use PDO;
use Throwable;

class ReservaServicio
{
    public function __construct
    (
        private PDO $pdo,
        private ReservaRepositorio $reservas,
        private EventoRepositorio $eventos,
        private ReservaValidator $validator,
    ){}

    private function limpiarArray(array $valores): array
    {
        $valores["nombre_huesped"] = trim($valores["nombre_huesped"]);
        $valores["email_huesped"] = trim($valores["email_huesped"]);
        $valores["nombre_alojamiento"] = trim($valores["nombre_alojamiento"]);
        if(isset($valores["notas"]))
            {
                $valores["notas"] = trim($valores["notas"]);
            }
        return $valores;
    }

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
    
    public function crear(array $valores): array
    {
        $errores = $this->validator->validarCreacion($valores); //Verificar los datos están bien

        if($errores !== [])
            {
                throw new DatosNoValidos($errores); 
            }

        $valores = $this->limpiarArray($valores);

        $this->pdo->beginTransaction();

        try{
            $reservaId = $this->reservas->crear($valores);
            $this->eventos->crear($reservaId, "creada", "Reserva creada");
            $this->pdo->commit();
        } catch(Throwable $e){
            $this->pdo->rollBack();
            throw $e;
        }
        
        return $this->obtener($reservaId);
    }

    public function cambiarEstado(int $reservaId, string $nuevoEstado): array
    {
        $estadoNuevo = EstadoReserva::tryFrom($nuevoEstado);
        if(!$estadoNuevo)
            {
                throw new DatosNoValidos(['estado' => 'El estado no es válido']);
            }
        $this->pdo->beginTransaction();

        try{
            $reserva = $this->obtener($reservaId);
            $estadoActual = EstadoReserva::from($reserva["estado"]);
            if(!$estadoActual->puedeCambiar($estadoNuevo))
                {
                    throw new TransicionNoPermitida("No se puede pasar de {$estadoActual->value} a {$estadoNuevo->value}");
                }

            $this->reservas->actualizarEstado($reservaId, $nuevoEstado);
            $this->eventos->crear($reservaId, $nuevoEstado, "Reserva {$estadoNuevo->value}");
            $this->pdo->commit();

        } catch(Throwable $e){
            $this->pdo->rollBack();
            throw $e;
        }
        return $this->obtener($reservaId);
    }
}