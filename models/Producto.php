<?php
class Producto {
    private $conn;
    private $table_name = "productos";

    public $id;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $categoria;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los productos
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Obtener producto por ID
    public function obtenerPorId() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->stock = $row['stock'];
            $this->categoria = $row['categoria'];
            $this->fecha_creacion = $row['fecha_creacion'];
            $this->fecha_actualizacion = $row['fecha_actualizacion'];
            return true;
        }
        return false;
    }

    // Crear producto
    public function crear() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nombre, descripcion, precio, stock, categoria) 
                  VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));

        // Bind de parámetros
        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->descripcion);
        $stmt->bindParam(3, $this->precio);
        $stmt->bindParam(4, $this->stock);
        $stmt->bindParam(5, $this->categoria);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Actualizar producto
    public function actualizar() {
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ?
                  WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->categoria = htmlspecialchars(strip_tags($this->categoria));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind de parámetros
        $stmt->bindParam(1, $this->nombre);
        $stmt->bindParam(2, $this->descripcion);
        $stmt->bindParam(3, $this->precio);
        $stmt->bindParam(4, $this->stock);
        $stmt->bindParam(5, $this->categoria);
        $stmt->bindParam(6, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Eliminar producto
    public function eliminar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Buscar productos
    public function buscar($keywords) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE nombre ILIKE ? OR descripcion ILIKE ? OR categoria ILIKE ?
                  ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        $keywords = "%{$keywords}%";
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);
        $stmt->execute();
        
        return $stmt;
    }
}