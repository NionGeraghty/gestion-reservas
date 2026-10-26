<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$bd = new App\BaseDatos(
    $_ENV['DB_HOST'],
    (int) $_ENV['DB_PORT'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
);

$repositorio = new App\Repositorio\ReservaRepositorio($bd->conexion());

echo "Sin filtros: " . count($repositorio->buscar([])) . "\n";
echo "Confirmadas: " . count($repositorio->buscar(['estado' => 'confirmada'])) . "\n";
echo "Nombre 'maria': " . count($repositorio->buscar(['nombre_huesped' => 'maria'])) . "\n";
echo "Del 25/10 al 31/10: " . count($repositorio->buscar(['desde' => '2026-10-25', 'hasta' => '2026-10-31'])) . "\n";
echo "Confirmadas + fechas: " . count($repositorio->buscar(['estado' => 'confirmada', 'desde' => '2026-10-25', 'hasta' => '2026-10-31'])) . "\n";