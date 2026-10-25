<?php
declare(strict_types=1);

namespace App\Repositorio;

use PDO;

class ReservaRepositorio
{
    public function __construct
    (
        private PDO $pdo,
    ) {}

    public function buscarPorId (int $id) : ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM reserva WHERE id = :id');
        $stmt->execute(['id'=>$id]);
        $fila = $stmt->fetch();

        return $fila === false ? null : $fila;
    }
}