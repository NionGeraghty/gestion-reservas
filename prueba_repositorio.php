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

$nuevaId = $repositorio->crear([
    'nombre_huesped' => 'Prueba Pérez',
    'email_huesped' => 'prueba@example.com',
    'nombre_alojamiento' => 'Casa Rural El Robledal',
    'fecha_entrada' => '2026-12-10',
    'fecha_salida' => '2026-12-12',
    'importe' => '180.00',
]);

var_dump($nuevaId);
var_dump($repositorio->buscarPorId($nuevaId));