<?php
namespace Modelos;

require_once('../db/connect.php');

class Empleado
{
    private $dbconnect;

    public function __construct()
    {
        global $link;
        $this->dbconnect = $link;
        mysqli_report(MYSQLI_REPORT_ALL & ~MYSQLI_REPORT_INDEX);
    }

    protected function eliminaEmpleado($id){

        $usersQuery = "DELETE FROM empleado_rol
            WHERE empleado_id = ?";

        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        
        mysqli_stmt_bind_param($stmt, 'i', $id);

        mysqli_stmt_execute($stmt);
        $stmt->close();

        $usersQuery = "DELETE FROM empleados 
            WHERE id = ?";

        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        
        mysqli_stmt_bind_param($stmt, 'i', $id);

        mysqli_stmt_execute($stmt);
        
        $stmt->close();
    }

    protected function updateEmpleado($data, $id)
    {
        $usersQuery = "UPDATE empleados SET
            nombre = ?,
            email = ?,
            area_id = ?,
            sexo = ?,
            descripcion = ?,
            boletin= ?  
            WHERE id = ?";
    
        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        
        mysqli_stmt_bind_param($stmt, 'ssissii', $data['nombre'], 
            $data['email'], $data['area'], $data['sexo'], $data['descrip'], 
            $data['boletin'], $id);

        mysqli_stmt_execute($stmt);
        
        $stmt->close();

        if (count($data['roles']) > 0){
            $this->storeRolEmpleado($data['roles'], $id);
        }

    }

    protected function listEmpleados($id = 0){

        $filter = "";
        $filterRol = "";
        if ($id !== 0) {
            $filter = " WHERE empleados.id = '$id' ";
            $filterRol = " WHERE empleado_id = '$id' ";
        }
        $usersQuery = "SELECT empleados.id, empleados.nombre, descripcion, email, sexo, areas.id as idarea, areas.nombre as area,
                    boletin
                    FROM empleados 
                    INNER JOIN areas
                    ON empleados.area_id = areas.id
                    $filter
                    ORDER BY empleados.nombre";
        
        $result = mysqli_query($this->dbconnect, $usersQuery);
        $arrayResult = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        $usersQuery = "SELECT empleado_id, rol_id
                FROM empleado_rol
                $filterRol";

        $result = mysqli_query($this->dbconnect, $usersQuery);
        $arrayResultRoles = mysqli_fetch_all($result, MYSQLI_ASSOC);

        return [$arrayResult, $arrayResultRoles];

    }

    protected function storeEmpleados($data){
        
        $usersQuery = "INSERT INTO empleados (nombre,email,area_id,sexo,descripcion,boletin) 
            VALUES (?,?,?,?,?,?);";
        
        $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
        
        mysqli_stmt_bind_param($stmt, 'ssissi', $data['nombre'], 
            $data['email'], $data['area'], $data['sexo'], $data['descrip'], 
            $data['boletin']);

        mysqli_stmt_execute($stmt);
        $id=$stmt->insert_id;
        $stmt->close();
        
        if (count($data['roles']) > 0){
            $this->storeRolEmpleado($data['roles'], $id);
        }
    }

    private function storeRolEmpleado($roles, $id = 0){

        for($i=0; $i< count($roles); $i++){
            $usersQuery = "INSERT INTO empleado_rol (empleado_id,rol_id) 
            VALUES (?,?);";

            $stmt = mysqli_prepare($this->dbconnect, $usersQuery);
                    
            mysqli_stmt_bind_param($stmt, 'ii', $id, $roles[$i]);

            mysqli_stmt_execute($stmt);

            $stmt->close();
        }

    }
    
}
