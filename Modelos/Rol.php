<?php
namespace Modelos;

require_once('../db/connect.php');

class Rol {

    private $dbconnect;

    public function __construct()
    {
        global $link;
        $this->dbconnect = $link;
        mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);    
    } 
    
    public function listRoles(){
        $usersQuery = "SELECT id, nombre
                    FROM roles
                    ORDER BY id";
        
        $result = mysqli_query($this->dbconnect, $usersQuery, MYSQLI_USE_RESULT);


        $arrayResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        return $arrayResult;

    }

}