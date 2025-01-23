<?php
class FavoritoModel extends Database
{
  public function insertFavorito($id_publicacion, $correo)
  {
      $query1 = "INSERT INTO Favoritos(id_usuarios, id_publicacion)
        VALUES(?, ?);";
      $params1 = [
      ['s', $correo],
      ["i", $id_publicacion]
      ];

      $this->insert($query1, $params1); 

      $query2 = "UPDATE publicacion
        SET num_likes = (num_likes + 1)
        WHERE id_publicacion = ?;";
      $params2 = [["i", $id_publicacion]];
    
      $this->insert($query2, $params2);
    
      return true;
  }

  public function getFavorito($correo)
  {
    $query = "SELECT 
       Pub.id_publicacion, 
       Pub.id_usuarios, 
       Pub.titulo, 
       tema.nombre_tema, 
       Pub.foto_portada, 
       Pub.descripcion, 
       Pub.num_likes, 
       Pub.estado, 
       Pub.fecha_creacion, 
       Pub.instrucciones,
        fotos_publicacion.imagen as foto_proceso
   FROM 
       publicacion AS Pub
   JOIN 
       tema ON tema.id_tema = Pub.id_tema
   JOIN 
       fotos_publicacion ON fotos_publicacion.id_publicacion = Pub.id_publicacion 
	JOIN 
       usuarios ON usuarios.correo = Pub.id_usuarios
	JOIN 
		Favoritos ON Favoritos.id_publicacion = Pub.id_publicacion
   WHERE 
       Favoritos.id_usuarios = ?;";
    $params = ["s", $correo];

    return $this->select($query, $params);
  }

  public function deleteFavorito($id_publicacion, $correo)
  {
    $query1 = "UPDATE publicacion
        SET
        num_likes = num_likes - 1
        WHERE id_publicacion = ?;";
    $params1 = [["i", $id_publicacion]];
    
    $this->insert($query1, $params1);

    $query2 = "DELETE FROM Favoritos
    WHERE  id_usuarios = ? AND id_publicacion = ?;";
    $params2 = [
      ['s', $correo],
      ["i", $id_publicacion]
    ];
    $this->insert($query2, $params2);

    return true;
  }
}
?>

