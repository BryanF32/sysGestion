<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN, ROL_GERENTE]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$clientes = $db->query("
    SELECT c.*, COUNT(p.id) AS total_proyectos
    FROM clientes c
    LEFT JOIN proyectos p ON p.cliente_id = c.id
    GROUP BY c.id, c.nombre, c.empresa, c.telefono, c.email, c.creado_en
    ORDER BY c.nombre
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Clientes</div>
        <div class="page-subtitle"><?= count($clientes) ?> cliente(s)</div>
    </div>
    <a href="crear.php" class="btn btn-primary">＋ Nuevo Cliente</a>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($clientes)): ?>
            <div class="empty-state">◯ <p>No hay clientes registrados</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Nombre</th><th>Empresa</th><th>Teléfono</th><th>Email</th><th>Proyectos</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td style="color:var(--text3)"><?= $c['id'] ?></td>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($c['nombre']) ?></td>
                    <td><?= htmlspecialchars($c['empresa'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['telefono'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '—') ?></td>
                    <td><span class="badge badge-active"><?= $c['total_proyectos'] ?> proyectos</span></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="editar.php?id=<?= $c['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar cliente?')">Eliminar</a>
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
