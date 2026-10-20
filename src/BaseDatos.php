<?php

namespace App;

use PDO;

class BaseDatos 
{
    private ?PDO $pdo = null;

    public function __construct(
        private string $host,
        private int $puerto,
        private string $nombreBD,
        private string $usuario,
        private string $contrasena,
    ) {}

    public function conexion(): PDO 
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};port={$this->puerto};dbname={$this->nombreBD};charset=utf8mb4";
            $this->pdo = new PDO(
                $dsn,
                $this->usuario,
                $this->contrasena,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        
        return $this->pdo;
    }
}