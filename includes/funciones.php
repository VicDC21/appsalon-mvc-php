<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// 
function esUltimo(string $actual, string $proximo): bool {
    return $actual !== $proximo;
}

// Validar usuario autenticado
function isAuth() {
    if (!isset($_SESSION['nombre'])){
        header('Location: /');
    } 
}

// Validar que sean administradores
function isAdmin() {
    if (!isset($_SESSION['admin'])){
        header('Location: /');
    } 
}