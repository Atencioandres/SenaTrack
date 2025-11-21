-- Base de datos SenaTrack
-- Sistema de Gestión de Comparendos

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS senatrack_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE senatrack_db;

-- Nueva tabla para programas de formación
CREATE TABLE IF NOT EXISTS programas_formacion (
    id_programa INT AUTO_INCREMENT PRIMARY KEY,
    nombre_programa VARCHAR(150) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: administrador
CREATE TABLE IF NOT EXISTS administrador (
    id_administrador VARCHAR(20) PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    nombre_administrador VARCHAR(50) NOT NULL,
    apellido_administrador VARCHAR(50) NOT NULL,
    correo_administrador VARCHAR(100) NOT NULL UNIQUE,
    nivel_acceso VARCHAR(20) NOT NULL CHECK (nivel_acceso IN ('alto', 'medio', 'bajo')),
    contrasena VARCHAR(255) NOT NULL,
    foto_perfil TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: usuario
-- Agregados campos para recuperación de contraseña
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario VARCHAR(20) PRIMARY KEY,
    id_administrador VARCHAR(20) NOT NULL,
    tipo_usuario VARCHAR(30) NOT NULL CHECK (tipo_usuario IN ('aprendiz', 'bienestar', 'admin')),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    nombre_usuario VARCHAR(50) NOT NULL,
    apellido_usuario VARCHAR(50) NOT NULL,
    correo_electronico VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    pregunta_seguridad VARCHAR(255) DEFAULT NULL,
    respuesta_seguridad VARCHAR(255) DEFAULT NULL,
    foto_perfil TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_administrador) REFERENCES administrador(id_administrador) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nueva tabla para códigos de verificación de recuperación de contraseña
CREATE TABLE IF NOT EXISTS codigos_verificacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo_electronico VARCHAR(100) NOT NULL,
    codigo VARCHAR(6) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion TIMESTAMP NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    INDEX idx_correo (correo_electronico),
    INDEX idx_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: informe
CREATE TABLE IF NOT EXISTS informe (
    id_informe VARCHAR(20) PRIMARY KEY,
    tipo_informe VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_generacion DATE NOT NULL DEFAULT (CURRENT_DATE)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: aprendiz
-- Modificada para usar relación con tabla programas_formacion
CREATE TABLE IF NOT EXISTS aprendiz (
    id_aprendiz VARCHAR(20) PRIMARY KEY,
    id_usuario VARCHAR(20) NOT NULL,
    id_informe VARCHAR(20) NOT NULL,
    nombre_aprendiz VARCHAR(50) NOT NULL,
    id_programa INT NOT NULL,
    ficha INT NOT NULL CHECK (ficha > 0),
    telefono BIGINT NOT NULL CHECK (telefono >= 3000000000 AND telefono <= 3999999999),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_informe) REFERENCES informe(id_informe) ON DELETE CASCADE,
    FOREIGN KEY (id_programa) REFERENCES programas_formacion(id_programa) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: personal_bienestar
CREATE TABLE IF NOT EXISTS personal_bienestar (
    id_bienestar VARCHAR(20) PRIMARY KEY,
    id_usuario VARCHAR(20) NOT NULL,
    id_informe VARCHAR(20) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    especialidad VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_informe) REFERENCES informe(id_informe) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: comparendo
-- Agregado campo evidencia_url para almacenar archivos adjuntos
CREATE TABLE IF NOT EXISTS comparendo (
    id_comparendo VARCHAR(20) PRIMARY KEY,
    id_aprendiz VARCHAR(20) NOT NULL,
    id_bienestar VARCHAR(20) NOT NULL,
    id_informe VARCHAR(20) NOT NULL,
    descripcion TEXT NOT NULL,
    evidencia_url VARCHAR(500) DEFAULT NULL,
    fecha_comparendo DATE NOT NULL DEFAULT (CURRENT_DATE),
    FOREIGN KEY (id_aprendiz) REFERENCES aprendiz(id_aprendiz) ON DELETE CASCADE,
    FOREIGN KEY (id_bienestar) REFERENCES personal_bienestar(id_bienestar) ON DELETE CASCADE,
    FOREIGN KEY (id_informe) REFERENCES informe(id_informe) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar programas de formación por defecto
INSERT INTO programas_formacion (nombre_programa, descripcion) VALUES 
('Análisis y Desarrollo de Software', 'Programa técnico enfocado en desarrollo de aplicaciones'),
('Mecanización Agrícola', 'Programa técnico en maquinaria y equipos agrícolas'),
('Acuicultura', 'Programa técnico en cultivo de especies acuáticas'),
('Especies Menores', 'Programa técnico en producción de especies menores'),
('Empresas Pecuarias', 'Programa técnico en gestión de empresas pecuarias');

-- Insertar datos de prueba

-- Administrador por defecto
INSERT INTO administrador VALUES 
('admin1', 'admin', 'Carlos', 'Torres', 'admin@sena.edu.co', 'alto', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NOW());

-- Usuario administrador
INSERT INTO usuario VALUES 
('u1', 'admin1', 'admin', 'admin', 'Carlos', 'Torres', 'admin@sena.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NOW());

-- Informes de ejemplo
INSERT INTO informe VALUES 
('inf1', 'Conducta', 'Falta de respeto a instructores', '2025-01-15'),
('inf2', 'Asistencia', 'Inasistencia injustificada reiterada', '2025-01-16'),
('inf3', 'Conducta', 'Uso inadecuado del uniforme', '2025-01-17');

-- Usuario Personal de Bienestar
INSERT INTO usuario VALUES 
('u2', 'admin1', 'bienestar', 'tmorales', 'Tatiana', 'Morales', 'tmorales@sena.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NOW());

INSERT INTO personal_bienestar VALUES 
('b1', 'u2', 'inf1', 'Tatiana Morales', 'Psicología');

-- Usuario Aprendiz
INSERT INTO usuario VALUES 
('u3', 'admin1', 'aprendiz', 'jperez', 'Juan', 'Pérez', 'jperez@sena.edu.co', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, NULL, NULL, NOW());

-- Actualizado para usar id_programa en lugar de nombre directo
INSERT INTO aprendiz VALUES 
('a1', 'u3', 'inf1', 'Juan Pérez', 1, 2924030, 3104560001);

-- Comparendo de ejemplo
INSERT INTO comparendo VALUES 
('c1', 'a1', 'b1', 'inf1', 'Llegó tarde reiteradamente a clase', NULL, '2025-01-20');
