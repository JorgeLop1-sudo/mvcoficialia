<style>
    .notifi-box{

    }
</style>

<div class="header">
    <h2 class="mb-0">Dashboard</h2>
    <div class="user-info">
        <?php if ($_SESSION['tipo_usuario'] === 'Administrador' || $_SESSION['tipo_usuario'] === 'Usuario'): ?>
            <div class="notifi-box">
                <span class="material-symbols-rounded">notifications</span>
            </div>
        <?php endif; ?>
        <div>
            <div class="fw-bold"><?php echo $_SESSION['nombre']; ?></div>
            <div class="small"><?php echo $_SESSION['tipo_usuario']; ?></div>
        </div>
    </div>
</div>