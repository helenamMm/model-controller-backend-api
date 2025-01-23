<?php
class PublicacionController extends BaseController
{
  public function insertPublicacionAction()
  {
    $strErrorDesc = '';
    $responseData = '';
    try {
      $requestMethod = $_SERVER["REQUEST_METHOD"];
      $inputData = json_decode(file_get_contents("php://input"), true);

      if (strtoupper($requestMethod) != 'POST') {
          throw new Exception("Method not supported.");
      }

      // Validate and decode input
      if (!isset($inputData['correo']) || !isset($inputData['titulo']) || 
          !isset($inputData['nombre_tema']) || !isset($inputData['foto_portada']) || 
          !isset($inputData['instrucciones'])|| !isset($inputData['descripcion']) || 
          !isset($inputData['num_likes'])) {
          throw new Exception("Invalid input.");
      }
      $correo = $inputData['correo'];
      $titulo = $inputData['titulo'];
      $nombre_tema = $inputData['nombre_tema'];
      $foto_portada = $inputData['foto_portada'];
      $instrucciones = $inputData['instrucciones'];
      $descripcion = $inputData['descripcion'];
      $num_likes = $inputData['num_likes'];

      // Handle foto_proceso
      if (isset($inputData['foto_proceso'])) {
        //$fotoProcesoArray = json_decode($inputData['foto_proceso'], true);
        $fotoProcesoArray = $inputData['foto_proceso'];
        if (is_array($fotoProcesoArray)) {
            $foto_procesoConcatenated = implode('|', $fotoProcesoArray);
        } else {
            throw new Exception("Invalid foto_proceso format.");
        }
      } else {
        throw new Exception("No se pudo hacer el arreglo de blobs base64.");
      }

      // Insert data into the database
      $publicacionModel = new PublicacionModel();
      $result = $publicacionModel->insertPublicacion($correo, $titulo, $nombre_tema, $foto_portada, $instrucciones, $descripcion,
      $num_likes, $foto_procesoConcatenated); // Use $foto_procesoConcatenated here
      if ($result) {
        $responseData = json_encode(["message" => "Publicacion agregada exitosamente"]);
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
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {

                $publicacionModel = new PublicacionModel();
                $arrUsers = $publicacionModel->getTodasPublicaciones();
                //aqui tengo que ver como el ultimo elemento que se saque debo de convertirlo a un arreglo de strings 
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
  public function listPorUsuarioAction()
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

    $publicacionModel = new PublicacionModel();
    $arrUsers = $publicacionModel->getPublicacionesUsuario($correo);
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

  public function modifyAction()
  {
    $strErrorDesc = '';
        $responseData = '';
        try {
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        if (strtoupper($requestMethod) != 'POST') {
            throw new Exception("Method not supported.");
        }
    
        // Validate and decode input
        if (!isset($inputData['id_publicacion']) || !isset($inputData['correo']) || !isset($inputData['titulo']) || 
            !isset($inputData['nombre_tema']) || !isset($inputData['foto_portada']) || 
            !isset($inputData['descripcion'])||!isset($inputData['instrucciones']) || !isset($inputData['foto_proceso']) ) {
            throw new Exception("Invalid input.");
        }
        $id_publicacion = $inputData['id_publicacion'];
        $correo = $inputData['correo'];
        $titulo = $inputData['titulo'];
        $nombre_tema = $inputData['nombre_tema'];
        $foto_portada = $inputData['foto_portada'];
        $descripcion = $inputData['descripcion'];
        $instrucciones = $inputData['instrucciones'];
        $foto_proceso = $inputData['foto_proceso'];

        //falta cambiar todo a publicacion 
       if ($foto_proceso != '') { 
          //$fotoProcesoArray = $inputData['foto_proceso'];
          if (is_array($foto_proceso)) {
              $foto_proceso = implode('|', $foto_proceso);
               
          } 
         else {
          throw new Exception("No se pudo hacer el arreglo de blobs base64.");
        }
      }
        $publicacionModel = new PublicacionModel();
        $result = $publicacionModel->modifyPublicacion($id_publicacion, $correo, $titulo, $nombre_tema, $foto_portada, $descripcion, $instrucciones, $foto_proceso);

        if ($result) {
          $responseData = json_encode(["message" => "Publicacion modificada exitosamente"]);
        } else {
          throw new Exception("Fallo insertar nueva publicacion.");
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

    if (!isset($inputData['id_publicacion'])|| !isset($inputData['correo'])) {
        throw new Exception("Invalid input.");
    }
    $id_publicacion = $inputData['id_publicacion'];
    $correo = $inputData['correo'];

    $publicacionModel = new PublicacionModel();
    $result = $publicacionModel->deletePublicacion($id_publicacion, $correo);
    if ($result) {
      $responseData = json_encode(["message" => "Publicacion eliminada exitosamente"]);
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
  public function orderAscAction()
  {
    $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {

                $publicacionModel = new PublicacionModel();
                $arrUsers = $publicacionModel->orderAscPublicacion();
                //aqui tengo que ver como el ultimo elemento que se saque debo de convertirlo a un arreglo de strings 
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

  public function orderDescAction()
  {
    $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {

                $publicacionModel = new PublicacionModel();
                $arrUsers = $publicacionModel->orderDescPublicacion();
                //aqui tengo que ver como el ultimo elemento que se saque debo de convertirlo a un arreglo de strings 
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
}
?>
