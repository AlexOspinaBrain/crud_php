<?php
namespace Controladores;

use \Modelos\Area;
require_once('../Modelos/Area.php');

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$requestMethod = $_SERVER["REQUEST_METHOD"];

class AreaController extends Area
{
    private $requestMethod;

    private const SUCCESSHTTPSTATUS = 200;
    private const TRYERROR = 400;
    private const SERVERERROR = 500;

    private $response = [
        "status" => NULL,
        "message" => "error",
        "data" => []
    ];

    public function __construct($requestMethod)
    {
        parent::__construct();
        $this->requestMethod = $requestMethod;
        
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

    private function deniedMethodRequest()
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
    private function getAreas()
    {
        try {
            $this->response['data'] = $this->listAreas();
            $this->response['message'] = "OK";
            $this->response['status'] = self::SUCCESSHTTPSTATUS;
        } catch (\Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = self::TRYERROR;
        }
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }

    
}

try {
    $controller = new AreaController($requestMethod);
    $controller->httpMethod();
}catch (\Error $e) {
        echo $e->getMessage();
    }