<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../Config/database.php";
require_once __DIR__ . "/../models/solicitudes.php";


class SolicitudMaterialController {

    private $model;

    public function __construct($conn)
    {
        $this->model = new SolicitudMaterialModel($conn);
    }

    // Create request with details
    public function crear($data)
    {
        // Basic validation (business rule)
        if (empty($data['materiales']) || !is_array($data['materiales'])) {
            return [
                "success" => false,
                "error" => "La solicitud debe contener al menos un material."
            ];
        }

        try {
            $this->model->begin();

            $idSolicitud = $this->model->createSolicitudes($data);
            $this->model->addDetalle($idSolicitud, $data['materiales']);

            $this->model->commit();

            return [
                "success" => true,
                "message" => "Solicitud creada correctamente.",
                "id_solicitud" => $idSolicitud
            ];

        } catch (Exception $e) {
            $this->model->rollback();

            return [
                "success" => false,
                "error" => "Error al crear la solicitud: " . $e->getMessage()
            ];
        }
    }

    // Approve or reject request
    public function responder($data)
    {
        $ok = $this->model->responderSolicitud(
            $data['id_solicitud'],
            $data['estado'],
            $data['id_usuario_aprobador'],
            $data['observaciones'] ?? null
        );
    
        if (!$ok) {
            return [
                "success" => false,
                "error" => "No se pudo responder la solicitud. Verifique su estado."
            ];
        }
    
        return [
            "success" => true,
            "message" => "La solicitud fue actualizada correctamente."
        ];
    }

    // Mark request as delivered
    public function entregar($data)
    {
        $ok = $this->model->marcarEntregada(
            $data['id_solicitud'],
            $data['id_usuario']
        );
    
        if (!$ok) {
            return [
                "success" => false,
                "error" => "La solicitud no puede marcarse como entregada."
            ];
        }
    
        return [
            "success" => true,
            "message" => "La solicitud fue marcada como entregada correctamente."
        ];
    }

    public function obtener($id)
    {
        return $this->model->getById($id);
    }

    public function obtenerCompleta($id)
    {
        return $this->model->getSolicitudCompleta($id);
    }

    // List all requests
    public function listar()
    {
        return $this->model->getAll();
    }

    // ============================================
    // NUEVAS FUNCIONES PARA LOS SELECTORES
    // ============================================
    
    public function obtenerProgramas()
    {
        return $this->model->getProgramas();
    }

    public function obtenerRaes($programaId)
    {
        return $this->model->getRaesPorPrograma($programaId);
    }

    public function obtenerFichas($programaId)
    {
        return $this->model->getFichasPorPrograma($programaId);
    }

    public function obtenerMateriales()
    {
        return $this->model->getMateriales();
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

    case "responder":
        $data = json_decode(file_get_contents("php://input"), true);
        sendJSON($controller->responder($data));
        break;
    
    case "entregar":
        $data = json_decode(file_get_contents("php://input"), true);
        sendJSON($controller->entregar($data));
        break;
    
    case "obtenerCompleta":
        sendJSON($controller->obtenerCompleta($_GET['id']));
        break;

    case "listar":
        sendJSON($controller->listar());
        break;

    // ============================================
    // NUEVOS CASOS PARA LOS SELECTORES
    // ============================================
    
    case "programas":
        sendJSON($controller->obtenerProgramas());
        break;

    case "raes":
        $programaId = isset($_GET['programa']) ? (int) $_GET['programa'] : 0;
        sendJSON($controller->obtenerRaes($programaId));
    break;


    case "fichas":
        $programaId = $_GET['programa'] ?? 0;
        sendJSON($controller->obtenerFichas($programaId));
        break;

    case "materiales":
        sendJSON($controller->obtenerMateriales());
        break;

    default:
        sendJSON([
            "success" => false,
            "error" => "Acción no válida."
        ]);
}