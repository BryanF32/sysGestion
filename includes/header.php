<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina_actual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/SistemaG/assets/css/estilo.css">
</head>
<body>
<nav class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">⬡</span>
        <span class="logo-text">GestPro</span>
    </div>
    <ul class="nav-menu">
        <li class="nav-item <?= $pagina_actual == 'index' ? 'active' : '' ?>">
            <a href="/SistemaG/index.php"><span class="nav-icon">◈</span> Dashboard</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'proyectos') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/proyectos/lista.php"><span class="nav-icon">◉</span> Proyectos</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'tareas') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/tareas/lista.php"><span class="nav-icon">◎</span> Tareas</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'tiempos') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/tiempos/lista.php"><span class="nav-icon">◷</span> Tiempos</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'gastos') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/gastos/lista.php"><span class="nav-icon">◈</span> Gastos</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'clientes') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/clientes/lista.php"><span class="nav-icon">◯</span> Clientes</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/usuarios/lista.php"><span class="nav-icon">◍</span> Usuarios</a>
        </li>
    </ul>
    <div class="sidebar-footer"><span>v1.0.0</span></div>
</nav>
<main class="main-content"></main>