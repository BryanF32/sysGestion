<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN, ROL_GERENTE]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }
$stmt = $db->prepare("SELECT * FROM gastos WHERE id=:id");
$stmt->execute([':id'=>$id]);
$g = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$g) { header("Location: lista.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE gastos SET proyecto_id=:pid, descripcion=:desc, monto=:monto, fecha=:fecha WHERE id=:id");
    $stmt->execute([':pid' => $_POST['proyecto_id'] ?: null, ':desc' => $_POST['descripcion'], ':monto' => $_POST['monto'], ':fecha' => $_POST['fecha'], ':id' => $id]);
    header("Location: lista.php?msg=actualizado");
    exit;
}

$proyectos = $db->query("SELECT id, nombre FROM proyectos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div><div class="page-title">Editar Gasto</div></div>
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
                        <option value="<?= $p['id'] ?>" <?= $g['proyecto_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= date('Y-m-d', strtotime($g['fecha'])) ?>">
            </div>
            <div class="form-group full">
                <label>Descripción</label>
                <input type="text" name="descripcion" value="<?= htmlspecialchars($g['descripcion']) ?>">
            </div>
            <div class="form-group">
                <label>Monto ($)</label>
                <input type="number" name="monto" min="0" step="0.01" value="<?= $g['monto'] ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
