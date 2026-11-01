<?php
declare(strict_types=1);
namespace App\Validacion;

use App\Modelo\EstadoReserva;
use DateTimeImmutable;

class ReservaValidator
{
    public function validarCreacion(array $datos): array
    {
        $errores = [];

        //nombre_huesped
        $nombre_huesped = $datos["nombre_huesped"] ?? "";
        if(!is_string($nombre_huesped))
            {
                $errores["nombre_huesped"] = "El nombre no es válido";
            }
        else
            {
                $nombre_huesped = trim($nombre_huesped);
                if($nombre_huesped==="")
                    {
                        $errores["nombre_huesped"] = "El nombre es obligatorio";
                    }
                else if(mb_strlen($nombre_huesped)>100)
                    {
                        $errores["nombre_huesped"] = "El nombre es demasiado largo";
                    }
            }

        //email_huesped
        $email = $datos["email_huesped"] ?? "";
        if(!is_string($email))
            {
                $errores["email_huesped"] = "El correo electrónico no es válido";
            }
        else
            {
                $email = trim($email);
                if($email==="")
                    {
                        $errores["email_huesped"] = "El correo electrónico es obligatorio";
                    }
                else if(mb_strlen($email)>254)
                    {
                        $errores["email_huesped"] = "El correo electrónico es demasiado largo";
                    }
                else if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $errores["email_huesped"] = "El correo electrónico no es válido";
                    }
            }

        //nombre_alojamiento
        $nombre_alojamiento = $datos["nombre_alojamiento"] ?? "";
        if(!is_string($nombre_alojamiento))
            {
                $errores["nombre_alojamiento"] = "El alojamiento no es válido";
            }
        else
            {
                $nombre_alojamiento = trim($nombre_alojamiento);
                if($nombre_alojamiento==="")
                    {
                        $errores["nombre_alojamiento"] = "El alojamiento es obligatorio";
                    }
                else if(mb_strlen($nombre_alojamiento)>150)
                    {
                        $errores["nombre_alojamiento"] = "El alojamiento es demasiado largo";
                    }
            }

        //fechas
        $fecha_entrada = null;
        $fecha_salida = null;

        $texto_entrada = $datos["fecha_entrada"] ?? "";
        if($texto_entrada==="")
            {
                $errores["fecha_entrada"] = "La fecha de entrada es obligatoria";
            }
        else
            {
                $fecha_entrada = $this->leerFecha($texto_entrada);
                if($fecha_entrada===null)
                    {
                        $errores["fecha_entrada"] = "La fecha de entrada no es válida";
                    }
            }

        $texto_salida = $datos["fecha_salida"] ?? "";
        if($texto_salida==="")
            {
                $errores["fecha_salida"] = "La fecha de salida es obligatoria";
            }
        else
            {
                $fecha_salida = $this->leerFecha($texto_salida);
                if($fecha_salida===null)
                    {
                        $errores["fecha_salida"] = "La fecha de salida no es válida";
                    }
            }

        if($fecha_entrada!==null && $fecha_salida!==null)
            {
                $hoy = new DateTimeImmutable('today');
                if($fecha_salida<=$fecha_entrada)
                    {
                        $errores["fecha_salida"] = "La fecha de salida tiene que ser después de la fecha de entrada";
                    }
                else if($fecha_entrada<$hoy)
                    {
                        $errores["fecha_entrada"] = "La fecha de entrada no puede estar en el pasado";
                    }
            }

        //importe
        if(!isset($datos["importe"]))
            {
                $errores["importe"] = "El importe es obligatorio";
            }
        else if(!is_int($datos["importe"]) && !is_float($datos["importe"]) && !is_string($datos["importe"]))
            {
                $errores["importe"] = "El importe tiene que ser un número";
            }
        else
            {
                $importe = $datos["importe"];
                if(!is_string($importe))
                    {
                        $importe = strval($importe);
                    }

                if(!preg_match("/^\d{1,8}(\.\d{1,2})?$/", $importe))
                    {
                        $errores["importe"] = "El importe no es válido";
                    }
            }

        //notas
        if(isset($datos["notas"]))
            {
                $notas = $datos["notas"];
                if(!is_string($notas))
                    {
                        $errores["notas"] = "El formato de las notas no es válido";
                    }
                else if(mb_strlen($notas)>1000)
                    {
                        $errores["notas"] = "Las notas son demasiado largas";
                    }
            }

        return $errores;
    }

    public function validarFiltros(array $filtros): array
    {
        $errores = [];

        //estado
        if(isset($filtros["estado"]))
            {
                $estado = $filtros["estado"];
                if(!is_string($estado) || EstadoReserva::tryFrom($estado)===null)
                    {
                        $errores["estado"] = "El estado no es válido";
                    }
            }

        //nombre_huesped
        if(isset($filtros["nombre_huesped"]))
            {
                $nombre = $filtros["nombre_huesped"];
                if(!is_string($nombre))
                    {
                        $errores["nombre_huesped"] = "La búsqueda no es válida";
                    }
                else if(mb_strlen($nombre)>100)
                    {
                        $errores["nombre_huesped"] = "La búsqueda es demasiado larga";
                    }
            }

        //fechas
        $desde = isset($filtros["desde"]);
        $hasta = isset($filtros["hasta"]);

        if($desde && !$hasta)
            {
                $errores["hasta"] = "Indica también la fecha final";
            }
        else if(!$desde && $hasta)
            {
                $errores["desde"] = "Indica también la fecha inicial";
            }
        else if($desde && $hasta)
            {
                $desde = $this->leerFecha($filtros["desde"]);
                $hasta = $this->leerFecha($filtros["hasta"]);

                if($desde===null)
                    {
                        $errores["desde"] = "La fecha inicial no es válida";
                    }
                if($hasta===null)
                    {
                        $errores["hasta"] = "La fecha final no es válida";
                    }
                if($desde!==null && $hasta!==null && $hasta<$desde)
                    {
                        $errores["hasta"] = "La fecha final no puede ser anterior a la inicial";
                    }
            }

        return $errores;
    }

    private function leerFecha(mixed $valor): ?DateTimeImmutable
    {
        if(!is_string($valor))
            {
                return null;
            }

        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
        if($fecha===false || $fecha->format('Y-m-d')!==$valor)
            {
                return null;
            }

        return $fecha;
    }
}