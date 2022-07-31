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
        mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
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
            $this->response['data'] = $this->listEmpleados($_GET['id'] ?? 0);
            $this->response['message'] = "OK";
            $this->response['status'] = self::SUCCESSHTTPSTATUS;
        } catch (Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = self::VALIDATIONERROR;
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
        $validation = $this->validationData();
        if ($validation['status'] === 'error') {
            $this->response['message'] = $validation['message'];
            $this->response['status'] = self::VALIDATIONERROR;
        } else {
            try {
                $this->storeEmpleados($validation['data']);
                $this->response['status'] = self::SUCCESSHTTPSTATUS;
                $this->response['message'] = "Empleado Guardado";
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

    private function listEmpleados($id = 0){

        $filter = "";
        if ($id !== 0) {
            $filter = " WHERE empleados.id = '$id' ";
        }
        $usersQuery = "SELECT empleados.id, empleados.nombre, email, sexo, areas.nombre as area,
                    boletin
                    FROM empleados 
                    INNER JOIN areas
                    ON empleados.area_id = areas.id
                    $filter
                    ORDER BY empleados.nombre";
        
        $result = mysqli_query($this->dbconnect, $usersQuery);
        $arrayResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        return $arrayResult;

    }

    private function validationData(){
        $return = ['status' => 'ok',
                    'message' => '',
                    'data' => ''];
        
        $nombre = strtoupper($_POST['nombre']) ?? '';
        $email = strtolower($_POST['email']) ?? '';
        $area = $_POST['area'] ?? '';
        $sexo = strtoupper($_POST['sexo']) ?? '';
        $descrip = $_POST['descrip'] ?? '';
        $boletin = $_POST['boletin'] ?? '';

        if ($nombre === '') {
            $return['message'] .= 'Nombre Requerido,';
            $return['status'] = 'error';
        } else {
            setlocale(LC_ALL, "es_ES.ISO-8859-1");
            if (preg_match("/^[a-zA-Z\sñáéíóúÁÉÍÓÚ]+$/", $nombre) == 0){
                $return['message'] .= 'Nombre Invalido,';
                $return['status'] = 'error';
            }
        }
        
        if ($email === '') {
            $return['message'] .= 'Email Requerido,';
            $return['status'] = 'error';
        } else {
            if (preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $email) == 0){
                $return['message'] .= 'Email Invalido,';
                $return['status'] = 'error';
            }
        }

        if ($area === 'NO') {
            $return['message'] .= 'Area Requerida,';
            $return['status'] = 'error';
        }

        if ($sexo === '') {
            $return['message'] .= 'Sexo Requerido,';
            $return['status'] = 'error';
        } else {
            if ($sexo !== 'M' && $sexo !== 'F'){
                $return['message'] .= 'Sexo Invalido,';
                $return['status'] = 'error';
            }
        }

        if ($descrip === '') {
            $return['message'] .= 'Descripcón Requerida,';
            $return['status'] = 'error';
        }

        if ($return['status'] !== 'error'){
            $return['data'] = [        
                'nombre' => $nombre,
                'email' => $email,
                'area' => $area,
                'sexo' => $sexo,
                'descrip' => $descrip,
                'boletin' => $boletin,
            ];
        }

        return $return;
    }

    private function storeEmpleados($data){
        
        $usersQuery = "INSERT INTO empleados (nombre,email,area_id,sexo,descripcion,boletin) 
            VALUES (?,?,?,?,?,?);";
        
        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        
        mysqli_stmt_bind_param($stmt, 'ssissi', $data['nombre'], 
            $data['email'], $data['area'], $data['sexo'], $data['descrip'], 
            $data['boletin']);

        mysqli_stmt_execute($stmt);
        
        $stmt->close();

    }


    
}
