<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$proyecto_presel = intval($_GET['proyecto_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("INSERT INTO gastos (proyecto_id, descripcion, monto, fecha) VALUES (:pid, :desc, :monto, :fecha)");
    $stmt->execute([':pid' => $_POST['proyecto_id'] ?: null, ':desc' => $_POST['descripcion'], ':monto' => $_POST['monto'], ':fecha' => $_POST['fecha']]);
    header("Location: lista.php?msg=creado");
    exit;
}

$proyectos = $db->query("SELECT id, nombre FROM proyectos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div><div class="page-title">Registrar Gasto</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Proyecto</label>
                <select name="proyecto_id">
                    <option value="">— Sin proyecto —</option>
                    <?php foreach ($proyectos as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $proyecto_presel == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group full">
                <label>Descripción *</label>
                <input type="text" name="descripcion" placeholder="Ej: Licencia software, Materiales, etc.">
            </div>
            <div class="form-group">
                <label>Monto ($) *</label>
                <input type="number" name="monto" min="0" step="0.01" value="0" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Gasto</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
