<?php
require_once "../Gestion-inventario/Config/database.php";
require_once "../models/solicitudes.php";

class SolicitudMaterialController {

    private $model;

    public function __construct($conn)
    {
        $this->model = new SolicitudMaterialModel($conn);
    }

    // Create request with details
    public function crear($data)
    {
        try {
            $this->model->begin();

            $idSolicitud = $this->model->createSolicitudes($data);
            $this->model->addDetalle($idSolicitud, $data['materiales']);

            $this->model->commit();

            return [
                "status" => "success",
                "message" => "Solicitud creada correctamente.",
                "id_solicitud" => $idSolicitud
            ];

        } catch (Exception $e) {
            $this->model->rollback();

            return [
                "status" => "error",
                "message" => "Error al crear la solicitud."
            ];
        }
    }

    public function obtener($id)
    {
        return $this->model->getById($id);
    }
}


/* ACTION HANDLER */

$controller = new SolicitudMaterialController($conn);

$accion = $_GET['accion'] ?? null;

function sendJSON($data)
{
    header("Content-Type: application/json");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($accion) {

    case "crear":
        $data = json_decode(file_get_contents("php://input"), true);
        sendJSON($controller->crear($data));
        break;

    case "obtener":
        sendJSON($controller->obtener($_GET['id']));
        break;

    default:
        sendJSON([
            "status" => "error",
            "message" => "Acción no válida."
        ]);
        
    //borrar
}
