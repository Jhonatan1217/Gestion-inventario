<?php
require_once "../../config/database.php";
require_once "../models/material_formacion.php";


class MaterialFormacionController {

    private $model;

    public function __construct($conn)
    {
        $this->model = new MaterialFormacionModel($conn);
    }

 
    //   LIST MATERIALS
    public function listar()
    {
        return $this->model->getAll();
    }

 
    //   GET MATERIAL BY ID
    public function obtener($id)
    {
        return $this->model->getById($id);
    }

 
    //   CREATE MATERIAL
    public function crear($data)
    {
        // Model returns false when validation fails
        $ok = $this->model->create($data);

        if (!$ok) {
            return [
                "status" => "error",
                "message" => "Material cannot be created. Check classification and inventory code."
            ];
        }

        return [
            "status" => "success",
            "message" => "Material created successfully."
        ];
    }

 
    //   UPDATE MATERIAL
    public function actualizar($id, $data)
    {
        $ok = $this->model->update($id, $data);

        if (!$ok) {
            return [
                "status" => "error",
                "message" => "Material cannot be updated. Check classification and inventory code."
            ];
        }

        return [
            "status" => "success",
            "message" => "Material updated successfully."
        ];
    }

 
    //   DELETE MATERIAL
    public function eliminar($id)
    {
        $ok = $this->model->delete($id);

        if (!$ok) {
            return [
                "status" => "error",
                "message" => "Material cannot be deleted because it is used in other tables."
            ];
        }

        return [
            "status" => "success",
            "message" => "Material deleted successfully."
        ];
    }

 
    //   SEARCH MATERIAL
    public function buscar($term)
    {
        return $this->model->search($term);
    }

 
    //   MATERIAL STOCK
    public function stock($id)
    {
        return $this->model->getStockTotal($id);
    }
}


// API-LIKE ACTION HANDLER (switch)


$controller = new MaterialFormacionController($conn);

// read action
$action = $_GET['action'] ?? null;

// simple JSON response function
function sendJSON($data, $code = 200)
{
    header("Content-Type: application/json");
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($action) {

    case "listar":
        sendJSON($controller->listar());
        break;

    case "obtener":
        sendJSON($controller->obtener($_GET['id'] ?? null));
        break;

    case "buscar":
        sendJSON($controller->buscar($_GET['term'] ?? ""));
        break;

    case "stock":
        sendJSON($controller->stock($_GET['id'] ?? null));
        break;

    case "crear":
        $data = json_decode(file_get_contents("php://input"), true);
        sendJSON($controller->crear($data));
        break;

    case "actualizar":
        $id = $_GET['id'] ?? null;
        $data = json_decode(file_get_contents("php://input"), true);
        sendJSON($controller->actualizar($id, $data));
        break;

    case "eliminar":
        sendJSON($controller->eliminar($_GET['id'] ?? null));
        break;

    default:
        sendJSON(["status" => "error", "message" => "Unknown action"]);
        break;
}

?>