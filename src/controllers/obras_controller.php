<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../models/obra.php";
require_once __DIR__ . "/../../Config/database.php";

class ObraController {

    private $model;

    public function __construct(PDO $conn) {
        $this->model = new ObraModel($conn);
    }

    /* List works */
    public function listar() {
        echo json_encode($this->model->listar());
    }

    /* Get work */
    public function obtener($id) {
        if (!$id) {
            echo json_encode(["error" => "id_actividad requerido"]);
            return;
        }

        $data = $this->model->obtener($id);
        echo json_encode($data ?: ["error" => "Obra no encontrada"]);
    }

    /* Create work */
    public function crear() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!$input) {
            echo json_encode(["error" => "Datos inválidos"]);
            return;
        }

        $ok = $this->model->crear($input);

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Obra creada correctamente" : "Error al crear obra"
        ]);
    }

    /* Update work */
    public function actualizar() {
        $input = json_decode(file_get_contents("php://input"), true);

        if (!isset($input["id_actividad"])) {
            echo json_encode(["error" => "id_actividad requerido"]);
            return;
        }

        $ok = $this->model->actualizar($input);

        echo json_encode([
            "success" => $ok,
            "message" => $ok ? "Obra actualizada correctamente" : "Error al actualizar obra"
        ]);
    }

    /* Change state */
    public function cambiarEstado($id, $accion) {
        if (!$id) {
            echo json_encode(["error" => "id_actividad requerido"]);
            return;
        }

        $map = [
            "activar"   => "Activa",
            "finalizar" => "Finalizada"
        ];

        if (!isset($map[$accion])) {
            echo json_encode(["error" => "Acción inválida"]);
            return;
        }

        $ok = $this->model->cambiarEstado($id, $map[$accion]);

        echo json_encode([
            "success" => $ok,
            "message" => $ok
                ? "Estado actualizado a {$map[$accion]}"
                : "Error al cambiar estado"
        ]);
    }
}

/* ROUTER */
$accion = $_GET["accion"] ?? null;
$id = $_GET["id_actividad"] ?? null;

$database = new Database();
$conn = $database->getConnection();

$controller = new ObraController($conn);

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
        $controller->cambiarEstado($id, "activar");
        break;

    case "finalizar":
        $controller->cambiarEstado($id, "finalizar");
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}
