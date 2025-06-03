CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    imagen VARCHAR(255),
    rol ENUM('admin', 'barberia', 'user') DEFAULT 'user'
);

CREATE TABLE barberia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    direccion VARCHAR(255),
    informacion VARCHAR(255),
    usuario_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id)
);

CREATE TABLE barbero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    informacion VARCHAR(255),
    imagen VARCHAR(255),
    id_barberia INT,
    FOREIGN KEY (id_barberia) REFERENCES barberia(id)
);

CREATE TABLE horario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barberia INT,
    dia ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'),
    hora_inicio TIME,
    hora_fin TIME,
    FOREIGN KEY (id_barberia) REFERENCES barberia(id)
);

CREATE TABLE servicio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    precio DECIMAL(10, 2),
    id_barberia INT,
    FOREIGN KEY (id_barberia) REFERENCES barberia(id)
);

CREATE TABLE cita (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_barbero INT,
    id_servicio INT,
    metodo_pago ENUM('paypal', 'efectivo'),
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
    fecha_hora DATETIME,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id),
    FOREIGN KEY (id_barbero) REFERENCES barbero(id),
    FOREIGN KEY (id_servicio) REFERENCES servicio(id)
);

CREATE TABLE redes_sociales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barberia INT,
    nombre_red_social VARCHAR(255),
    url VARCHAR(255),
    FOREIGN KEY (id_barberia) REFERENCES barberia(id)
);

