<?php

// Include the RAE model
require_once __DIR__ . "/../models/rae.php";

// Controller class for RAE operations
class rae_controller {

    private $model; // Instance of the RAE model

    // Constructor: initialize the model and set JSON header
    public function __construct($conn) {
        $this->model = new rae_model($conn);
        header("Content-Type: application/json"); // Always return JSON
    }

    /* ==========================
       LIST ALL RAE ENTRIES
    ========================== */
    public function listar() {
        $data = $this->model->listar(); // Call the model's listar method
        echo json_encode($data); // Return data as JSON
    }

    /* ==========================
       GET RAE BY ID
    ========================== */
    public function obtener($id) {
        if (!$id) {
            echo json_encode(['error' => 'ID requerido']); // Error if ID is missing
            return;
        }
        $data = $this->model->obtener($id); // Get data from model
        echo json_encode($data ?: ['error' => 'RAE no encontrada']); // Return data or error
    }

    /* ==========================
       CREATE NEW RAE
    ========================== */
    public function crear() {
        $input = json_decode(file_get_contents('php://input'), true); // Read JSON input

        if (!$input) {
            echo json_encode(['error' => 'Datos inválidos']); // Error if input is invalid
            return;
        }

        $ok = $this->model->crear($input); // Call model create method

        // Return success message
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE creada correctamente' : 'Error al crear RAE'
        ]);
    }

    /* ==========================
       UPDATE EXISTING RAE
    ========================== */
    public function actualizar() {
        $input = json_decode(file_get_contents('php://input'), true); // Read JSON input

        if (!isset($input['id_rae'])) {
            echo json_encode(['error' => 'id_rae requerido']); // ID is required
            return;
        }

        $ok = $this->model->actualizar($input); // Call model update method

        // Return success message
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE actualizada correctamente' : 'Error al actualizar RAE'
        ]);
    }

    /* ==========================
       ACTIVATE RAE
    ========================== */
    public function activar() {
        $id = $_GET['id_rae'] ?? null; // Get ID from query string

        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']); // ID required
            return;
        }

        $ok = $this->model->activar($id); // Call model activate method

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE activada' : 'Error al activar RAE'
        ]);
    }

    /* ==========================
       DEACTIVATE RAE
    ========================== */
    public function inactivar() {
        $id = $_GET['id_rae'] ?? null; // Get ID from query string

        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']); // ID required
            return;
        }

        $ok = $this->model->inactivar($id); // Call model deactivate method

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE inactivada' : 'Error al inactivar RAE'
        ]);
    }
}
