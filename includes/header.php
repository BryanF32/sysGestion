<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/SistemaG/includes/auth.php';
$pagina_actual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestPro — Sistema de Gestión</title>
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
        <?php if (tienePermiso([ROL_ADMIN, ROL_GERENTE])): ?>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'proyectos') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/proyectos/lista.php"><span class="nav-icon">◉</span> Proyectos</a>
        </li>
        <?php endif; ?>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'tareas') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/tareas/lista.php"><span class="nav-icon">◎</span> Tareas</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'tiempos') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/tiempos/lista.php"><span class="nav-icon">◷</span> Tiempos</a>
        </li>
        <?php if (tienePermiso([ROL_ADMIN, ROL_GERENTE])): ?>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'gastos') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/gastos/lista.php"><span class="nav-icon">◈</span> Gastos</a>
        </li>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'clientes') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/clientes/lista.php"><span class="nav-icon">◯</span> Clientes</a>
        </li>
        <?php endif; ?>
        <?php if (tienePermiso([ROL_ADMIN])): ?>
        <li class="nav-item <?= strpos($_SERVER['PHP_SELF'], 'usuarios') !== false ? 'active' : '' ?>">
            <a href="/SistemaG/usuarios/lista.php"><span class="nav-icon">◍</span> Usuarios</a>
        </li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <div style="font-size:12px;color:var(--text2);margin-bottom:6px;font-weight:500;"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></div>
        <div style="font-size:11px;color:var(--text3);margin-bottom:10px;"><?= htmlspecialchars($_SESSION['rol_nombre']) ?></div>
        <a href="/SistemaG/logout.php" style="font-size:11px;color:var(--accent2);text-decoration:none;">⏻ Cerrar sesión</a>
    </div>
</nav>
<main class="main-content">
