<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$tiempos = $db->query("
    SELECT rt.*, u.nombre + ' ' + u.apellido AS usuario,
           t.nombre AS tarea, p.nombre AS proyecto
    FROM registro_tiempos rt
    LEFT JOIN usuarios u ON rt.usuario_id = u.id
    LEFT JOIN tareas t ON rt.tarea_id = t.id
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    ORDER BY rt.fecha DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_horas = array_sum(array_column($tiempos, 'horas_trabajadas'));
?>

<div class="page-header">
    <div>
        <div class="page-title">Registro de Tiempos</div>
        <div class="page-subtitle"><?= count($tiempos) ?> registros · Total: <?= number_format($total_horas, 1) ?>h</div>
    </div>
    <a href="crear.php" class="btn btn-primary">＋ Registrar Tiempo</a>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($tiempos)): ?>
            <div class="empty-state">◷ <p>No hay tiempos registrados</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Fecha</th><th>Usuario</th><th>Tarea</th><th>Proyecto</th><th>Horas</th><th>Descripción</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($tiempos as $rt): ?>
                <tr>
                    <td style="color:var(--text3)"><?= $rt['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($rt['fecha'])) ?></td>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($rt['usuario'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($rt['tarea'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($rt['proyecto'] ?? '—') ?></td>
                    <td><span style="font-weight:600;color:var(--accent3)"><?= $rt['horas_trabajadas'] ?>h</span></td>
                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($rt['descripcion'] ?? '—') ?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="editar.php?id=<?= $rt['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?= $rt['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar?')">Eliminar</a>
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
