<?php
require_once __DIR__ . "/../models/bodega.php";

/**
 * Controlador REST para las bodegas.
 */
class BodegaController {

    /**
     * @var BodegaModel
     */
    private $model;

    public function __construct(PDO $conn) {
        $this->model = new BodegaModel($conn);
    }

    /**
     * Envía una respuesta JSON uniforme.
     */
    private function jsonResponse(array $payload, int $statusCode = 200): void {
        http_response_code($statusCode);
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Obtiene y valida el ID recibido por POST.
     */
    private function getPostId(): ?int {
        if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
            $this->jsonResponse(["status" => "error", "msg" => "ID requerido o inválido"], 400);
            return null;
        }

        return (int) $_POST["id"];
    }

    /* ================================
       LISTAR
    ================================ */
    public function listar(): void {
        $estado = isset($_GET["estado"]) && $_GET["estado"] !== ""
            ? (int) $_GET["estado"]
            : null;

        $bodegas = $this->model->getBodegas($estado);
        $this->jsonResponse(["status" => "ok", "data" => $bodegas]);
    }

    /* ================================
       OBTENER
    ================================ */
    public function obtener($id = null): void {
        $id = $id ?? ($_GET["id"] ?? null);
        if ($id === null || !is_numeric($id)) {
            $this->jsonResponse(["status" => "error", "msg" => "Debe enviar un ID válido"], 400);
            return;
        }

        $bodega = $this->model->getBodegaById((int) $id);
        if (!$bodega) {
            $this->jsonResponse(["status" => "error", "msg" => "Bodega no encontrada"], 404);
            return;
        }

        $this->jsonResponse(["status" => "ok", "data" => $bodega]);
    }

    /* ================================
       CREAR
    ================================ */
    public function crear(): void {
        $nombre = trim($_POST["nombre"] ?? "");
        if ($nombre === "") {
            $this->jsonResponse(["status" => "error", "msg" => "Nombre requerido"], 400);
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

    /* ================================
       ACTUALIZAR
    ================================ */
    public function actualizar(): void {
        $id = $this->getPostId();
        if ($id === null) {
            return;
        }

        $nombre = trim($_POST["nombre"] ?? "");
        $ubicacion = trim($_POST["ubicacion"] ?? "");
        $descripcion = trim($_POST["descripcion"] ?? "");

        if ($nombre === "") {
            $this->jsonResponse(["status" => "error", "msg" => "Nombre requerido"], 400);
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

    /* ================================
       ACTIVAR
    ================================ */
    public function activar(): void {
        $id = $this->getPostId();
        if ($id === null) {
            return;
        }

        $this->jsonResponse($this->model->cambiarEstado($id, 1));
    }

    /* ================================
       INACTIVAR
    ================================ */
    public function inactivar(): void {
        $id = $this->getPostId();
        if ($id === null) {
            return;
        }

        $this->jsonResponse($this->model->cambiarEstado($id, 0));
    }
}
 