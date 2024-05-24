<?php

namespace Model;

class Usuario extends ActiveRecord{

    // Base de datos
    protected static $tabla = 'usuarios'; // La tabla en la que va a encontrar los datos.
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token']; // Son las columnas de la tabla de Usuarios.

    // Atributos para cada uno
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    // Constructor
    public function __construct($args = []){ // Va a tomar unos argumentos pero por default va a estar vacio.
        $this->id           = $args['id'] ?? null;
        $this->nombre       = $args['nombre'] ?? '';
        $this->apellido     = $args['apellido'] ?? '';
        $this->email        = $args['email'] ?? '';
        $this->password     = $args['password'] ?? '';
        $this->telefono     = $args['telefono'] ?? '';
        $this->admin        = $args['admin'] ?? '0';
        $this->confirmado   = $args['confirmado'] ?? '0';
        $this->token   = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta(){
        if(!$this->nombre){ // En caso de que no esté presente el nombre
            self::$alertas['error'][] = "El Nombre es obligatorio";
        }

        if(!$this->apellido){ // En caso de que no esté presente el apellido
            self::$alertas['error'][] = "El Apellido es obligatorio";
        }

        if(!$this->telefono){ // En caso de que no esté presente el teléfono
            self::$alertas['error'][] = "El Teléfono es obligatorio";
        }

        if(!$this->email){ // En caso de que no esté presente el email
            self::$alertas['error'][] = "El Email es obligatorio";
        }

        if(!$this->password){ // En caso de que no esté presente el password
            self::$alertas['error'][] = "El Password es obligatorio";
        }

        if(strlen($this->password) < 6){ // En caso de que la contraseña sea menor a 6 carácteres
            self::$alertas['error'][] = "El Password tiene que ser minimo 6 carácteres";
        }

        return self::$alertas;
    }

    public function validarLogin(){

        if(!$this->email){
            self::$alertas['error'][] = 'El email es Obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El password es Obligatorio';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El email es Obligatorio';
        }

        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe tener al menos 6 carácteres';
        }

        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario(){
        $query = "SELECT  * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1;"; // SELECT * FROM usuarios WHERE email = '$email' LIMIT 1;

        $resultado = self::$db->query($query); // Aplicamos la consulta a la base de datos

        if($resultado->num_rows){ // Si hay un resultado, significa que ya hay una persona registrada con ese email
            self::$alertas['error'][] = "El Usuario ya está registrado"; // Agrega a las alertas de error
        }

        return $resultado; // Retornamos el resultado
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT); // Seleccionamos la contraseña que está en el atributo guardada y mandamos llamar al método para hashear.
    }

    public function crearToken(){
        $this->token = uniqid(); // En el Token le vamos a asignar una combinación de 13 digitos
    }

    public function comprobarPasswordAndVerificado($password){
        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado){
            // El usuario no está confirmado
            self::$alertas['error'][] = 'Credenciales incorrectas o falta confirmar tu cuenta';
        }else{
            // El usuario está confirmado
            return true; // Regresamos que está confirmado
        }
    }

}