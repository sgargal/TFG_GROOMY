<?php 
namespace App\Controllers;

require_once __DIR__ . '/../models/Barberia.php';
use App\Models\Barberia;

class BarberiaController {
    public function registrar() {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if(!$nombre || !$email || !$password) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Todos los campos son obligatorios'
            ]);
            return;
        }

        $barberiaModel = new Barberia();
        $resultado = $barberiaModel->registrar($nombre, $email, $password);

        if($resultado) {
            echo json_encode([
                'success' => true,
                'mensaje' => 'Barbería registrada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error al registrar la barbería'
            ]);
        }
    }
}