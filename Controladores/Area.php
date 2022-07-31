<?php
namespace Controladores;

use Exception;

require_once('../db/connect.php');

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller = new Area($link, $requestMethod);
$controller->httpMethod();

class Area
{
    private $requestMethod;

    private const SUCCESSHTTPSTATUS = 200;
    private const TRYERROR = 400;
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
                $this->getAreas();
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
    public function getAreas()
    {
        try {
            $this->response['data'] = $this->listAreas();
            $this->response['message'] = "OK";
            $this->response['status'] = self::SUCCESSHTTPSTATUS;
        } catch (Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = self::TRYERROR;
        }
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }


    private function listAreas(){
        $usersQuery = "SELECT id, nombre
                    FROM areas
                    ORDER BY id";
        
        $result = mysqli_query($this->dbconnect, $usersQuery, MYSQLI_USE_RESULT);


        $arrayResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        return $arrayResult;

    }

    
    
}
