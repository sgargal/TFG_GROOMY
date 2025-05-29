<?php
namespace App\Models;
use Config\Conexion;

require_once __DIR__ . '/../config/Conexion.php';

use PDO;
use PDOException;
use Exception;

class Usuario {
    private $id;
    private $nombre;
    private $email;
    private $password;
    private $rol;
    private $imagen;
    private Conexion $db;


    public function __construct($id = null, $nombre = null, $email = null, $password = null, $rol = null, $imagen = null) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->password = $password;
        $this->rol = $rol;
        $this->imagen = $imagen;
        $this->db = new Conexion();
    }

    //Método para registrar un nuevo usuario
    public function registrar() {
        try {
            $this->db= new Conexion();
            $sql ="INSERT INTO usuarios (nombre, email, password, rol, imagen)
                    VALUES (:nombre, :email, :password, :rol, :imagen)";
            $stmt = $this->db->Conectar()->prepare($sql);
            $passwordHash = password_hash($this->password, PASSWORD_BCRYPT);

            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $passwordHash);
            $stmt->bindParam(':rol', $this->rol);
            $stmt->bindParam(':imagen', $this->imagen);

            $resultado = $stmt->execute();

            $this->db->cerrarBD();

            if($resultado) {
                return "Usuario registrado correctamente";
            }else {
                return "Error al registrar el usuario";
            }
        }catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    //Método para iniciar sesión
    public function login($email, $password) {
        try {
            $this->db = new Conexion();

            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $this->db->Conectar()->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->cerrarBD();

            if(!$usuario || !password_verify($password, $usuario['password'])) {
                return false;
            }
            return $usuario;
            
        }catch (PDOException $e) {
            return false;
        }
    }

    public function editarUsuario($id, $nombre, $email, $password, $img, $rol) {
        try {
            $this->db = new Conexion();

            if(!empty($password)) {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $sql = "UPDATE usuarios
                        SET nombre = :nombre, email = :email, password = :password, imagen = :imagen, rol = :rol
                        WHERE id = :id";
            }else {
                $sql = "UPDATE usuarios
                        SET nombre = :nombre, email = :email, imagen = :imagen, rol = :rol
                        WHERE id = :id";
            }

            $stmt = $this->db->Conectar()->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            if(!empty($password)) {
                $stmt->bindParam(':password', $passwordHash);
            }
            $stmt->bindParam(':imagen', $img);
            $stmt->bindParam(':rol', $rol);

            $resultado = $stmt->execute();
            $this->db->cerrarBD();
            if($resultado) {
                return "Perfil actualizado correctamente";
            } else {
                return "Error al actualizar el perfil";
            }
        }catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

}
?>