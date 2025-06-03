<?php
namespace Config;

use PDO;
use PDOException;

class Conexion { 
    private $host = 'localhost';
    private $dbName = 'groomydb';
    private $user = 'root';
    private $pass = '';
    private $conexion;

    public function __construct() {
        $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

        try {
            $this->conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->dbName}",
                $this->user,
                $this->pass,
                $opciones
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'mensaje' => 'Error de conexión a la base de datos: ' . $e->getMessage()
            ]);
            exit;
        } 
    }

    // Método para obtener la conexión
    public function Conectar() {
        return $this->conexion;
    }

    // Método para cerrar la conexión
    public function cerrarBD(): void {
        $this->conexion = null;
    }
}

?>