<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Si no está logueado, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: /SistemaG/login.php");
    exit;
}

// Roles
define('ROL_ADMIN',    1);
define('ROL_GERENTE',  2);
define('ROL_EMPLEADO', 3);

$_rol_actual = $_SESSION['rol_id'];

// Función para verificar si tiene permiso
function tienePermiso($roles_permitidos) {
    global $_rol_actual;
    return in_array($_rol_actual, (array)$roles_permitidos);
}

// Función para redirigir si no tiene permiso
function requiereRol($roles_permitidos) {
    if (!tienePermiso($roles_permitidos)) {
        header("Location: /SistemaG/index.php?error=sin_permiso");
        exit;
    }
}

// Permisos por módulo
// Administrador (1): todo
// Gerente (2): proyectos, tareas, tiempos, gastos, clientes — NO usuarios
// Empleado (3): solo tareas y tiempos — solo los suyos

$permisos = [
    'proyectos' => [ROL_ADMIN, ROL_GERENTE],
    'tareas'    => [ROL_ADMIN, ROL_GERENTE, ROL_EMPLEADO],
    'tiempos'   => [ROL_ADMIN, ROL_GERENTE, ROL_EMPLEADO],
    'gastos'    => [ROL_ADMIN, ROL_GERENTE],
    'clientes'  => [ROL_ADMIN, ROL_GERENTE],
    'usuarios'  => [ROL_ADMIN],
];
?>
