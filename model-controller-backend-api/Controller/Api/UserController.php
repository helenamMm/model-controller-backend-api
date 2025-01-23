<?php
//include "BaseController.php";
class UserController extends BaseController
{
    /** 
* "/user/list" Endpoint - Get list of users 
*/
    public function listAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userModel = new UserModel();
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $arrUsers = $userModel->getUsers($intLimit);
                $responseData = json_encode($arrUsers);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function insertAction(){
        //echo "existo";
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }
    
        // Validate and decode input
        if (!isset($inputData['correo']) || !isset($inputData['nombre']) || 
            !isset($inputData['apellido']) || !isset($inputData['contra']) || 
            !isset($inputData['foto_perfil'])) {
            throw new Exception("Invalid input.");
        }
    
        $correo = $inputData['correo'];
        $nombre = $inputData['nombre'];
        $apellido = $inputData['apellido'];
        $contra = $inputData['contra'];
        $foto_perfil = $inputData['foto_perfil'];
    
        $userModel = new UserModel();
        $result = $userModel->insertUser($correo, $nombre, $apellido, $contra, $foto_perfil);
    
        if ($result) {
            $responseData = json_encode(["message" => "Usuario agregado exitosamente."]);
        } else {
            throw new Exception("Fallo insertar nuevo usuario.");
        }
        } catch (Exception $e) {
        $strErrorDesc = $e->getMessage();
        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    
        if (!$strErrorDesc) {
        $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 201 OK']);
        } else {
        $this->sendOutput(json_encode(["message" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
        }   
    
    }

    public function listUserAction(){
        //echo "estoy dentro de listUserAction";
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);

        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }

        if (!isset($inputData['correo'])) {
            throw new Exception("Invalid input.");
        }

        $correo = $inputData['correo'];

        $userModel = new UserModel();
        $arrUsers = $userModel->getUser($correo);
        $responseData = json_encode($arrUsers);
        
        } catch (Error $e) {
            $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(["message" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
        }
    
    } 

    public function modifyAction(){
        //echo "existo";
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }
    
        // Validate and decode input
        if (!isset($inputData['correo']) || !isset($inputData['nombre']) || 
            !isset($inputData['apellido']) || !isset($inputData['contra']) || 
            !isset($inputData['foto_perfil'])) {
            throw new Exception("Invalid input.");
        }
    
        $correo = $inputData['correo'];
        $nombre = $inputData['nombre'];
        $apellido = $inputData['apellido'];
        $contra = $inputData['contra'];
        $foto_perfil = $inputData['foto_perfil'];
    
        $userModel = new UserModel();
        $result = $userModel->modifyUser($correo, $nombre, $apellido, $contra, $foto_perfil);

        //logica para hacer un select gerUser con el correo que se me da
        $arrUsers = $userModel->getUser($correo);
        $responseData = json_encode($arrUsers);
        } catch (Exception $e) {
        $strErrorDesc = $e->getMessage();
        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
        }
    
        if (!$strErrorDesc) {
        $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
        } else {
        $this->sendOutput(json_encode(["message" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
        }
    }   

    public function verifyAction()
    {
        $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }
    
        if (!isset($inputData['correo']) || !isset($inputData['contra'])) {
            throw new Exception("Invalid input.");
        }
        $correo = $inputData['correo'];
        $contra = $inputData['contra'];

        $userModel = new UserModel();
        $result = $userModel->verifyUser($correo, $contra);
        if ($result['is_valid'] === 1) {
            $responseData = json_encode(["message" => "Credenciales válidas."]);
            $statusHeader = 'HTTP/1.1 200 OK'; 
        } else {
            $responseData = json_encode(["message" => "Credenciales no válidas."]);
            $statusHeader = 'HTTP/1.1 401 Unauthorized'; 
        }
        }catch (Exception $e) {
            $strErrorDesc = $e->getMessage();
            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        
            if (!$strErrorDesc) {
                $this->sendOutput($responseData, ['Content-Type: application/json', $statusHeader]);
            } else {
                $this->sendOutput(json_encode(["message" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
            }
    
    }
}
?>