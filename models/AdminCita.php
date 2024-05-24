<?php

namespace Model;

class AdminCita extends ActiveRecord{
    protected static $tabla = 'citasservicios'; // Nombre de la tabla
    protected static $columnasDB = ['id', 'hora', 'cliente', 'email', 'telefono', 'servicio', 'precio']; // Nombre de las columnas
    
    // Atributos
    public $id;
    public $hora;
    public $cliente;
    public $email;
    public $telefono;
    public $servicio;
    public $precio;

    // Constructor
    public function __construct($args = [])
    {
        $this->id           = $args['id'] ?? null;
        $this->hora         = $args['hora'] ?? '';
        $this->cliente      = $args['cliente'] ?? '';
        $this->email        = $args['email'] ?? '';
        $this->telefono     = $args['telefono'] ?? '';
        $this->servicio     = $args['servicio'] ?? '';
        $this->precio       = $args['precio'] ?? '';
    }
}

?>
