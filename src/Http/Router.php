<?php
declare(strict_types=1);
namespace App\Http;

use App\Excepcion\RecursoNoEncontrado;

class Router
{
    public function __construct(
        private $routes = []
    ){}

    public function agregar(string $metodo, string $patron, callable $accion): void
    {
        $this->routes = [...$this->routes, array("metodo"=>$metodo, "patron"=>$patron, "accion"=>$accion)];
    }

    public function despachar(string $metodo, string $ruta): mixed
    {
        $routes = $this->routes;
        for($i=0;$i<count($routes);$i++)
            {
                if($metodo === $routes[$i]["metodo"] && preg_match($routes[$i]["patron"], $ruta, $coincidencias))
                    {  
                        return $routes[$i]["accion"](...array_slice($coincidencias, 1));
                    }
            }
        throw new RecursoNoEncontrado();
    }
}