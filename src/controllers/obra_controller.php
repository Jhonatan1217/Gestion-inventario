<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../Config/database.php";
require_once __DIR__ . "/../models/obra.php";

/* VALIDAR CONEXIÓN */
if (!isset($conn)) {
    echo json_encode(["error" => "Conexion no disponible"]);
    exit;
}

class ObraController {

    private $model;

    public function __construct(PDO $conn) {
        $this->model = new ObraModel($conn);
    }

    public function listar() {
        echo json_encode($this->model->listar());
    }

    public function obtener($id) {
        echo json_encode($this->model->obtener($id));
    }

    public function crear() {
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode([
            "success" => $this->model->crear($data)
        ]);
    }

    public function actualizar() {
        $data = json_decode(file_get_contents("php://input"), true);
        echo json_encode([
            "success" => $this->model->actualizar($data)
        ]);
    }

    public function cambiarEstado($id, $estado) {
        echo json_encode([
            "success" => $this->model->cambiarEstado($id, $estado)
        ]);
    }
}

/* INSTANCIA CONTROLLER */
$controller = new ObraController($conn);

/* ROUTER */
$accion = $_GET["accion"] ?? null;
$id = $_GET["id_actividad"] ?? null;

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
        $controller->cambiarEstado($id, "Activa");
        break;

    case "finalizar":
        $controller->cambiarEstado($id, "Finalizada");
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}
