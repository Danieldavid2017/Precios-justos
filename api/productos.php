<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../models/Producto.php';

$database = new Database();
$db = $database->getConnection();

// Inicializar base de datos si es necesario
$database->initializeDatabase();

$producto = new Producto($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch($request_method) {
    case 'GET':
        if(isset($_GET['id'])) {
            // Obtener producto específico
            $producto->id = $_GET['id'];
            if($producto->obtenerPorId()) {
                $producto_arr = array(
                    "id" => $producto->id,
                    "nombre" => $producto->nombre,
                    "descripcion" => $producto->descripcion,
                    "precio" => $producto->precio,
                    "stock" => $producto->stock,
                    "categoria" => $producto->categoria,
                    "fecha_creacion" => $producto->fecha_creacion,
                    "fecha_actualizacion" => $producto->fecha_actualizacion
                );
                http_response_code(200);
                echo json_encode($producto_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Producto no encontrado."));
            }
        } elseif(isset($_GET['buscar'])) {
            // Buscar productos
            $stmt = $producto->buscar($_GET['buscar']);
            $num = $stmt->rowCount();

            if($num > 0) {
                $productos_arr = array();
                $productos_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $producto_item = array(
                        "id" => $id,
                        "nombre" => $nombre,
                        "descripcion" => html_entity_decode($descripcion),
                        "precio" => $precio,
                        "stock" => $stock,
                        "categoria" => $categoria,
                        "fecha_creacion" => $fecha_creacion,
                        "fecha_actualizacion" => $fecha_actualizacion
                    );
                    array_push($productos_arr["records"], $producto_item);
                }
                http_response_code(200);
                echo json_encode($productos_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No se encontraron productos."));
            }
        } else {
            // Obtener todos los productos
            $stmt = $producto->obtenerTodos();
            $num = $stmt->rowCount();

            if($num > 0) {
                $productos_arr = array();
                $productos_arr["records"] = array();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $producto_item = array(
                        "id" => $id,
                        "nombre" => $nombre,
                        "descripcion" => html_entity_decode($descripcion),
                        "precio" => $precio,
                        "stock" => $stock,
                        "categoria" => $categoria,
                        "fecha_creacion" => $fecha_creacion,
                        "fecha_actualizacion" => $fecha_actualizacion
                    );
                    array_push($productos_arr["records"], $producto_item);
                }
                http_response_code(200);
                echo json_encode($productos_arr);
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "No se encontraron productos."));
            }
        }
        break;

    case 'POST':
        // Crear producto
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->nombre) && !empty($data->precio) && !empty($data->stock)) {
            $producto->nombre = $data->nombre;
            $producto->descripcion = $data->descripcion ?? '';
            $producto->precio = $data->precio;
            $producto->stock = $data->stock;
            $producto->categoria = $data->categoria ?? '';

            if($producto->crear()) {
                http_response_code(201);
                echo json_encode(array("message" => "Producto creado exitosamente."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo crear el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos. Nombre, precio y stock son requeridos."));
        }
        break;

    case 'PUT':
        // Actualizar producto
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->id) && !empty($data->nombre) && !empty($data->precio) && isset($data->stock)) {
            $producto->id = $data->id;
            $producto->nombre = $data->nombre;
            $producto->descripcion = $data->descripcion ?? '';
            $producto->precio = $data->precio;
            $producto->stock = $data->stock;
            $producto->categoria = $data->categoria ?? '';

            if($producto->actualizar()) {
                http_response_code(200);
                echo json_encode(array("message" => "Producto actualizado exitosamente."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo actualizar el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Datos incompletos."));
        }
        break;

    case 'DELETE':
        // Eliminar producto
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->id)) {
            $producto->id = $data->id;

            if($producto->eliminar()) {
                http_response_code(200);
                echo json_encode(array("message" => "Producto eliminado exitosamente."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "No se pudo eliminar el producto."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "ID del producto requerido."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método no permitido."));
        break;
}
?>