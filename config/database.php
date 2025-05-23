<?php
class Database
{
    private $host = 'localhost';
    private $db_name = 'inventario_db';
    private $username = 'postgres'; // Cambiar por tu usuario
    private $password = '1234'; // Cambiar por tu contraseña
    private $port = '5432';
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo json_encode(['error' => 'Error de conexión: ' . $exception->getMessage()]);
        }

        return $this->conn;
    }

    public function initializeDatabase()
    {
        try {
            $sql = "
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

            CREATE INDEX IF NOT EXISTS idx_productos_nombre ON productos(nombre);
            CREATE INDEX IF NOT EXISTS idx_productos_categoria ON productos(categoria);
            ";

            $this->conn->exec($sql);
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }
}
