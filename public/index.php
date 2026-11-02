<?php
declare(strict_types=1);
use App\BaseDatos;
use App\Repositorio\EventoRepositorio;
use App\Repositorio\ReservaRepositorio;
use App\Servicio\ReservaServicio;
use App\Validacion\ReservaValidator;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS']);

date_default_timezone_set('Europe/Madrid');

$bd = new BaseDatos(
    $_ENV['DB_HOST'],
    (int) $_ENV['DB_PORT'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);

$pdo = $bd->conexion();
$servicio = new ReservaServicio(
    $pdo,
    new ReservaRepositorio($pdo),
    new EventoRepositorio($pdo),
    new ReservaValidator()
);

echo count($servicio->listar([]));