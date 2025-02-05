<h1 class="nombre-pagina">Reestablece tu Password</h1>
<p class="descripcion-pagina">Ingresa tu nuevo password a continuación</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<?php if(!$error && !$exito): ?>
    <form class="formulario" method="POST">
        <div class="campo">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Tu password">
        </div>
        <input type="submit" value="Guardar Nuevo Password" class="boton">
    </form>
<?php endif; ?>

<div class="acciones">
    <?php if(!$exito): ?>
        <a href="/" class="enlace">¿Ya tienes una cuenta? Inicia Sesión</a>
        <a href="/crear-cuenta" class="enlace">¿No tienes cuenta? Regístrate</a>
    <?php else: ?>
        <a href="/" class="enlace">Volver al menú principal</a>
    <?php endif; ?>
</div>