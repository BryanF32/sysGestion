<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$nombre || !$email || !$password) {
        $error = 'Nombre, email y contraseña son requeridos.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO usuarios (nombre, apellido, email, password_hash, rol_id, activo, creado_en)
                VALUES (:nombre, :apellido, :email, :hash, :rol, :activo, GETDATE())");
            $stmt->execute([
                ':nombre' => $nombre, ':apellido' => $apellido,
                ':email' => $email, ':hash' => $hash,
                ':rol' => $_POST['rol_id'] ?: null,
                ':activo' => isset($_POST['activo']) ? 1 : 0
            ]);
            header("Location: lista.php?msg=creado");
            exit;
        } catch (PDOException $e) {
            $error = 'El email ya está registrado.';
        }
    }
}

$roles = $db->query("SELECT id, nombre FROM roles ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div><div class="page-title">Nuevo Usuario</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<?php if ($error): ?><div class="alert alert-error">✕ <?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" placeholder="Bryan">
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" placeholder="Fuentes">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" placeholder="usuario@empresa.com">
            </div>
            <div class="form-group">
                <label>Contraseña *</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol_id">
                    <option value="">— Sin rol —</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="justify-content:flex-end;padding-top:20px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="activo" checked style="width:auto;"> Usuario Activo
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Crear Usuario</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
