

CREATE TABLE comentarios (
    idComentario INT AUTO_INCREMENT PRIMARY KEY,
    idAviso INT NOT NULL,
    idUser INT NOT NULL,
    comentario TEXT NOT NULL,
    fecha_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idAviso) REFERENCES avisos(idAviso),
    FOREIGN KEY (idUser) REFERENCES usuarios(idUser)
);
