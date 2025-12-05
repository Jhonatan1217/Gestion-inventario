<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../Config/database.php";
require_once __DIR__ . "/../models/ficha.php";

class FichaController {

    private $model;

    public function __construct(PDO $conn) {
        $this->model = new FichaModel($conn);
    }

    /* Listar fichas */
    public function listar() {
        echo json_encode($this->model->listar());
    }

    /* Obtener ficha por ID */
    public function obtener($id) {
        if (!$id) {
            echo json_encode(['error' => 'id_ficha requerido']);
            return;
        }

        $data = $this->model->obtener($id);
        echo json_encode($data ?: ['error' => 'Ficha no encontrada']);
    }

    /* Crear ficha */
    public function crear() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        $ok = $this->model->crear($input);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Ficha creada correctamente" : "Error al crear ficha"
        ]);
    }

    /* Actualizar ficha */
    public function actualizar() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input['id_ficha'])) {
            echo json_encode(['error' => 'id_ficha requerido']);
            return;
        }

        $ok = $this->model->actualizar($input);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Ficha actualizada correctamente" : "Error al actualizar ficha"
        ]);
    }

    /* Activar ficha */
    public function activar($id) {
        if (!$id) {
            echo json_encode(['error' => 'id_ficha requerido']);
            return;
        }

        $ok = $this->model->activar($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Ficha activada" : "Error al activar ficha"
        ]);
    }

    /* Inactivar ficha */
    public function inactivar($id) {
        if (!$id) {
            echo json_encode(['error' => 'id_ficha requerido']);
            return;
        }

        $ok = $this->model->inactivar($id);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? "Ficha inactivada" : "Error al inactivar ficha"
        ]);
    }
}

/* Router */
$accion = $_GET['accion'] ?? null;
$id = $_GET['id_ficha'] ?? null;

$controller = new FichaController($conn);

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
        echo json_encode(["error" => "Acción no válida"]);
}

