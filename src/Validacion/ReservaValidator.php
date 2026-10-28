<?php
declare(strict_types=1);
namespace App\Validacion;

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
        $fecha_entrada_bien = false;
        $fecha_salida_bien = false;

        $texto_entrada = $datos["fecha_entrada"] ?? "";
        if(!is_string($texto_entrada))
            {
                $errores["fecha_entrada"] = "La fecha de entrada no es válida";
            }
        else if($texto_entrada==="")
            {
                $errores["fecha_entrada"] = "La fecha de entrada es obligatoria";
            }
        else
            {
                $fecha_entrada = DateTimeImmutable::createFromFormat('!Y-m-d', $texto_entrada);
                if(!$fecha_entrada || $fecha_entrada->format('Y-m-d') !== $texto_entrada)
                    {
                        $errores["fecha_entrada"] = "La fecha de entrada no es válida";
                    }
                else {$fecha_entrada_bien=true;}
            }

        $texto_salida = $datos["fecha_salida"] ?? "";
        if(!is_string($texto_salida))
            {
                $errores["fecha_salida"] = "La fecha de salida no es válida";
            }
        else if($texto_salida==="")
            {
                $errores["fecha_salida"] = "La fecha de salida es obligatoria";
            }
        else
            {
                $fecha_salida = DateTimeImmutable::createFromFormat('!Y-m-d', $texto_salida);
                if(!$fecha_salida || $fecha_salida->format('Y-m-d') !== $texto_salida)
                    {
                        $errores["fecha_salida"] = "La fecha de salida no es válida";
                    }
                else {$fecha_salida_bien=true;}
            }

        if($fecha_entrada_bien && $fecha_salida_bien)
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
}