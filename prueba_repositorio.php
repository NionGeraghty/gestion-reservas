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

$repositorio->actualizarEstado(1, 'confirmada');
var_dump($repositorio->buscarPorId(1)['estado']);

$repositorio->actualizarEstado(1, 'inventado');