<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();

// Si ya está logueado, redirigir
if (isset($_SESSION['usuario_id'])) {
    header("Location: /SistemaG/index.php");
    exit;
}

include("conexion.php");
$db = (new Cconexion())->conexionBD();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $db->prepare("SELECT u.*, r.nombre AS rol_nombre FROM usuarios u LEFT JOIN roles r ON u.rol_id = r.id WHERE u.email = :email AND u.activo = 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Verificar contraseña (soporta hash y texto plano para migración)
        $ok = false;
        if (password_verify($password, $usuario['password_hash'])) {
            $ok = true;
        } elseif ($password === $usuario['password_hash']) {
            // Contraseña en texto plano — hashearla automáticamente
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $db->prepare("UPDATE usuarios SET password_hash=:hash WHERE id=:id")->execute([':hash'=>$hash, ':id'=>$usuario['id']]);
            $ok = true;
        }

        if ($ok) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['rol_id'] = $usuario['rol_id'];
            $_SESSION['rol_nombre'] = $usuario['rol_nombre'];
            header("Location: /SistemaG/index.php");
            exit;
        }
    }
    $error = 'Email o contraseña incorrectos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestPro — Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0a0a0f;
            color: #e8e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 24px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-logo .icon { font-size: 36px; color: #6c63ff; }
        .login-logo h1 {
            font-family: 'Syne', sans-serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-top: 8px;
        }
        .login-logo p { color: #9090a8; font-size: 13px; margin-top: 4px; }
        .card {
            background: #16161f;
            border: 1px solid #2a2a3a;
            border-radius: 12px;
            padding: 28px;
        }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: 12px; color: #9090a8; margin-bottom: 6px; font-weight: 500; }
        input {
            width: 100%;
            background: #1a1a24;
            border: 1px solid #2a2a3a;
            border-radius: 8px;
            padding: 10px 14px;
            color: #e8e8f0;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            transition: border-color 0.15s;
        }
        input:focus { border-color: #6c63ff; }
        .btn {
            width: 100%;
            background: #6c63ff;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.15s;
        }
        .btn:hover { background: #5a52e0; }
        .alert {
            background: rgba(255,101,132,0.1);
            border: 1px solid rgba(255,101,132,0.2);
            color: #ff6584;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 16px;
        }
        .roles-info {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #2a2a3a;
            font-size: 11px;
            color: #5a5a72;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-wrap">
    <div class="login-logo">
        <div class="icon">⬡</div>
        <h1>GestPro</h1>
        <p>Sistema de Gestión de Proyectos</p>
    </div>
    <div class="card">
        <?php if ($error): ?>
            <div class="alert">✕ <?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" name="email" placeholder="usuario@empresa.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
    </div>
</div>
</body>
</html>
