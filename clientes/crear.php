<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("INSERT INTO clientes (nombre, empresa, telefono, email, creado_en) VALUES (:nombre, :empresa, :tel, :email, GETDATE())");
    $stmt->execute([':nombre' => trim($_POST['nombre']), ':empresa' => $_POST['empresa'], ':tel' => $_POST['telefono'], ':email' => $_POST['email']]);
    header("Location: lista.php?msg=creado");
    exit;
}
?>

<div class="page-header">
    <div><div class="page-title">Nuevo Cliente</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Nombre *</label>
                <input type="text" name="nombre" placeholder="Nombre del contacto" required>
            </div>
            <div class="form-group">
                <label>Empresa</label>
                <input type="text" name="empresa" placeholder="Nombre de la empresa">
            </div>
            <div class="form-group">
                <label>Teléfono</label>
                <input type="text" name="telefono" placeholder="+503 0000-0000">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="correo@empresa.com">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cliente</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
