<?php
declare(strict_types=1);

namespace App\Repositorio;

use PDO;

class EventoRepositorio
{
    public function __construct
    (
        private PDO $pdo,
    ){}

    public function crear(int $reserva_id, string $tipo, string $descripcion): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO reserva_evento (reserva_id, tipo, descripcion) VALUES (:reserva_id, :tipo, :descripcion)');
        $stmt->execute(['reserva_id'=>$reserva_id, 'tipo'=>$tipo, 'descripcion'=>$descripcion]);
    }

    public function buscarPorReserva(int $reserva_id): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM reserva_evento WHERE reserva_id = :reserva_id ORDER BY fecha_creacion, id');
        $stmt->execute(['reserva_id'=>$reserva_id]);
        return $stmt->fetchAll();
    }
}