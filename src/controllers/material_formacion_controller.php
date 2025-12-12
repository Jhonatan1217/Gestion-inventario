<?php

require_once __DIR__ . "/../models/material_formacion.php";
require_once __DIR__ . "/../../Config/database.php";

class MaterialFormacionController {

    private $model;

    // Constructor: receives the PDO connection and creates the model instance.
    // Also sets the response header to JSON format.
    public function __construct(PDO $conn) {
        $this->model = new MaterialFormacionModel($conn);
        header("Content-Type: application/json; charset=utf-8");
    }

    /* List all materials */
    public function listar() {
        $data = $this->model->getAll();
        echo json_encode($data);
    }

    /* Get material by ID */
    public function obtener() {
        $id = $_GET["id"] ?? null;

        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            return;
        }

        $data = $this->model->getById($id);
        echo json_encode($data ?: ["error" => "Material no encontrado"]);
    }

    /* Create new material */
    public function crear() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            echo json_encode(["error" => "Datos inválidos"]);
            return;
        }

        $ok = $this->model->create($input);

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Material creado correctamente" : "Error al crear material. Verifica la clasificación y el código de inventario."
        ]);
    }

    /* Update existing material */
    public function actualizar() {
        $id = $_GET["id"] ?? null;

        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            return;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            echo json_encode(["error" => "Datos inválidos"]);
            return;
        }

        $ok = $this->model->update($id, $input);

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Material actualizado correctamente" : "Error al actualizar material. Verifica la clasificación y el código de inventario."
        ]);
    }

    /* Delete material */
    public function eliminar() {
        $id = $_GET["id"] ?? null;

        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            return;
        }

        $ok = $this->model->delete($id);

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Material eliminado correctamente" : "No se puede eliminar el material porque está en uso en otras tablas."
        ]);
    }

    /* Toggle material status (enable/disable) */
    public function toggleEstado() {
        $id = $_GET["id"] ?? null;

        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            return;
        }

        // Get current material
        $material = $this->model->getById($id);
        
        if (!$material) {
            echo json_encode(["error" => "Material no encontrado"]);
            return;
        }

        // Toggle status
        $nuevoEstado = $material['estado'] === 'Disponible' ? 'Agotado' : 'Disponible';
        
        $ok = $this->model->update($id, [
            'nombre' => $material['nombre'],
            'descripcion' => $material['descripcion'],
            'unidad_medida' => $material['unidad_medida'],
            'clasificacion' => $material['clasificacion'],
            'codigo_inventario' => $material['codigo_inventario'],
            'estado' => $nuevoEstado
        ]);

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Estado actualizado correctamente" : "Error al actualizar estado"
        ]);
    }

    /* Search material by name or code */
    public function buscar() {
        $term = $_GET["term"] ?? "";
        $data = $this->model->search($term);
        echo json_encode($data);
    }

    /* Get material stock */
    public function stock() {
        $id = $_GET["id"] ?? null;

        if (!$id) {
            echo json_encode(["error" => "ID requerido"]);
            return;
        }

        $data = $this->model->getStockTotal($id);
        echo json_encode($data);
    }
}

/* Router - Routes requests to controller methods */

// Get the action from the query string
$accion = $_GET["accion"] ?? null;

// Validate that an action was specified
if (!$accion) {
    echo json_encode(["error" => "Acción requerida"]);
    exit;
}

// Create controller instance with database connection
$controller = new MaterialFormacionController($conn);

// Route the request to the appropriate controller method
switch ($accion) {

    case "listar":
        $controller->listar();
        break;

    case "obtener":
        $controller->obtener();
        break;

    case "crear":
        $controller->crear();
        break;

    case "actualizar":
        $controller->actualizar();
        break;

    case "eliminar":
        $controller->eliminar();
        break;

    case "toggleEstado":
        $controller->toggleEstado();
        break;

    case "buscar":
        $controller->buscar();
        break;

    case "stock":
        $controller->stock();
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}

?>