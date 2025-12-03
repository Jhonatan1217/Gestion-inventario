<?php
require_once __DIR__ . "/../models/bodega.php";
require_once __DIR__ . "/../../Config/database.php";

// It is established that all responses will be in JSON format.
header("Content-Type: application/json; charset=utf-8");

/* Controller REST to handle CRUD operations for warehouses.*/
class BodegaController {

    /**Instance of the warehouse model.*/
    private $model;

    /**Constructor: receives the connection and creates the model.*/
    public function __construct(PDO $conn) {
        $this->model = new BodegaModel($conn);
    }

    /**Send a JSON response with HTTP code.*/
    private function jsonResponse($data, int $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /* Gets the JSON sent by the client (POST/PUT).*/
    private function getJson() {
        return json_decode(file_get_contents("php://input"), true);
    }

    /* Lists all warehouses.*/
   
    public function listar() {
        $this->jsonResponse($this->model->listar());
    }

    /* Gets a warehouse by its ID.*/
    public function obtener() {
        $id = $_GET["id_bodega"] ?? null;

        // ID validation
        if (!$id) {
            $this->jsonResponse(["error" => "ID no proporcionado"], 400);
            return;
        }

        // Query to the model
        $bodega = $this->model->obtenerPorId($id);

        // If it exists, returns 200, if not, 404
        $this->jsonResponse(
            $bodega ?: ["error" => "Bodega no encontrada"],
            $bodega ? 200 : 404
        );
    }

    /* Creates a new warehouse.*/
    public function crear() {
        $data = $this->getJson();

        // JSON validation
        if (!$data) {
            $this->jsonResponse(["error" => "JSON inválido"], 400);
            return;
        }

        // Insertion in DB
        $ok = $this->model->crear(
            $data['codigo_bodega'],
            $data['nombre'],
            $data['ubicacion'],
        );

        // Message according to success or failure
        $this->jsonResponse(
            $ok ? ["mensaje" => "Bodega creada correctamente"]
                : ["error" => "No se pudo crear"],
            $ok ? 200 : 500
        );
    }

    /* Updates an existing warehouse.*/
    public function actualizar() {
        $data = $this->getJson();
        $id = $_GET["id_bodega"] ?? $data["id_bodega"] ?? null;

        // Validación de ID
        if (!$id) {
            $this->jsonResponse(["error" => "ID faltante"], 400);
            return;
        }

        // Update in DB
        $ok = $this->model->actualizar(
            $id,
            $data['codigo_bodega'],
            $data['nombre'],
            $data['ubicacion'],
        );

        $this->jsonResponse(
            $ok ? ["mensaje" => "Bodega actualizada correctamente"]
                : ["error" => "No se pudo actualizar"],
            $ok ? 200 : 500
        );
    }

    /*Deletes a warehouse by ID.*/
    public function eliminar() {
        $data = $this->getJson();
        $id = $_GET["id_bodega"] ?? $data["id_bodega"] ?? null;

        // ID validation
        if (!$id) {
            $this->jsonResponse(["error" => "ID faltante"], 400);
            return;
        }

        // Deletion in DB
        $ok = $this->model->eliminar($id);

        $this->jsonResponse(
            $ok ? ["mensaje" => "Bodega eliminada correctamente"]
                : ["error" => "No se pudo eliminar"],
            $ok ? 200 : 500
        );
    }

    /*Changes the state of a warehouse (active/inactive).*/
    public function cambiar_estado() {
        $data = $this->getJson();

        // Data validation
        if (!isset($data["id_bodega"], $data["estado"])) {
            $this->jsonResponse(["error" => "Datos incompletos"], 400);
            return;
        }

        // State update
        $ok = $this->model->cambiarEstado(
            $data["id_bodega"],
            $data["estado"]
        );

        $this->jsonResponse(
            $ok ? ["mensaje" => "Estado actualizado correctamente"]
                : ["error" => "No se pudo actualizar el estado"],
            $ok ? 200 : 500
        );
    }
}

// Action received by GET
$accion = $_GET["accion"] ?? null;

// Action validation
if (!$accion) {
    echo json_encode(["error" => "Acción no especificada"]);
    exit;
}

// Controller instance
$controller = new BodegaController($conn);

// Method verification
if (!method_exists($controller, $accion)) {
    echo json_encode(["error" => "Acción inválida"]);
    exit;
}

// Dynamic method call
$controller->$accion();
