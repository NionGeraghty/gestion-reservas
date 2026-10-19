SET NAMES utf8mb4;
DROP TABLE IF EXISTS reserva_evento;
DROP TABLE IF EXISTS reserva;

CREATE TABLE reserva (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_huesped VARCHAR(100) NOT NULL,
    email_huesped VARCHAR(254) NOT NULL,
    nombre_alojamiento VARCHAR(150) NOT NULL,
    fecha_entrada DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    estado ENUM('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
    importe DECIMAL(10,2) NOT NULL,
    notas TEXT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reserva_estado (estado),
    INDEX idx_reserva_fechas (fecha_entrada, fecha_salida),
    CONSTRAINT fecha_valida CHECK (fecha_salida>fecha_entrada),
    CONSTRAINT importe_valido CHECK (importe >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reserva_evento (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT UNSIGNED NOT NULL,
    tipo ENUM ('creada','confirmada','cancelada') NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_evento_reserva (reserva_id),
    CONSTRAINT fk_evento_reserva FOREIGN KEY (reserva_id) REFERENCES reserva(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo
INSERT INTO reserva (id, nombre_huesped, email_huesped, nombre_alojamiento, fecha_entrada, fecha_salida, estado, importe, notas, fecha_creacion) VALUES
(1,  'María José Fernández López',     'mariajose.fernandez@example.com', 'Casa Rural El Robledal',       '2026-11-06', '2026-11-08', 'pendiente',  180.00, NULL,                                         '2026-10-15 18:42:00'),
(2,  'Íñigo Etxeberria Ruiz',          'inigo.etxeberria@example.com',    'Hotel Rural Valle del Jerte',  '2026-12-04', '2026-12-08', 'cancelada',  520.00, 'Viajan con un perro pequeño',                '2026-09-20 10:15:00'),
(3,  'Lucía Martín Sánchez',           'lucia.martin@example.com',        'Hotel Casa Palacio Los Arcos', '2026-09-12', '2026-09-14', 'confirmada', 220.00, NULL,                                         '2026-08-28 12:05:00'),
(4,  'Javier Rodríguez Gil',           'javier.rodriguez@example.com',    'Apartamentos Plaza Mayor',     '2026-10-02', '2026-10-05', 'confirmada', 225.00, NULL,                                         '2026-09-15 09:20:00'),
(5,  'Carmen Ruiz Delgado',            'carmen.ruiz@example.com',         'Casa Rural El Robledal',       '2026-10-09', '2026-10-11', 'cancelada',  180.00, NULL,                                         '2026-09-25 20:30:00'),
(6,  'José Antonio Pérez Moreno',      'joseantonio.perez@example.com',   'Hotel Casa Palacio Los Arcos', '2026-10-17', '2026-10-21', 'confirmada', 440.00, 'Llegada tardía, sobre las 23:00',            '2026-10-01 08:55:00'),
(7,  'Ana Belén Castro Núñez',         'anabelen.castro@example.com',     'Casa Rural La Fragua',         '2026-10-18', '2026-10-20', 'confirmada', 190.00, NULL,                                         '2026-10-05 19:10:00'),
(8,  'Sergio Navarro Blanco',          'sergio.navarro@example.com',      'Apartamentos Plaza Mayor',     '2026-10-28', '2026-11-03', 'confirmada', 450.00, 'Necesitan cuna para un bebé',                '2026-10-08 14:25:00'),
(9,  'María Luisa Gómez Herrera',      'marialuisa.gomez@example.com',    'Hotel Rural Valle del Jerte',  '2026-10-30', '2026-11-01', 'pendiente',  260.00, NULL,                                         '2026-10-17 22:14:00'),
(10, 'Raúl Domínguez Vega',            'raul.dominguez@example.com',      'Hotel Spa Termas del Tajo',    '2026-11-20', '2026-11-23', 'pendiente',  420.00, NULL,                                         '2026-10-18 11:02:00'),
(11, 'Elena Torres Marín',             'elena.torres@example.com',        'Casa Rural La Fragua',         '2026-12-05', '2026-12-08', 'confirmada', 285.00, 'Viajan con perro',                           '2026-10-12 17:40:00'),
(12, 'Marta Jiménez Ortiz',            'marta.jimenez@example.com',       'Hotel Spa Termas del Tajo',    '2026-11-13', '2026-11-15', 'cancelada',  280.00, NULL,                                         '2026-09-29 13:30:00'),
(13, 'José Luis Sanz Romero',          'joseluis.sanz@example.com',       'Hotel Casa Palacio Los Arcos', '2026-12-23', '2026-12-27', 'pendiente',  480.00, 'Solicitan habitación con vistas a la plaza', '2026-10-14 10:45:00'),
(14, 'Laura Moreno Díaz',              'laura.moreno@example.com',        'Apartamentos Plaza Mayor',     '2027-01-15', '2027-01-18', 'confirmada', 225.00, NULL,                                         '2026-10-11 16:50:00'),
(15, 'Francisco Javier López Serrano', 'fjavier.lopez@example.com',       'Casa Rural El Robledal',       '2027-02-12', '2027-02-15', 'cancelada',  270.00, NULL,                                         '2026-10-03 12:00:00');

INSERT INTO reserva_evento (reserva_id, tipo, descripcion, fecha_creacion) VALUES
(1,  'creada',     'Reserva creada',                                 '2026-10-15 18:42:00'),
(2,  'creada',     'Reserva creada',                                 '2026-09-20 10:15:00'),
(2,  'confirmada', 'Reserva confirmada',                             '2026-09-21 09:30:00'),
(2,  'cancelada',  'Reserva cancelada por el huésped',               '2026-10-10 17:05:00'),
(3,  'creada',     'Reserva creada',                                 '2026-08-28 12:05:00'),
(3,  'confirmada', 'Reserva confirmada',                             '2026-08-28 16:40:00'),
(4,  'creada',     'Reserva creada',                                 '2026-09-15 09:20:00'),
(4,  'confirmada', 'Reserva confirmada',                             '2026-09-15 13:10:00'),
(5,  'creada',     'Reserva creada',                                 '2026-09-25 20:30:00'),
(5,  'cancelada',  'Reserva cancelada por el huésped',               '2026-09-30 11:45:00'),
(6,  'creada',     'Reserva creada',                                 '2026-10-01 08:55:00'),
(6,  'confirmada', 'Reserva confirmada',                             '2026-10-01 10:20:00'),
(7,  'creada',     'Reserva creada',                                 '2026-10-05 19:10:00'),
(7,  'confirmada', 'Reserva confirmada',                             '2026-10-06 09:00:00'),
(8,  'creada',     'Reserva creada',                                 '2026-10-08 14:25:00'),
(8,  'confirmada', 'Reserva confirmada',                             '2026-10-09 10:05:00'),
(9,  'creada',     'Reserva creada',                                 '2026-10-17 22:14:00'),
(10, 'creada',     'Reserva creada',                                 '2026-10-18 11:02:00'),
(11, 'creada',     'Reserva creada',                                 '2026-10-12 17:40:00'),
(11, 'confirmada', 'Reserva confirmada',                             '2026-10-13 09:15:00'),
(12, 'creada',     'Reserva creada',                                 '2026-09-29 13:30:00'),
(12, 'confirmada', 'Reserva confirmada',                             '2026-09-30 10:00:00'),
(12, 'cancelada',  'Reserva cancelada por el huésped',               '2026-10-16 19:20:00'),
(13, 'creada',     'Reserva creada',                                 '2026-10-14 10:45:00'),
(14, 'creada',     'Reserva creada',                                 '2026-10-11 16:50:00'),
(14, 'confirmada', 'Reserva confirmada',                             '2026-10-11 18:30:00'),
(15, 'creada',     'Reserva creada',                                 '2026-10-03 12:00:00'),
(15, 'cancelada',  'Reserva cancelada por falta de pago',            '2026-10-07 09:40:00');