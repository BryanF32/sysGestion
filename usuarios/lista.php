<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$usuarios = $db->query("
    SELECT u.*, r.nombre AS rol
    FROM usuarios u
    LEFT JOIN roles r ON u.rol_id = r.id
    ORDER BY u.nombre
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Usuarios</div>
        <div class="page-subtitle"><?= count($usuarios) ?> usuario(s)</div>
    </div>
    <a href="crear.php" class="btn btn-primary">＋ Nuevo Usuario</a>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($usuarios)): ?>
            <div class="empty-state">◍ <p>No hay usuarios registrados</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Registrado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td style="color:var(--text3)"><?= $u['id'] ?></td>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['rol'] ?? '—') ?></td>
                    <td><span class="badge <?= $u['activo'] ? 'badge-active' : 'badge-cancelado' ?>"><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
                    <td><?= $u['creado_en'] ? date('d/m/Y', strtotime($u['creado_en'])) : '—' ?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="editar.php?id=<?= $u['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar usuario?')">Eliminar</a>
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
