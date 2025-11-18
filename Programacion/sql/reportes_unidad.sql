CREATE TABLE reportes_unidad (
  idReporte INT AUTO_INCREMENT PRIMARY KEY,
  idUnidad INT NOT NULL,
  titulo VARCHAR(100) NOT NULL,
  descripcion TEXT NOT NULL,
  fecha_reporte DATETIME DEFAULT CURRENT_TIMESTAMP,
  archivo VARCHAR(255) DEFAULT NULL,
  FOREIGN KEY (idUnidad) REFERENCES unidades(idUnidad) ON DELETE CASCADE
);
