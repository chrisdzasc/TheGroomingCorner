<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\AdminController;
use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\ServicioController;
use MVC\Router;

$router = new Router();

// ----- INICIAR Y CERRAR SESIÓN. -----
$router->get('/', [LoginController::class, 'login']); // Toma una URL y la función correspondiente definida en el controlador.
$router->post('/', [LoginController::class, 'login']); // Toma una URL y la función correspondiente definida en el controlador.
$router->get('/logout', [LoginController::class, 'logout']); // Toma una URL y la función correspondiente definida en el controlador.

// ----- RECUPERAR PASSWORD -----
$router->get('/olvide', [LoginController::class, 'olvide']); // Toma una URL y la función correspondiente definida en el controlador
$router->post('/olvide', [LoginController::class, 'olvide']); // Toma una URL y la función correspondiente definida en el controlador
$router->get('/recuperar', [LoginController::class, 'recuperar']); // Toma una URL y la función correspondiente definida en el controlador
$router->post('/recuperar', [LoginController::class, 'recuperar']); // Toma una URL y la función correspondiente definida en el controlador

// ----- CREAR LAS CUENTAS -----
$router->get('/crear-cuenta', [LoginController::class, 'crear']); // Toma una URL y la función correspondiente definida en el controlador
$router->post('/crear-cuenta', [LoginController::class, 'crear']); // Toma una URL y la función correspondiente definida en el controlador

// ----- CONFIRMAR LAS CUENTAS -----
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// ----- AREA PRIVADA-----
$router->get('/cita', [CitaController::class, 'index']);
$router->get('/admin', [AdminController::class, 'index']);

// ----- API -----
$router->get('/api/servicios', [APIController::class, 'index']); // Nos va a listar todos los servicios de la BD.
$router->post('/api/citas', [APIController::class, 'guardar']);  // Para mandar los datos de las citas
$router->post('/api/eliminar', [APIController::class, 'eliminar']); // Para eliminar una cita

// ----- CRUD de Servicios -----
$router->get('/servicios', [ServicioController::class, 'index']); // Es el que va a mostrar todos 
$router->get('/servicios/crear', [ServicioController::class, 'crear']); // Para crear los servicios
$router->post('/servicios/crear', [ServicioController::class, 'crear']); // Para crear los servicios
$router->get('/servicios/actualizar', [ServicioController::class, 'actualizar']); // Actualizar un servicio
$router->post('/servicios/actualizar', [ServicioController::class, 'actualizar']); // Actualizar un servicio
$router->post('/servicios/eliminar', [ServicioController::class, 'eliminar']); // Eliminar un servicio

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();