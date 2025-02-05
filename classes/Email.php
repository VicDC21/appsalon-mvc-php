<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    public $mailer;
    public $nombre;
    public $email;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
        $this->mailer = new PHPMailer(true);
    }
    
    public function enviarConfirmacion() {
        try {
            $this->setData();
            $this->setConfirmationContent();
            $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    
    public function enviarRecuperacion() {
        try {
            $this->setData();
            $this->setRecuperationContent();
            $this->mailer->send();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function setData() {
        // Configurar SMTP
        $this->mailer->SMTPDebug = 0;
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['EMAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['EMAIL_USER'];
        $this->mailer->Password = $_ENV['EMAIL_PASS'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['EMAIL_PORT'];  

        // Configurar mails de origen y destino
        $this->mailer->setFrom($_ENV['EMAIL_USER'], 'AppSalon');
        $this->mailer->addAddress($this->email, $this->nombre);  
                
        // Habilitar HTML en el email
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    private function setConfirmationContent() {
        // Configurar el contenido del email
        $this->mailer->Subject = 'Confirma tu cuenta';

        // Definir el contenido del email
        $contenido = '<html>'; 
        $contenido .= '<p><strong>Hola ' . $this->nombre . '</strong>. ';
        $contenido .= 'Has creado tu cuenta en App Salon, solo debes confirmarla presionando el siguiente enlace</p>';
        $contenido .= '<p>Presiona aquí: <a href="' . $_ENV['APP_URL'] . '/confirmar-cuenta?token=' . $this->token . '">Confirmar Cuenta</a> </p>';
        $contenido .= '<p>Si no solicitaste esto, ignora este mensaje.</p>';
        $contenido .= '</html>';

        $this->mailer->Body = $contenido;
        $this->mailer->AltBody = "Texto alternativo de confirmación sin HTML";
    }

    private function setRecuperationContent() {
        // Configurar el contenido del email
        $this->mailer->Subject = 'Reestablece tu contraseña';

        // Definir el contenido del email
        $contenido = '<html>'; 
        $contenido .= '<p><strong>Hola ' . $this->nombre . '</strong>. ';
        $contenido .= 'Para reestablecer tu contraseña haz click en el siguiente enlace</p>';
        $contenido .= '<p>Presiona aquí: <a href="' . $_ENV['APP_URL'] . '/reestablecer-password?token=' . $this->token . '">Reestablece tu contraseña</a> </p>';
        $contenido .= '<p>Si no solicitaste esto, ignora este mensaje.</p>';
        $contenido .= '</html>';

        $this->mailer->Body = $contenido;
        $this->mailer->AltBody = "Texto alternativo de recuperación sin HTML";
    }
}