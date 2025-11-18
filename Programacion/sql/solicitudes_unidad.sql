CREATE TABLE solicitudes_unidad (
    idSolicitud INT AUTO_INCREMENT PRIMARY KEY,
    idUser INT NOT NULL,
    idUnidad INT NOT NULL,
    estado ENUM('pendiente', 'aprobada', 'rechazada') DEFAULT 'pendiente',
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUser) REFERENCES usuarios(idUser),
    FOREIGN KEY (idUnidad) REFERENCES unidades_habitacionales(idUnidad)
);
