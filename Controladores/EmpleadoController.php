<?php
namespace Controladores;

use \Modelos\Empleado;

require_once('../Modelos/Empleado.php');

// Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class EmpleadoController extends Empleado
{
    private $requestMethod;

    private const SUCCESSHTTPSTATUS = 200;
    private const VALIDATIONERROR = 400;
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
                $this->getEmpleados();
                break;
            case 'POST':
                $this->addEmpleado();
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
        } catch (\Exception $e) {
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
                if ($_POST['idEmpleado'] != ""){
                    $this->updateEmpleado($validation['data'], $_POST['idEmpleado']);
                } else {
                    $this->storeEmpleados($validation['data']);
                }
                $this->response['status'] = self::SUCCESSHTTPSTATUS;
                $this->response['message'] = "Empleado Guardado";
            } catch (\Exception $e) {
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
        $params=[];
        $this->parse_raw_http_request($params);

        try {
            $this->eliminaEmpleado($params['id']);
            $this->response['status'] = self::SUCCESSHTTPSTATUS;
            $this->response['message'] = "Empleado Eliminado";
        } catch (\Exception $e) {
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = self::SERVERERROR;
        }

        
        exit(json_encode($this->response, JSON_UNESCAPED_UNICODE));
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

        $roles = explode(",", $_POST['roles']);

        if ($nombre === '') {
            $return['message'] .= 'Nombre Requerido,';
            $return['status'] = 'error';
        } else {
            setlocale(LC_ALL, "es_ES.ISO-8859-1");
            if (preg_match("/^[a-zA-Z\s??????????????????????]+$/", $nombre) == 0){
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
            $return['message'] .= 'Descripc??n Requerida,';
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
                'roles' => $roles,
            ];
        }

        return $return;
    }


    /**
     * Parse raw HTTP request data
     *
     * Pass in $a_data as an array. This is done by reference to avoid copying
     * the data around too much.
     *
     * Any files found in the request will be added by their field name to the
     * $data['files'] array.
     *
     * @ref: http://www.chlab.ch/blog/archives/webdevelopment/manually-parse-raw-http-data-php
     *
     * @param   array  Empty array to fill with data
     * @return  array  Associative array of request data
     */
    private function parse_raw_http_request(array &$a_data)
    {
        // read incoming data
        $input = file_get_contents('php://input');

        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

        // content type is probably regular form-encoded
        if (!count($matches))
        {
            // we expect regular puts to containt a query string containing data
            parse_str(urldecode($input), $a_data);
            return $a_data;
        }

        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);

        // loop data blocks
        foreach ($a_blocks as $id => $block)
        {
            if (empty($block))
            continue;

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== FALSE)
            {
            // match "name", then everything after "stream" (optional) except for prepending newlines
            preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            $a_data['files'][$matches[1]] = $matches[2];
            }
            // parse all other fields
            else
            {
            // match "name" and optional value in between newline sequences
            preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            $a_data[$matches[1]] = $matches[2];
            }
        }
    }
    
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller = new EmpleadoController($requestMethod);
$controller->httpMethod();
