<?php

namespace Controllers;
use Model\Usuario;
use MVC\Router;
use Classes\Email;

class LoginController {
    public static function login(Router $router) {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            // Validar
            $alertas = $auth->validarLogin();

            if(empty($alertas)) {
                // Verificar si el usuario existe
                $usuario = Usuario::where('email', $auth->email);
                if($usuario) {
                    // Verificar si la contraseña es correcta
                    if($usuario->checkPasswordAndValidatedUser($auth->password)) {
                        // Autenticar al usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        if($usuario->admin === '0') {
                            header('Location: /cita');
                        } else {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        }
                    } else {
                        Usuario::setAlerta('error', 'La contraseña es incorrecta o la cuenta no ha sido confirmada');
                    }
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'alertas'=> $alertas
        ]);
    }

    public static function logout() {
        session_unset();
        header('Location: /');
    }

    public static function olvide(Router $router) {
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === '1') {
                    // Generar un token único
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email de recuperación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarRecuperacion();

                    // Alerta de éxito
                    Usuario::setAlerta('exito', 'Revisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no ha sido confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/olvide', [
            'alertas'=> $alertas
        ]);
    }

    public static function reestablecer(Router $router) {
        $alertas = [];  
        $error = false;
        $exito = false;
        $usuario= Usuario::where('token', s($_GET['token']));
        
        if(empty($usuario)){
            Usuario::setAlerta('error','Token inválido');
            $error = true;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();
            
            if(empty($alertas)) {
                // Hashear la contraseña
                $usuario->hashPassword();
                $usuario->token = '';
                $usuario->guardar();

                // Alerta de éxito
                Usuario::setAlerta('exito', 'Password actualizado');
                $alertas = Usuario::getAlertas();
                $exito = true;
            }
        }


        $alertas = Usuario::getAlertas();
        $router->render('auth/reestablecer-password', [
            'alertas'=> $alertas,
            'error'=> $error,
            'exito'=> $exito
        ]);    
    }

    public static function crear(Router $router) {
        $usuario = new Usuario;

        // Alertas vacías
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Si no hay alertas, crear la cuenta
            if(empty($alertas)) {
                // Verificar si el usuario ya existe
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear la contraseña
                    $usuario->hashPassword();

                    // Generar un token único
                    $usuario->crearToken();

                    // Enviar el email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                                 
                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if($resultado) {
                        // Redireccionar
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario'=> $usuario,
            'alertas'=> $alertas
        ]);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje', []);
    }

    public static function confirmar(Router $router){
        $alertas = [];
        $usuario = Usuario::where('token', $_GET['token']);
        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token inválido');
        } else {
            // Modificar el usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = '';
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta confirmada');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();
        
        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas'=> $alertas
        ]);
    }
}