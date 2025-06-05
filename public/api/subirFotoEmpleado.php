<?php
header('Content-Type: application/json');

if (!isset($_FILES['imagenEmpleado']) || $_FILES['imagenEmpleado']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'mensaje' => 'No se recibió imagen']);
    exit;
}

$img = $_FILES['imagenEmpleado'];
$nombre = uniqid() . "_" . basename($img['name']);
$rutaDestino = "../../assets/src/barberos/" . $nombre;

if (!is_dir("../../assets/src/barberos")) {
    mkdir("../../assets/src/barberos", 0777, true);
}

if (move_uploaded_file($img['tmp_name'], $rutaDestino)) {
    echo json_encode([
        'success' => true,
        'ruta' => "assets/src/barberos/" . $nombre
    ]);
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Error al mover la imagen']);
}
