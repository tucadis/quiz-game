-- Base de datos para la plataforma de minijuegos
CREATE DATABASE IF NOT EXISTS minijuegos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE minijuegos_db;

-- Tabla de sesiones
CREATE TABLE IF NOT EXISTS sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(6) UNIQUE NOT NULL,
    nombre_instructor VARCHAR(100) NOT NULL,
    juego_actual VARCHAR(50) DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activa BOOLEAN DEFAULT TRUE,
    INDEX idx_codigo (codigo),
    INDEX idx_activa (activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios/participantes
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    nickname VARCHAR(50) NOT NULL,
    es_instructor BOOLEAN DEFAULT FALSE,
    fecha_union TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para nube de palabras
CREATE TABLE IF NOT EXISTS wordcloud (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    palabra VARCHAR(200) NOT NULL,
    contador INT DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id),
    UNIQUE KEY unique_palabra_sesion (sesion_id, palabra)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para encuestas
CREATE TABLE IF NOT EXISTS encuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    pregunta TEXT NOT NULL,
    opciones JSON NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion_activa (sesion_id, activa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para votos de encuestas
CREATE TABLE IF NOT EXISTS votos_encuesta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    encuesta_id INT NOT NULL,
    usuario_id INT NOT NULL,
    opcion_index INT NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (encuesta_id) REFERENCES encuestas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_voto (encuesta_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para ideas/post-its
CREATE TABLE IF NOT EXISTS ideas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    usuario_id INT NOT NULL,
    texto TEXT NOT NULL,
    color VARCHAR(7) DEFAULT '#ffeb3b',
    posicion_x DECIMAL(5,2) DEFAULT 0,
    posicion_y DECIMAL(5,2) DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para ranking/votación
CREATE TABLE IF NOT EXISTS ranking_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    texto VARCHAR(255) NOT NULL,
    votos INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para votos de ranking
CREATE TABLE IF NOT EXISTS votos_ranking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ranking_item_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_voto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ranking_item_id) REFERENCES ranking_items(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_voto_ranking (ranking_item_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para trivia
CREATE TABLE IF NOT EXISTS trivia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    preguntas JSON NOT NULL,
    pregunta_actual INT DEFAULT 0,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para respuestas de trivia
CREATE TABLE IF NOT EXISTS respuestas_trivia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trivia_id INT NOT NULL,
    usuario_id INT NOT NULL,
    pregunta_index INT NOT NULL,
    respuesta INT NOT NULL,
    puntos INT DEFAULT 0,
    tiempo_respuesta INT DEFAULT 0,
    fecha_respuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trivia_id) REFERENCES trivia(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_respuesta (trivia_id, usuario_id, pregunta_index)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para pictionary
CREATE TABLE IF NOT EXISTS pictionary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    palabras JSON NOT NULL,
    palabra_actual_index INT DEFAULT 0,
    dibujante_id INT DEFAULT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para adivinanzas de pictionary
CREATE TABLE IF NOT EXISTS adivinanzas_pictionary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pictionary_id INT NOT NULL,
    usuario_id INT NOT NULL,
    adivinanza TEXT NOT NULL,
    es_correcta BOOLEAN DEFAULT FALSE,
    puntos INT DEFAULT 0,
    fecha_adivinanza TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pictionary_id) REFERENCES pictionary(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para planning poker
CREATE TABLE IF NOT EXISTS planning_poker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    tarea TEXT NOT NULL,
    revelado BOOLEAN DEFAULT FALSE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para estimaciones de planning poker
CREATE TABLE IF NOT EXISTS estimaciones_poker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poker_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estimacion VARCHAR(10) NOT NULL,
    fecha_estimacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poker_id) REFERENCES planning_poker(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_estimacion (poker_id, usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para reacciones
CREATE TABLE IF NOT EXISTS reacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    usuario_id INT NOT NULL,
    emoji VARCHAR(10) NOT NULL,
    fecha_reaccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reaccion (sesion_id, usuario_id),
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para ruleta de decisiones
CREATE TABLE IF NOT EXISTS ruleta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    opciones JSON NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sesion_id) REFERENCES sesiones(id) ON DELETE CASCADE,
    INDEX idx_sesion (sesion_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para historial de ruleta
CREATE TABLE IF NOT EXISTS historial_ruleta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruleta_id INT NOT NULL,
    resultado VARCHAR(255) NOT NULL,
    fecha_giro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ruleta_id) REFERENCES ruleta(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
