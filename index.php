<?php
include("includes/header.php");
include("conexion.php");
$db = (new Cconexion())->conexionBD();

// Stats
$total_proyectos = $db->query("SELECT COUNT(*) FROM proyectos")->fetchColumn();
$proyectos_activos = $db->query("SELECT COUNT(*) FROM proyectos WHERE estado = 'en_progreso'")->fetchColumn();
$total_tareas = $db->query("SELECT COUNT(*) FROM tareas")->fetchColumn();
$tareas_pendientes = $db->query("SELECT COUNT(*) FROM tareas WHERE estado = 'pendiente'")->fetchColumn();
$total_horas = $db->query("SELECT ISNULL(SUM(horas_trabajadas),0) FROM registro_tiempos")->fetchColumn();
$total_gastos = $db->query("SELECT ISNULL(SUM(monto),0) FROM gastos")->fetchColumn();
$total_presupuesto = $db->query("SELECT ISNULL(SUM(presupuesto),0) FROM proyectos")->fetchColumn();

// Proyectos recientes
$proyectos_rec = $db->query("
    SELECT TOP 5 p.id, p.nombre, p.estado, p.prioridad, p.porcentaje_avance,
           p.fecha_fin, c.nombre AS cliente
    FROM proyectos p
    LEFT JOIN clientes c ON p.cliente_id = c.id
    ORDER BY p.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Tareas recientes
$tareas_rec = $db->query("
    SELECT TOP 5 t.nombre, t.estado, t.prioridad,
           p.nombre AS proyecto,
           u.nombre + ' ' + u.apellido AS asignado
    FROM tareas t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    LEFT JOIN usuarios u ON t.asignado_a = u.id
    ORDER BY t.creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Gastos por proyecto (top 5)
$gastos_proyecto = $db->query("
    SELECT TOP 5 p.nombre AS proyecto, SUM(g.monto) AS total
    FROM gastos g
    JOIN proyectos p ON g.proyecto_id = p.id
    GROUP BY p.nombre
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Resumen general del sistema</div>
    </div>
    <span style="color:var(--text3);font-size:12px;"><?= date('d/m/Y H:i') ?></span>
</div>

<div class="stats-grid">
    <div class="stat-card blue">
        <div class="stat-label">Proyectos Total</div>
        <div class="stat-value"><?= $total_proyectos ?></div>
        <div class="stat-sub"><?= $proyectos_activos ?> en progreso</div>
    </div>
    <div class="stat-card pink">
        <div class="stat-label">Tareas</div>
        <div class="stat-value"><?= $total_tareas ?></div>
        <div class="stat-sub"><?= $tareas_pendientes ?> pendientes</div>
    </div>
    <div class="stat-card green">
        <div class="stat-label">Horas Registradas</div>
        <div class="stat-value"><?= number_format($total_horas, 1) ?>h</div>
        <div class="stat-sub">Total acumulado</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">Presupuesto / Gasto</div>
        <div class="stat-value">$<?= number_format($total_gastos, 0) ?></div>
        <div class="stat-sub">de $<?= number_format($total_presupuesto, 0) ?> presup.</div>
    </div>
</div>

<div class="dashboard-grid">
    <div>
        <div class="card">
            <div class="card-header">
                <span class="card-title">Proyectos Recientes</span>
                <a href="proyectos/lista.php" class="btn btn-ghost btn-sm">Ver todos</a>
            </div>
            <div class="table-wrap">
                <?php if (empty($proyectos_rec)): ?>
                    <div class="empty-state">◉ <p>No hay proyectos aún</p></div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Proyecto</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th>Avance</th>
                            <th>Vence</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($proyectos_rec as $p): ?>
                        <tr>
                            <td><a href="proyectos/ver.php?id=<?= $p['id'] ?>" style="color:var(--text);text-decoration:none;font-weight:500;"><?= htmlspecialchars($p['nombre']) ?></a></td>
                            <td><?= htmlspecialchars($p['cliente'] ?? '—') ?></td>
                            <td><span class="badge badge-<?= $p['estado'] ?>"><?= str_replace('_', ' ', $p['estado']) ?></span></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="progress-wrap"><div class="progress-bar" style="width:<?= $p['porcentaje_avance'] ?>%"></div></div>
                                    <span style="font-size:11px;color:var(--text3)"><?= $p['porcentaje_avance'] ?>%</span>
                                </div>
                            </td>
                            <td><?= $p['fecha_fin'] ? date('d/m/Y', strtotime($p['fecha_fin'])) : '—' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Tareas Recientes</span>
                <a href="tareas/lista.php" class="btn btn-ghost btn-sm">Ver todas</a>
            </div>
            <div class="table-wrap">
                <?php if (empty($tareas_rec)): ?>
                    <div class="empty-state">◎ <p>No hay tareas aún</p></div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr><th>Tarea</th><th>Proyecto</th><th>Asignado</th><th>Estado</th><th>Prioridad</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tareas_rec as $t): ?>
                        <tr>
                            <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($t['nombre']) ?></td>
                            <td><?= htmlspecialchars($t['proyecto'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($t['asignado'] ?? '—') ?></td>
                            <td><span class="badge badge-<?= $t['estado'] ?>"><?= str_replace('_', ' ', $t['estado']) ?></span></td>
                            <td><span class="badge badge-<?= $t['prioridad'] ?>"><?= $t['prioridad'] ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-header"><span class="card-title">Gastos por Proyecto</span></div>
            <?php if (empty($gastos_proyecto)): ?>
                <div class="empty-state">◈ <p>Sin gastos registrados</p></div>
            <?php else: ?>
                <?php
                $max = max(array_column($gastos_proyecto, 'total'));
                foreach ($gastos_proyecto as $g):
                    $pct = $max > 0 ? ($g['total'] / $max * 100) : 0;
                ?>
                <div style="margin-bottom:14px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:12px;color:var(--text2)"><?= htmlspecialchars($g['proyecto']) ?></span>
                        <span style="font-size:12px;font-weight:500;color:var(--accent4)">$<?= number_format($g['total'], 2) ?></span>
                    </div>
                    <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct ?>%;background:linear-gradient(90deg,var(--accent4),var(--accent2))"></div></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Accesos Rápidos</span></div>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="proyectos/crear.php" class="btn btn-primary">＋ Nuevo Proyecto</a>
                <a href="tareas/crear.php" class="btn btn-ghost">＋ Nueva Tarea</a>
                <a href="tiempos/crear.php" class="btn btn-ghost">◷ Registrar Tiempo</a>
                <a href="gastos/crear.php" class="btn btn-ghost">◈ Registrar Gasto</a>
                <a href="clientes/crear.php" class="btn btn-ghost">◯ Nuevo Cliente</a>
            </div>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>
