<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN, ROL_GERENTE]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }
$stmt = $db->prepare("SELECT * FROM clientes WHERE id=:id");
$stmt->execute([':id'=>$id]);
$c = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$c) { header("Location: lista.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE clientes SET nombre=:nombre, empresa=:empresa, telefono=:tel, email=:email WHERE id=:id");
    $stmt->execute([':nombre' => $_POST['nombre'], ':empresa' => $_POST['empresa'], ':tel' => $_POST['telefono'], ':email' => $_POST['email'], ':id' => $id]);
    header("Location: lista.php?msg=actualizado");
    exit;
}
?>

<div class="page-header">
    <div><div class="page-title">Editar Cliente</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($c['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label>Empresa</label>
                <input type="text" name="empresa" value="<?= htmlspecialchars($c['empresa']) ?>">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($c['telefono']) ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($c['email']) ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
