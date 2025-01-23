<?php
class FavoritoController extends BaseController
{
  public function insertFavoritoAction()
  {
    $strErrorDesc = '';
    $responseData = '';
    try {
      $requestMethod = $_SERVER["REQUEST_METHOD"];
      $inputData = json_decode(file_get_contents("php://input"), true);

      if (strtoupper($requestMethod) != 'POST') {
          throw new Exception("Method not supported.");
      }

      if (!isset($inputData['id_publicacion']) || !isset($inputData['correo'])) {
          throw new Exception("Invalid input.");
      }

      $id_publicacion = $inputData['id_publicacion'];
      $correo = $inputData['correo'];
      
      $FavoritoModel = new FavoritoModel();
      $result = $FavoritoModel->insertFavorito($id_publicacion, $correo);
      if ($result) {
        $responseData = json_encode(["message" => "Favorito agregada exitosamente"]);
      } else {
        throw new Exception("Fallo insertar nueva publicacion.");
      }
    } catch (Exception $e) { 
      $strErrorDesc = $e->getMessage();
      $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
    }
    if (!$strErrorDesc) {
      $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 201 OK']);
    } else {
      $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
    }   
  }

  public function listAction()
  {
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

    $FavoritoModel = new FavoritoModel();
    $arrUsers = $FavoritoModel->getFavorito($correo);
    if ($arrUsers && is_array($arrUsers)) {
      foreach ($arrUsers as &$record) {
          if (!empty($record['foto_proceso'])) {
              $record['foto_proceso'] = explode('|', $record['foto_proceso']);
          } else {
              $record['foto_proceso'] = []; 
          }
      }
      unset($record); 
  }
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
        $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
    }
  }

  public function eliminarAction()
  {
    $strErrorDesc = '';
    $responseData = '';
    try {
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    $inputData = json_decode(file_get_contents("php://input"), true);

    if (strtoupper($requestMethod) != 'POST') {
        throw new Exception("Method not supported.");
    }

    if (!isset($inputData['id_publicacion']) || !isset($inputData['correo'])) {
        throw new Exception("Invalid input.");
    }
    $id_publicacion = $inputData['id_publicacion'];
    $correo = $inputData['correo'];

    $FavoritoModel = new FavoritoModel();
    $result = $FavoritoModel->deleteFavorito($id_publicacion, $correo);
    if ($result) {
      $responseData = json_encode(["message" => "Favorito eliminada exitosamente"]);
    } else {
      throw new Exception("Fallo eliminar nueva publicacion.");
    }
   } catch (Exception $e) {
    $strErrorDesc = $e->getMessage();
    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
    }

    if (!$strErrorDesc) {
    $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
    } else {
    $this->sendOutput(json_encode(["error" => $strErrorDesc]), ['Content-Type: application/json', $strErrorHeader]);
    }
 
  }
}
?>
