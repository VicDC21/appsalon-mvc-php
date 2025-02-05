<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php 
    include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Tu Email">
    </div>
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Tu Password">
    </div>
    <input type="submit" value="Iniciar sesión" class="boton">
</form>

<div class="acciones">
    <a href="/crear-cuenta" class="enlace">¿No tienes cuenta? Regístrate</a>
    <a href="/olvide" class="enlace">¿Olvidaste tu Password?</a>
</div>