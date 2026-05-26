<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id=:id");
$stmt->execute([':id'=>$id]);
$u = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$u) { header("Location: lista.php"); exit; }
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $params = [
        ':nombre' => trim($_POST['nombre']),
        ':apellido' => trim($_POST['apellido']),
        ':email' => trim($_POST['email']),
        ':rol' => $_POST['rol_id'] ?: null,
        ':activo' => isset($_POST['activo']) ? 1 : 0,
        ':id' => $id
    ];
    $sql = "UPDATE usuarios SET nombre=:nombre, apellido=:apellido, email=:email, rol_id=:rol, activo=:activo";
    if (!empty($_POST['password'])) {
        $sql .= ", password_hash=:hash";
        $params[':hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    $sql .= " WHERE id=:id";
    $db->prepare($sql)->execute($params);
    header("Location: lista.php?msg=actualizado");
    exit;
}

$roles = $db->query("SELECT id, nombre FROM roles ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div><div class="page-title">Editar Usuario</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>">
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" value="<?= htmlspecialchars($u['apellido']) ?>">
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>">
            </div>
            <div class="form-group">
                <label>Nueva Contraseña <span style="color:var(--text3)">(dejar vacío para no cambiar)</span></label>
                <input type="password" name="password" placeholder="Nueva contraseña">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol_id">
                    <option value="">— Sin rol —</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $u['rol_id'] == $r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="justify-content:flex-end;padding-top:20px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="activo" <?= $u['activo'] ? 'checked' : '' ?> style="width:auto;"> Usuario Activo
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
