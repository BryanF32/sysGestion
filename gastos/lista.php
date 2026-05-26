<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN, ROL_GERENTE]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$gastos = $db->query("
    SELECT g.*, p.nombre AS proyecto
    FROM gastos g
    LEFT JOIN proyectos p ON g.proyecto_id = p.id
    ORDER BY g.fecha DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total = array_sum(array_column($gastos, 'monto'));
?>

<div class="page-header">
    <div>
        <div class="page-title">Gastos</div>
        <div class="page-subtitle"><?= count($gastos) ?> registros · Total: $<?= number_format($total, 2) ?></div>
    </div>
    <a href="crear.php" class="btn btn-primary">＋ Registrar Gasto</a>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($gastos)): ?>
            <div class="empty-state">◈ <p>No hay gastos registrados</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Fecha</th><th>Proyecto</th><th>Descripción</th><th>Monto</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($gastos as $g): ?>
                <tr>
                    <td style="color:var(--text3)"><?= $g['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($g['fecha'])) ?></td>
                    <td><a href="../proyectos/ver.php?id=<?= $g['proyecto_id'] ?>" style="color:var(--accent);text-decoration:none;"><?= htmlspecialchars($g['proyecto'] ?? '—') ?></a></td>
                    <td style="color:var(--text)"><?= htmlspecialchars($g['descripcion']) ?></td>
                    <td><span style="font-weight:600;color:var(--accent4)">$<?= number_format($g['monto'], 2) ?></span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="editar.php?id=<?= $g['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?= $g['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar gasto?')">Eliminar</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
