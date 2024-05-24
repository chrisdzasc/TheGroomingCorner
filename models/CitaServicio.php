<?php

namespace Model;

class CitaServicio extends ActiveRecord{
    // Configuracion de BD
    protected static $tabla = 'citasservicios'; // Nombre de la tabla
    protected static $columnasDB = ['id', 'citaId', 'servicioId']; // Columnas de la tabla de citasservicios

    // Atributos
    public $id;
    public $citaId;
    public $servicioId;

    // constructor
    public function __construct($args = [])
    {
        $this->id           = $args['id'] ?? null;
        $this->citaId       = $args['citaId'] ?? '';
        $this->servicioId   = $args['servicioId'] ?? '';
    }
}

?>