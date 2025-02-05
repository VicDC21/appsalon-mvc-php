<?php

namespace Model;

class Usuario extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        $this->validarCamposObligatorios(['nombre', 'apellido', 'telefono']);
        $this->validarEmail();
        $this->validarPassword();
        
        return self::$alertas;
    }

    public function validarLogin() {
        $this->validarCamposObligatorios(['email', 'password']);
        return self::$alertas;
    }

    public function validarEmail() {
        $this->validarCamposObligatorios(['email']);
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = "El email es inválido";
        }
        return self::$alertas;
    }

    public function validarPassword() {
        $this->validarCamposObligatorios(['password']);
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = "La contraseña debe contener al menos 6 caracteres";
        }
        return self::$alertas;
    }

    // Verificar si el usuario ya existe
    public function existeUsuario() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = "El email ya está registrado";
        }

        return $resultado;
    }   

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this->token = uniqid();
    }

    public function checkPasswordAndValidatedUser($password) {
        return password_verify($password, $this->password) && $this->confirmado;
    }
}