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

    public function buscar(array $filtros): array
    {
        $valoresArray = [];
        $condiciones = [];

        if(isset($filtros["estado"]))
            {
                array_push($condiciones, 'estado = :estado');
                $valoresArray["estado"] = $filtros["estado"];
            }
        if(isset($filtros["nombre_huesped"]))
            {
                array_push($condiciones, 'nombre_huesped LIKE :nombre_huesped'); 
                $valoresArray["nombre_huesped"] = '%' . $filtros["nombre_huesped"] . '%';
            }
        if(isset($filtros["desde"]))
            {
                array_push($condiciones, 'fecha_entrada <= :hasta and fecha_salida >= :desde');
                $valoresArray["desde"] = $filtros["desde"];
                $valoresArray["hasta"] = $filtros["hasta"];
            }

        $stmtString = 'SELECT * FROM reserva';

        if(count($condiciones) !== 0)
            {
                $stmtString = $stmtString . ' WHERE ' . implode(' AND ', $condiciones);
            }

        $stmtString = $stmtString . ' ORDER BY fecha_entrada, id';

        $stmt = $this->pdo->prepare($stmtString);
        $stmt->execute($valoresArray);

        return $stmt->fetchAll();
    }
}