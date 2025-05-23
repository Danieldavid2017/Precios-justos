-- Crear base de datos (ejecutar como superusuario)
CREATE DATABASE inventario_db;

-- Conectar a la base de datos inventario_db y ejecutar lo siguiente:

-- Crear tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INTEGER NOT NULL DEFAULT 0,
    categoria VARCHAR(100),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_productos_nombre ON productos(nombre);
CREATE INDEX IF NOT EXISTS idx_productos_categoria ON productos(categoria);

-- Insertar datos de ejemplo
INSERT INTO productos (nombre, descripcion, precio, stock, categoria) VALUES
('Laptop Dell XPS 13', 'Laptop ultrabook con procesador Intel i7', 1299.99, 15, 'Electrónicos'),
('Mouse Logitech MX Master', 'Mouse inalámbrico ergonómico', 99.99, 50, 'Accesorios'),
('Teclado Mecánico Corsair', 'Teclado mecánico RGB para gaming', 149.99, 25, 'Accesorios'),
('Monitor Samsung 27"', 'Monitor 4K UHD de 27 pulgadas', 399.99, 8, 'Electrónicos'),
('Auriculares Sony WH-1000XM4', 'Auriculares con cancelación de ruido', 349.99, 12, 'Audio');

-- Crear función para actualizar fecha de modificación
CREATE OR REPLACE FUNCTION actualizar_fecha_modificacion()
RETURNS TRIGGER AS $$
BEGIN
    NEW.fecha_actualizacion = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Crear trigger para actualizar automáticamente la fecha de modificación
CREATE TRIGGER trigger_actualizar_fecha
    BEFORE UPDATE ON productos
    FOR EACH ROW
    EXECUTE FUNCTION actualizar_fecha_modificacion();