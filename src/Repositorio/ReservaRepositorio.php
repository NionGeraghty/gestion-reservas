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

    public function crear(array $valores): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO reserva (nombre_huesped, email_huesped, nombre_alojamiento, fecha_entrada, fecha_salida, importe, notas)
            VALUES (:nombre_huesped, :email_huesped, :nombre_alojamiento, :fecha_entrada, :fecha_salida, :importe, :notas)');
        $stmt->execute([
            'nombre_huesped'=>$valores["nombre_huesped"], 
            'email_huesped'=>$valores["email_huesped"], 
            'nombre_alojamiento'=>$valores["nombre_alojamiento"], 
            'fecha_entrada'=>$valores["fecha_entrada"], 
            'fecha_salida'=>$valores["fecha_salida"], 
            'importe'=>$valores["importe"], 
            'notas'=>$valores["notas"] ?? null
            ]);
        return intval($this->pdo->lastInsertId());
    }

    public function actualizarEstado(int $id, string $nuevoEstado): void
    {
        $stmt = $this->pdo->prepare('UPDATE reserva
            SET estado = :estado
            WHERE id = :id');
        $stmt->execute(['estado'=>$nuevoEstado, 'id'=>$id]);
    }
}