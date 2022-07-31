<?php
namespace Controladores;

use Exception;

require_once('../db/connect.php');

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PATCH, GET, POST, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller = new Empleado($link, $requestMethod);
$controller->httpMethod();

class Empleado
{
    private $requestMethod;

    private const SUCCESSHTTPSTATUS = 200;
    private $validationFailedHTTPStatus = 400;
    private const VALIDATIONERROR = 400;
    private const SERVERERROR = 500;

    private $dbconnect;

    private $response = [
        "status" => NULL,
        "message" => "error",
        "data" => []
    ];

    public function __construct($link, $requestMethod)
    {
        $this->requestMethod = $requestMethod;
        $this->dbconnect = $link;
        mysqli_report(MYSQLI_REPORT_ALL);
    }

    public function httpMethod()
    {
        switch ($this->requestMethod) {
            case 'GET':
                $this->getEmpleados();
                break;
            case 'POST':
                $this->addEmpleado();
                break;
            case 'PATCH':
                $this->updateEmpleado();
                break;                
            case 'DELETE':
                $this->deleteEmpleado();
                break;
            default:
                $this->deniedMethodRequest();
                break;
        }
    }

    public function deniedMethodRequest()
    {

        unset($this->response['data']);
        $this->response['message'] = "error";
        $this->response['status'] = 405;
        $this->response['detalis'] = 'Method Not Allowed ' . $_SERVER['REQUEST_METHOD'] . ' try another';
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }
    /**
     * GET
     *
     * Description:
     * Get 
     *
     * 
     */
    public function getEmpleados()
    {

        try {
            $this->response['data'] = $this->listEmpleados();
            $this->response['message'] = "OK";
            $this->response['status'] = self::SUCCESSHTTPSTATUS;
        } catch (Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = $this->validationFailedHTTPStatus;
        }
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }

    /**
     * POST
     *
     * Description:
     * Post 
     *
     * @param 
     */
    public function addEmpleado()
    {
        if ($this->validationData()['status'] === 'error') {
            $this->response['message'] = $this->validationData()['message'];
            $this->response['status'] = self::VALIDATIONERROR;
        } else {
            try {
                $data = [];
                if($this->storeEmpleados($data)) {
                    $this->response['message'] = "OK";
                    $this->response['status'] = self::SUCCESSHTTPSTATUS;
                } else {
                    $this->response['message'] = "Error al guardar";
                    $this->response['status'] = self::SERVERERROR;
                }
            } catch (Exception $e) {
                $this->response['message'] = $e->getMessage();
                $this->response['status'] = self::SERVERERROR;
            }
        }
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }

    /**
     * DELETE
     *
     * Description:
     * Delete 
     *
     * @param int id
     */
    public function deleteEmpleado()
    {

        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }

    /**
     * UPDATE
     *
     * Description:
     * Update 
     *
     * @param int id
     */
    public function updateEmpleado()
    {

        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }

    private function listEmpleados(){
        $usersQuery = "SELECT nombre, email, sexo, areas.nombre as area
                    FROM empleados 
                    INNER JOIN areas
                    ON empleados.area_id = areas.id
                    ORDER BY empleados.nombre";
        
        $result = mysqli_query($this->dbconnect, $usersQuery);
        $arrayResult = mysqli_fetch_array($result);
        
        $empleados = [];

        foreach ($arrayResult as $value) {
            $empleados = $value;
        }

        return $empleados;

    }

    private function validationData(){
        $return = ['status' => 'error',
                    'message' => 'Error de validación'];
        return $return;
    }

    private function storeEmpleados($data){
        
        $usersQuery = "INSERT INTO empleados () VALUES ();";
        
        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        
        mysqli_stmt_bind_param($stmt, 'ss', $orgUuid, $facilityId);

        mysqli_stmt_execute($stmt);
        
        $stmt->close();

    }

    /*private function completeDataFacility(string $facilityId = "", string $orgUuid = ""): array
    {

        $usersQuery = "
            SELECT  pt.firstName,
                    pt.lastName,
                    user.firstname,
                    user.lastname,
                    rel.id
            FROM patients pt
            INNER JOIN relations rel
                ON pt.patientId = rel.patient AND pt.orgUuid = rel.orgUuid
            INNER JOIN user
                ON rel.id = user.id
            WHERE pt.orgUuid = ?
            AND pt.facid = ?
            AND rel.id in (SELECT email FROM unsubscribed_emails)
        ";

        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        mysqli_stmt_bind_param($stmt, 'ss', $orgUuid, $facilityId);

        mysqli_stmt_execute($stmt);
        $result = $stmt->get_result();
        $stmt->close();

        $unsubscribedUsers = [];

        foreach ($result as $value) {
            $unsubscribedUsers[] = $value;
        }

        return $unsubscribedUsers;
    }*/

    
}
