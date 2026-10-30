<?php
declare(strict_types=1);
namespace App\Excepcion;

use Exception;

class DatosNoValidos extends Exception
{
    public function __construct(private array $errores)
    {
        parent::__construct("Los datos no son válidos");
    }

    public function errores(): array
    {
        return $this->errores;
    }
}