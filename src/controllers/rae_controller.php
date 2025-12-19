<?php

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/../../Config/database.php";
require_once __DIR__ . "/../models/rae.php";

class RaeController {

    private $model;

    public function __construct(PDO $conn) {
        $this->model = new RaeModel($conn);
    }

    /* ==========================
       HELPERS
    ========================== */

    private function getJson() {
        return json_decode(file_get_contents("php://input"), true);
    }

    private function jsonResponse($data, int $code = 200) {
        http_response_code($code);
        echo json_encode($data);
    }

    /* ==========================
       LISTAR TODOS
    ========================== */
    public function listar() {
        $this->jsonResponse($this->model->listar());
    }

    /* ==========================
       OBTENER POR ID
    ========================== */
    public function obtener($id) {
        if (!$id) {
            $this->jsonResponse(['error' => 'id_rae requerido'], 400);
            return;
        }

        $data = $this->model->obtener($id);
        $this->jsonResponse($data ?: ['error' => 'RAE no encontrada']);
    }

    /* ==========================
       CREAR
    ========================== */
    public function crear() {
        $input = $this->getJson();

        if (!$input) {
            $this->jsonResponse(['error' => 'Datos inválidos'], 400);
            return;
        }

        // SOLO lo que existe en tu tabla
        $required = ['codigo_rae', 'descripcion_rae', 'id_programa', 'estado'];

        foreach ($required as $field) {
            if (!isset($input[$field])) {
                $this->jsonResponse(['error' => "Falta el campo: $field"], 400);
                return;
            }
        }

        $ok = $this->model->crear(
            $input['codigo_rae'],
            $input['descripcion_rae'],
            intval($input['id_programa']),
            $input['estado']
        );

        $this->jsonResponse(
            $ok ? ["success" => true, "message" => "RAE creada correctamente"]
               : ["error" => "Error al crear RAE"],
            $ok ? 200 : 500
        );
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

        $ok = $this->model->cambiarEstado(
            intval($data["id_rae"]),
            $data["estado"]
        );

        $this->jsonResponse(
            $ok ? ["mensaje" => "Estado actualizado correctamente"]
                : ["error" => "No se pudo actualizar el estado"],
            $ok ? 200 : 500
        );
    }
}

/* ==========================
      ROUTER
========================== */

$accion = $_GET['accion'] ?? null;
$id = $_GET['id_rae'] ?? null;

$controller = new RaeController($conn);

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

    case "cambiar_estado":
        $controller->cambiar_estado();
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}