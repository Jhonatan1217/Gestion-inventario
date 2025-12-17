<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../Config/database.php";
require_once __DIR__ . "/../models/rae.php";

class RaeController {

    private $model;

    // Constructor: receives the PDO connection and creates the model instance
    public function __construct(PDO $conn) {
        $this->model = new RaeModel($conn);
    }

    /* List all RAEs */
    public function listar() {
        // Get all RAEs from the model and return as JSON
        echo json_encode($this->model->listar());
    }

    /* Get RAE by ID */
    public function obtener($id) {
        // Validate that ID was provided
        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']);
            return;
        }

        // Query the model for the specific RAE
        $data = $this->model->obtener($id);
        
        // Return the RAE data or error if not found
        echo json_encode($data ?: ['error' => 'RAE no encontrada']);
    }

    /* Create new RAE */
    public function crear() {
        // Decode the JSON input from the request body
        $input = json_decode(file_get_contents("php://input"), true);

        // Validate that valid JSON was received
        if (!$input) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        // Create the RAE in the database
        $ok = $this->model->crear($input);

        // Return success or error response
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "RAE creada correctamente" : "Error al crear RAE"
        ]);
    }

/*Update*/
public function actualizar() {

    $data = $this->getJson();

    if (!isset($data["id_rae"])) {
        return $this->jsonResponse(["error" => "id_rae es obligatorio"], 400);
    }

    $ok = $this->model->actualizar(
        (int)$data["id_rae"],
        $data["codigo_rae"] ?? null,
        isset($data["id_programa"]) ? (int)$data["id_programa"] : null,
        $data["descripcion_rae"] ?? null,
        $data["estado"] ?? null
    );

    return $this->jsonResponse(
        $ok ? ["mensaje" => "RAE actualizado correctamente"]
            : ["error" => "No hay datos para actualizar"],
        $ok ? 200 : 400
    );
}



    /* ==========================
       CAMBIAR ESTADO (ACTIVAR/INACTIVAR)
    ========================== */
    public function cambiar_estado() {

        $data = $this->getJson();

        if (!isset($data["id_rae"], $data["estado"])) {
            $this->jsonResponse(["error" => "Datos incompletos"], 400);
            return;
        }

        // Update the RAE in the database
        $ok = $this->model->actualizar($input);

        // Return success or error response
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "RAE actualizada correctamente" : "Error al actualizar RAE"
        ]);
    }

    /* Activate RAE */
    public function activar($id) {
        // Validate that ID was provided
        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']);
            return;
        }

        // Change RAE status to active
        $ok = $this->model->activar($id);

        // Return success or error response
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "RAE activada" : "Error al activar RAE"
        ]);
    }

    /* Deactivate RAE */
    public function inactivar($id) {
        // Validate that ID was provided
        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']);
            return;
        }

        // Change RAE status to inactive
        $ok = $this->model->inactivar($id);

        // Return success or error response
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "RAE inactivada" : "Error al inactivar RAE"
        ]);
    }
}

/* Router - Routes requests to controller methods */

// Get the action and ID from query string
$accion = $_GET['accion'] ?? null;
$id = $_GET['id_rae'] ?? null;

// Create controller instance with database connection
$controller = new RaeController($conn);

// Route the request to the appropriate controller method
switch ($accion) {

    case "listar":
        $controller->listar();
        break;

    case "obtener":
        $controller->obtener($id);
        break;

    case "crear":
        $controller->crear();
        break;

    case "actualizar":
        $controller->actualizar();
        break;

    case "activar":
        $controller->activar($id);
        break;

    case "inactivar":
        $controller->inactivar($id);
        break;

    default:
        // Invalid action requested
        echo json_encode(["error" => "Acción no válida"]);
}
