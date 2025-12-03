<?php
require_once __DIR__ . "/../models/bodega.php";

class BodegaController {

    private $model; // Instance of BodegaModel

    // Constructor: receives a PDO connection and initializes the model
    public function __construct(PDO $conn) {
        $this->model = new BodegaModel($conn);
    }

    // Sends a standardized JSON response with a status code
    private function jsonResponse(array $payload, int $statusCode = 200): void {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    // Retrieves and validates the ID sent via POST; returns null if invalid
    private function getPostId(): ?int {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
            $this->jsonResponse(["status" => "error"], 400);
            return null;
        }
        return (int) $_POST["id"];
    }

    // List all warehouses, optionally filtered by status
    public function listar(): void {
        $estado = isset($_GET["estado"]) && $_GET["estado"] !== ""
            ? (int) $_GET["estado"]
            : null;

        $bodegas = $this->model->getBodegas($estado);
        $this->jsonResponse(["status" => "ok", "data" => $bodegas]);
    }

    // Get a warehouse by its ID
    public function obtener($id = null): void {
        $id = $id ?? ($_GET["id"] ?? null);

        if ($id === null || !is_numeric($id)) {
            $this->jsonResponse(["status" => "error"], 400);
            return;
        }

        $bodega = $this->model->getBodegaById((int) $id);

        if (!$bodega) {
            $this->jsonResponse(["status" => "error"], 404);
            return;
        }

        $this->jsonResponse(["status" => "ok", "data" => $bodega]);
    }

    // Create a new warehouse
    public function crear(): void {
        $nombre = trim($_POST["nombre"] ?? "");

        if ($nombre === "") {
            $this->jsonResponse(["status" => "error"], 400);
            return;
        }

        $data = [
            "nombre" => $nombre,
            "ubicacion" => trim($_POST["ubicacion"] ?? ""),
            "descripcion" => trim($_POST["descripcion"] ?? ""),
            "estado" => 1
        ];

        $this->jsonResponse($this->model->crearBodega($data));
    }

    // Update an existing warehouse
    public function actualizar(): void {
        $id = $this->getPostId();
        if ($id === null) {
            return;
        }

        $nombre = trim($_POST["nombre"] ?? "");
        $ubicacion = trim($_POST["ubicacion"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");

        if ($nombre === "") {
            $this->jsonResponse(["status" => "error"], 400);
            return;
        }

        $data = [
            "id" => $id,
            "nombre" => $nombre,
            "ubicacion" => $ubicacion,
            "descripcion" => $descripcion
        ];

        $this->jsonResponse($this->model->actualizarBodega($data));
    }

    // Activate a warehouse: set state to 1
    public function activar(): void {
        $id = $this->getPostId();
        if ($id === null) {
            return;
        }

        $this->jsonResponse($this->model->cambiarEstado($id, 1));
    }

    // Deactivate a warehouse: set state to 0
    public function inactivar(): void {
        $id = $this->getPostId();
        if ($id === null) {
            return;
        }

        $this->jsonResponse($this->model->cambiarEstado($id, 0));
    }
}
