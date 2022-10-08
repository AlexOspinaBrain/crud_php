<?php
namespace Controladores;

use \Modelos\Rol;
require_once('../Modelos/Rol.php');

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class RolController extends Rol
{
    private $requestMethod;

    private const SUCCESSHTTPSTATUS = 200;
    private const TRYERROR = 400;

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
                $this->getRoles();
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
    public function getRoles()
    {
        try {
            $this->response['data'] = $this->listRoles();
            $this->response['message'] = "OK";
            $this->response['status'] = self::SUCCESSHTTPSTATUS;
        } catch (\Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = self::TRYERROR;
        }
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
    }

   
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller = new RolController($requestMethod);
$controller->httpMethod();