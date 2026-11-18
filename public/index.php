<?php
declare(strict_types=1);
use App\BaseDatos;
use App\Excepcion\DatosNoValidos;
use App\Excepcion\RecursoNoEncontrado;
use App\Excepcion\TransicionNoPermitida;
use App\Http\ReservaApiController;
use App\Http\Router;
use App\Repositorio\EventoRepositorio;
use App\Repositorio\ReservaRepositorio;
use App\Servicio\ReservaServicio;
use App\Validacion\ReservaValidator;

require __DIR__ . '/../vendor/autoload.php';

function enviarJson (mixed $datos, int $codigo): void
{
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
}

try{

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
    $apiController = new ReservaApiController($servicio);

    $router->agregar("GET", '#^/api/reservas$#', function () use($apiController) {$apiController->listar($_GET);});
    $router->agregar("GET", '#^/api/reservas/(\d+)$#', function (string $id) use($apiController) {$apiController->obtener($id);});
    $router->agregar("POST", '#^/api/reservas$#', function () use($apiController) {$apiController->crear();});
    $router->agregar("PATCH", '#^/api/reservas/(\d+)/estado$#', function (string $id) use($apiController) {$apiController->cambiarEstado($id);});


    $router->despachar($metodo, $ruta);

    } catch (DatosNoValidos $e){
        enviarJson(['error' => $e->getMessage(), 'campos' => $e->errores()], 400);
    } catch (RecursoNoEncontrado $e){
        enviarJson(['error' => $e->getMessage()],404);
    } catch (TransicionNoPermitida $e){
        enviarJson(['error' => $e->getMessage()], 409);
    } catch (Throwable $e){
        error_log((string) $e);
        enviarJson(['error' => 'Ha ocurrido un error inesperado'], 500);
    }
