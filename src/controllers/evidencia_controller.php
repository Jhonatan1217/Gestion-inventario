<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../Config/database.php";
require_once __DIR__ . "/../models/evidencia.php";

class EvidenciaController {

    private $model;

    public function __construct(PDO $conn) {
        $this->model = new EvidenciaModel($conn);
    }

    public function index() {
        echo json_encode($this->model->listar());
    }

    public function show($id) {
        $resultado = $this->model->obtenerPorId($id);

        if ($resultado) {
            echo json_encode($resultado);
        } else {
            http_response_code(404);
            echo json_encode(["mensaje" => "Evidencia no encontrada"]);
        }
    }

    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->model->crear($data)) {
            http_response_code(201);
            echo json_encode(["mensaje" => "Evidencia creada correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Error al crear la evidencia"]);
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if ($this->model->actualizar($id, $data)) {
            echo json_encode(["mensaje" => "Evidencia actualizada correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Error al actualizar la evidencia"]);
        }
    }

    public function destroy($id) {
        if ($this->model->eliminar($id)) {
            echo json_encode(["mensaje" => "Evidencia eliminada correctamente"]);
        } else {
            http_response_code(400);
            echo json_encode(["mensaje" => "Error al eliminar la evidencia"]);
        }
    }
}

/* Instancia del controlador */
$database = new Database();
$db = $database->getConnection();
$controller = new EvidenciaController($db);

/* Manejo básico de rutas */
$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET" && isset($_GET["id"])) {
    $controller->show($_GET["id"]);
} elseif ($method === "GET") {
    $controller->index();
} elseif ($method === "POST") {
    $controller->store();
} elseif ($method === "PUT" && isset($_GET["id"])) {
    $controller->update($_GET["id"]);
} elseif ($method === "DELETE" && isset($_GET["id"])) {
    $controller->destroy($_GET["id"]);
}
