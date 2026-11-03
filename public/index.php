<?php
declare(strict_types=1);
use App\BaseDatos;
use App\Http\Router;
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

$metodo = $_SERVER['REQUEST_METHOD'];
$ruta = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router();

$router->agregar("GET", '#^/api/reservas/(\d+)$#', function (string $x){return "It worked $x";});

echo $router->despachar($metodo, $ruta);

/*if(preg_match('#^/api/reservas/(\d+)$#', $ruta, $coincidencias))
    {
        echo "$coincidencias[0] $coincidencias[1]";
    }

if(preg_match('#^/api/reservas/(\d+)/estado$#', $ruta, $coincidencias))
    {
        echo "inside the estado";
    }*/