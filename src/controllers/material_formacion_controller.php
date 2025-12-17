<?php

class MaterialFormacionController {

    private $model;

    // Constructor: receives the PDO connection and creates the model instance.
    // Also sets the response header to JSON format.
    public function __construct(PDO $conn) {
        $this->model = new MaterialFormacionModel($conn);
        header("Content-Type: application/json; charset=utf-8");
    }

    // Get all materials
    public function listar()
    {
        return $this->model->getAll();
    }

    // Get material by ID
    public function obtener($id)
    {
        if (!$id) {
            return null;
        }

        return $this->model->getById($id);
    }

    // Create new material
    public function crear($data)
    {
        if (!$data) {
            return [
                "status" => "error",
                "message" => "No se recibieron datos para crear el material."
            ];
        }

        // Model returns false when validation fails
        $ok = $this->model->create($data);

        if (!$ok) {
            return [
                "status" => "error",
                "message" => "El material no fue creado. Verifique la clasificación y el código de inventario."
            ];
        }

        return [
            "status" => "success",
            "message" => "El material fue creado correctamente."
        ];
    }

    // Update material
    public function actualizar($id, $data)
    {
        if (!$id || !$data) {
            return [
                "status" => "error",
                "message" => "Datos inválidos para actualizar el material."
            ];
        }

        $ok = $this->model->update($id, $data);

        if (!$ok) {
            return [
                "status" => "error",
                "message" => "El material no fue actualizado. Verifique la clasificación y el código de inventario."
            ];
        }

        return [
            "status" => "success",
            "message" => "El material fue actualizado correctamente."
        ];
    }

    // // Delete material
    // public function eliminar($id)
    // {
    //     if (!$id) {
    //         return [
    //             "status" => "error",
    //             "message" => "ID de material no válido."
    //         ];
    //     }

    //     // Model returns false if material is used
    //     $ok = $this->model->delete($id);

    //     if (!$ok) {
    //         return [
    //             "status" => "error",
    //             "message" => "El material no se puede eliminar porque está siendo usado en otras tablas."
    //         ];
    //     }

    //     return [
    //         "status" => "success",
    //         "message" => "El material fue eliminado correctamente."
    //     ];
    // }

    // Search material
    public function buscar($term)
    {
        return $this->model->search($term);
    }

    // Get total stock
    public function stock($id)
    {
        if (!$id) {
            return [
                "stock_bodega" => 0,
                "stock_subbodega" => 0
            ];
        }

        return $this->model->getStockTotal($id);
    }
}

/* =====================================
   Action handler (switch)
   ===================================== */

$controller = new MaterialFormacionController($conn);

// Read action
$accion = $_GET['accion'] ?? null;

// Send JSON response
function sendJSON($data, $code = 200)
{
    header("Content-Type: application/json");
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
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

    // case "eliminar":
    //     sendJSON($controller->eliminar($_GET['id'] ?? null));
    //     break;

    default:
        sendJSON([
            "status" => "error",
            "message" => "La acción solicitada no es válida."
        ], 400);
        break;
}

?>