<?php

namespace Controllers;
use MVC\Router;
use Model\Servicio;

class ServicioController {
    public static function index(Router $router) {
        isAdmin();
        $servicios = Servicio::all();   
        $router->render('servicios/index', [
            'nombre'=> $_SESSION['nombre'],
            'servicios'=> $servicios
        ]);
    }

    public static function crear(Router $router) {
        isAdmin();
        self::postAndRender(new Servicio(), $router, 'crear');
    }

    public static function actualizar(Router $router) {
        isAdmin();
        if(!is_numeric($_GET['id'])) return;
        self::postAndRender(Servicio::find($_GET['id']), $router, 'actualizar');
    }

    public static function eliminar() {
        isAdmin();
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            Servicio::find($_POST['id'])->eliminar();
            header('Location: /servicios');
        }
    }

    private static function postAndRender(Servicio $servicio, Router $router, string $action){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();
            if(empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/'. $action, [
            'nombre'=> $_SESSION['nombre'],
            'servicio'=> $servicio,
            'alertas'=> $alertas
        ]);
    }
}