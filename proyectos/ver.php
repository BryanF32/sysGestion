<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }

$stmt = $db->prepare("SELECT p.*, c.nombre AS cliente, c.empresa,
    u.nombre + ' ' + u.apellido AS gerente
    FROM proyectos p
    LEFT JOIN clientes c ON p.cliente_id = c.id
    LEFT JOIN usuarios u ON p.gerente_id = u.id
    WHERE p.id = :id");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) { header("Location: lista.php"); exit; }

// Tareas del proyecto
$tareas = $db->prepare("SELECT t.*, u.nombre + ' ' + u.apellido AS asignado FROM tareas t LEFT JOIN usuarios u ON t.asignado_a = u.id WHERE t.proyecto_id = :id ORDER BY t.creado_en DESC");
$tareas->execute([':id' => $id]);
$tareas = $tareas->fetchAll(PDO::FETCH_ASSOC);

// Gastos
$gastos = $db->prepare("SELECT * FROM gastos WHERE proyecto_id = :id ORDER BY fecha DESC");
$gastos->execute([':id' => $id]);
$gastos = $gastos->fetchAll(PDO::FETCH_ASSOC);
$total_gastos = array_sum(array_column($gastos, 'monto'));

// Tiempos (a través de tareas del proyecto)
$tiempos = $db->prepare("
    SELECT rt.*, u.nombre + ' ' + u.apellido AS usuario, t.nombre AS tarea
    FROM registro_tiempos rt
    JOIN tareas t ON rt.tarea_id = t.id
    LEFT JOIN usuarios u ON rt.usuario_id = u.id
    WHERE t.proyecto_id = :id
    ORDER BY rt.fecha DESC
");
$tiempos->execute([':id' => $id]);
$tiempos = $tiempos->fetchAll(PDO::FETCH_ASSOC);
$total_horas = array_sum(array_column($tiempos, 'horas_trabajadas'));
?>

<div class="page-header">
    <div>
        <div class="page-title"><?= htmlspecialchars($p['nombre']) ?></div>
        <div class="page-subtitle">
            <span class="badge badge-<?= $p['estado'] ?>"><?= str_replace('_', ' ', $p['estado']) ?></span>
            &nbsp;<span class="badge badge-<?= $p['prioridad'] ?>"><?= $p['prioridad'] ?></span>
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="editar.php?id=<?= $id ?>" class="btn btn-ghost">Editar</a>
        <a href="lista.php" class="btn btn-ghost">← Volver</a>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:20px;">
    <div class="stat-card blue">
        <div class="stat-label">Presupuesto</div>
        <div class="stat-value">$<?= number_format($p['presupuesto'], 0) ?></div>
    </div>
    <div class="stat-card <?= $total_gastos > $p['presupuesto'] ? 'pink' : 'orange' ?>">
        <div class="stat-label">Gasto Total</div>
        <div class="stat-value">$<?= number_format($total_gastos, 0) ?></div>
        <div class="stat-sub"><?= $p['presupuesto'] > 0 ? number_format($total_gastos/$p['presupuesto']*100,1).'% del presupuesto' : '' ?></div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Horas Trabajadas</div>
        <div class="stat-value"><?= number_format($total_horas, 1) ?>h</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">Avance</div>
        <div class="stat-value"><?= $p['porcentaje_avance'] ?>%</div>
    </div>
</div>

<div class="dashboard-grid">
    <div>
        <!-- Información general -->
        <div class="card">
            <div class="card-header"><span class="card-title">Información General</span></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div><div style="font-size:11px;color:var(--text3);margin-bottom:4px;">CLIENTE</div><div><?= htmlspecialchars($p['cliente'] ?? '—') ?><?= $p['empresa'] ? ' — '.$p['empresa'] : '' ?></div></div>
                <div><div style="font-size:11px;color:var(--text3);margin-bottom:4px;">GERENTE</div><div><?= htmlspecialchars($p['gerente'] ?? '—') ?></div></div>
                <div><div style="font-size:11px;color:var(--text3);margin-bottom:4px;">FECHA INICIO</div><div><?= $p['fecha_inicio'] ? date('d/m/Y', strtotime($p['fecha_inicio'])) : '—' ?></div></div>
                <div><div style="font-size:11px;color:var(--text3);margin-bottom:4px;">FECHA FIN</div><div><?= $p['fecha_fin'] ? date('d/m/Y', strtotime($p['fecha_fin'])) : '—' ?></div></div>
            </div>
            <?php if ($p['descripcion']): ?>
                <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                    <div style="font-size:11px;color:var(--text3);margin-bottom:6px;">DESCRIPCIÓN</div>
                    <div style="color:var(--text2);line-height:1.6;"><?= nl2br(htmlspecialchars($p['descripcion'])) ?></div>
                </div>
            <?php endif; ?>
            <div style="margin-top:16px;">
                <div style="font-size:11px;color:var(--text3);margin-bottom:8px;">PROGRESO GENERAL</div>
                <div class="progress-wrap" style="height:10px;"><div class="progress-bar" style="width:<?= $p['porcentaje_avance'] ?>%"></div></div>
            </div>
        </div>

        <!-- Tareas -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Tareas (<?= count($tareas) ?>)</span>
                <a href="../tareas/crear.php?proyecto_id=<?= $id ?>" class="btn btn-primary btn-sm">＋ Agregar</a>
            </div>
            <?php if (empty($tareas)): ?>
                <div class="empty-state">◎ <p>Sin tareas en este proyecto</p></div>
            <?php else: ?>
            <table>
                <thead><tr><th>Tarea</th><th>Asignado</th><th>Estado</th><th>H.Est</th><th>H.Real</th></tr></thead>
                <tbody>
                <?php foreach ($tareas as $t): ?>
                    <tr>
                        <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($t['nombre']) ?></td>
                        <td><?= htmlspecialchars($t['asignado'] ?? '—') ?></td>
                        <td><span class="badge badge-<?= $t['estado'] ?>"><?= str_replace('_', ' ', $t['estado']) ?></span></td>
                        <td><?= $t['horas_estimadas'] ?>h</td>
                        <td><?= $t['horas_reales'] ?>h</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <!-- Gastos -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Gastos</span>
                <a href="../gastos/crear.php?proyecto_id=<?= $id ?>" class="btn btn-ghost btn-sm">＋</a>
            </div>
            <?php if (empty($gastos)): ?>
                <div class="empty-state">◈ <p>Sin gastos</p></div>
            <?php else: ?>
                <?php foreach ($gastos as $g): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:13px;color:var(--text)"><?= htmlspecialchars($g['descripcion']) ?></div>
                        <div style="font-size:11px;color:var(--text3)"><?= date('d/m/Y', strtotime($g['fecha'])) ?></div>
                    </div>
                    <span style="font-weight:600;color:var(--accent4)">$<?= number_format($g['monto'], 2) ?></span>
                </div>
                <?php endforeach; ?>
                <div style="display:flex;justify-content:space-between;padding-top:10px;font-weight:600;">
                    <span style="color:var(--text2)">Total</span>
                    <span style="color:var(--accent4)">$<?= number_format($total_gastos, 2) ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Registro de tiempos -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">Tiempos Registrados</span>
                <a href="../tiempos/crear.php" class="btn btn-ghost btn-sm">＋</a>
            </div>
            <?php if (empty($tiempos)): ?>
                <div class="empty-state">◷ <p>Sin tiempos registrados</p></div>
            <?php else: ?>
                <?php foreach (array_slice($tiempos, 0, 6) as $rt): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-size:12px;color:var(--text)"><?= htmlspecialchars($rt['usuario'] ?? '—') ?></div>
                        <div style="font-size:11px;color:var(--text3)"><?= htmlspecialchars($rt['tarea']) ?> · <?= date('d/m/Y', strtotime($rt['fecha'])) ?></div>
                    </div>
                    <span style="font-weight:600;color:var(--accent3)"><?= $rt['horas_trabajadas'] ?>h</span>
                </div>
                <?php endforeach; ?>
                <div style="display:flex;justify-content:space-between;padding-top:10px;font-weight:600;">
                    <span style="color:var(--text2)">Total</span>
                    <span style="color:var(--accent3)"><?= number_format($total_horas, 1) ?>h</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
