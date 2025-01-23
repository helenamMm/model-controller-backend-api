<?php
//include "inc/config.php";
class Database
{
    protected $connection = null;
    public function __construct()
    {
        try {
            $this->connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE_NAME);
    	
            if ( mysqli_connect_errno()) {
                throw new Exception("Could not connect to database.");   
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());   
        }			
    }
    public function select($query = "" , $params = [])
    {
        try {
            $stmt = $this->executeStatement( $query , $params );
            $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);			
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    public function selectMultipleParams($query = "" , $params = [])
    {
        try {
            $stmt = $this->executeMultipleParams( $query , $params );
            $result = $stmt->get_result()->fetch_assoc();		
            $stmt->close();
            return $result;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }
        return false;
    }

    public function insert($query = "" , $params = [])
    {
        try {
            $stmt = $this->executeMultipleParams($query, $params);          
            $stmt->close();
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return false;
    }

     public function delete($query = "", $params = [])
    {
        try {
        
        $stmt = $this->executeMultipleParams($query, $params);
        
        if ($stmt->affected_rows > 0) {
            return true;  
        } else {
            return false; 
        }
        } catch (Exception $e) {
        throw new Exception($e->getMessage());
        }
    }
    
    private function executeStatement($query = "" , $params = [])
    {
        try {
            $stmt = $this->connection->prepare( $query );
            if($stmt === false) {
                throw New Exception("Unable to do prepared statement: " . $query);
            }
            if( $params ) {
                $stmt->bind_param($params[0], $params[1]);
            }
            $stmt->execute();
            return $stmt;
        } catch(Exception $e) {
            throw New Exception( $e->getMessage() );
        }	
    }

    private function executeMultipleParams($query = "", $params = [])
    {
        try {
            
            //echo "Query: " . $query . "<br>";
            $stmt = $this->connection->prepare($query);
            
            if ($stmt === false) {
                echo "Error preparing statement: " . $this->connection->error . "<br>";
                throw new Exception("Unable to prepare statement: " . $this->connection->error);
            }
    
            $types = '';
            $values = [];
    
            foreach ($params as $param) {
                $types .= $param[0];  
                $values[] = $param[1];  
            }
            
            /* echo "Types: " . $types . "<br>";
            echo "Values: ";
            print_r($values);
            echo "<br>"; */

            
            $stmt->bind_param($types, ...$values);
    
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            // cualquier error que se de la execucion
            echo "Error: " . $e->getMessage();
            throw new Exception($e->getMessage());
        }
    }
     public function getLastInsertId()
    {
        return $this->connection->insert_id; 
    }
}
?>
