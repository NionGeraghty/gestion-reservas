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

$eventos = new App\Repositorio\EventoRepositorio($bd->conexion());

$eventos->crear(1, 'confirmada', 'Reserva confirmada');

foreach ($eventos->buscarPorReserva(1) as $evento) {
    echo $evento['fecha_creacion'] . ' — ' . $evento['tipo'] . "\n";
}