<?php

require_once __DIR__ . "/../models/rae.php";

class rae_controller {

    private $model;

    public function __construct($conn) {
        $this->model = new rae_model($conn);

        header("Content-Type: application/json");
    }

    /* ==========================
       LISTAR
    ========================== */
    public function listar() {
        $data = $this->model->listar();
        echo json_encode($data);
    }

    /* ==========================
       OBTENER POR ID
    ========================== */
    public function obtener($id) {
        if (!$id) {
            echo json_encode(['error' => 'ID requerido']);
            return;
        }
        $data = $this->model->obtener($id);
        echo json_encode($data ?: ['error' => 'RAE no encontrada']);
    }

    /* ==========================
       CREAR
    ========================== */
    public function crear() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        $ok = $this->model->crear($input);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE creada correctamente' : 'Error al crear RAE'
        ]);
    }

    /* ==========================
       ACTUALIZAR
    ========================== */
    public function actualizar() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['id_rae'])) {
            echo json_encode(['error' => 'id_rae requerido']);
            return;
        }

        $ok = $this->model->actualizar($input);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE actualizada correctamente' : 'Error al actualizar RAE'
        ]);
    }

    /* ==========================
       ACTIVAR
    ========================== */
    public function activar() {
        $id = $_GET['id_rae'] ?? null;

        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']);
            return;
        }

        $ok = $this->model->activar($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE activada' : 'Error al activar RAE'
        ]);
    }

    /* ==========================
       INACTIVAR
    ========================== */
    public function inactivar() {
        $id = $_GET['id_rae'] ?? null;

        if (!$id) {
            echo json_encode(['error' => 'id_rae requerido']);
            return;
        }

        $ok = $this->model->inactivar($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'RAE inactivada' : 'Error al inactivar RAE'
        ]);
    }
}
