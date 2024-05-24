<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Comprobar que exista el usuario.
                $usuario = Usuario::where('email', $auth->email);

                if($usuario){
                    // Verificar el password y que su cuenta esté comprobada
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        // Autenticar el usuario
                        if(!isset($_SESSION)){
                            session_start();// Inicia la sessión
                        }

                        $_SESSION['id'] = $usuario->id; // Guardar la id del usuario.
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido; // Guardar el nombre del usuario
                        $_SESSION['email'] = $usuario->email; // Guardar el email del usuario.
                        $_SESSION['login'] = true; // Decir que tiene el login true.

                        // Redireccionamiento
                        if($usuario->admin === "1"){
                            // Es Admin
                            $_SESSION['admin'] = $usuario->admin ?? null; // Decir que es admin o no

                            header('Location: /admin');

                        }else{
                            // Es usuario mortal
                            header('Location: /cita');
                        }
                    }
                } else{
                    // No existe ese correo
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas(); // Para obtener las alertas
        
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        // Autenticar el usuario
        if(!isset($_SESSION)){
            session_start();// Inicia la sessión
        }

        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === "1"){
                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                }else{
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                }
            }
        }
        
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router){

        $alertas= [];
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){

                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                
                $usuario->token = null;

                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function crear(Router $router){

        $usuario = new Usuario; // Creando una nueva instancia de usuario.

        $alertas = []; // Alertas Vacias.
    
        // Va a comproba si la solicitud HTTP recibida es del Método POST.
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $usuario->sincronizar($_POST); // Va a ir sincronizando el objeto vacio con los datos nuevos que van llegando por $_POST.
            $alertas = $usuario->validarNuevaCuenta(); // Va a guardar los errores que existan en la variable de $alertas.

            // Revisar que alertas esté vacío
            if(empty($alertas)){ // Si $alertas está vacío
                // Verificar que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{
                    // Hashear el Password
                    $usuario->hashPassword();

                    // Generar un Token único
                    $usuario->crearToken();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token); // Instanciado un nuevo email y pasando los valores
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();

                    if($resultado){
                        header('Location: /mensaje'); // Redireccionar al usuario cuando llene el formulario.
                    }
                }
            }
            
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario, // El objeto ya va a estar disponible en la vista
            'alertas' => $alertas // Pasamos las alertas hacia la vista
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){

        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if(empty($usuario) || $usuario->token === ''){
            // Si está vacío, mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Válido'); // Setear alertas
        } else{
            // Modificar a Usuario confirmado
            $usuario->confirmado = 1; // Cambia el estatús de confirmado a 1
            $usuario->token = ""; // Elimina el Token.
            $usuario->guardar(); // Llama el método de guardar para efectuar los cambios.
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas(); // Obtener alertas.

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}