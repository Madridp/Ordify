<?php 

class Db
{
  private $link;
  private $engine;
  private $host;
  private $name;
  private $user;
  private $pass;
  private $charset;
  private $options;
  
  /**
   * Constructor para nuestra clase
   */
  public function __construct()
  {
    $this->engine  = IS_LOCAL ? LDB_ENGINE : DB_ENGINE;
    $this->name    = IS_LOCAL ? LDB_NAME : DB_NAME;
    $this->user    = IS_LOCAL ? LDB_USER : DB_USER;
    $this->pass    = IS_LOCAL ? LDB_PASS : DB_PASS;
    $this->charset = IS_LOCAL ? LDB_CHARSET : DB_CHARSET;
    $this->host    = IS_LOCAL ? LDB_HOST : DB_HOST;
    $this->options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
    
    return $this;    
  }

  /**
   * Método para abrir una conexión a la base de datos
   *
   * @return mixed
   */
  private static function connect() 
  {
    try {
      $db       = new self();
      $db->link = new PDO($db->engine.':host='.$db->host.';dbname='.$db->name.';charset='.$db->charset, $db->user, $db->pass, $db->options);
      return $db->link;
    } catch (PDOException $e) {
      die(sprintf('No  hay conexión a la base de datos, hubo un error: %s', $e->getMessage()));
    }
  }

  /**
   * Método para hacer un query a la base de datos
   *
   * @param string $sql
   * @param array $params
   * @return mixed
   */
  public static function query($sql, $params = [])
  {
    $link = self::connect(); // nuestra conexión a la db
    $link->beginTransaction(); // por cualquier error, checkpoint
    $query = $link->prepare($sql);
    
    try {

      // Manejando errores en el query o la petición
      // SELECT * FROM usuarios WHERE id=:cualquier AND name = :name;
      $query->execute($params);
  
      // SELECT | INSERT | UPDATE | DELETE | ALTER TABLE
      // Manejando el tipo de query
      // SELECT * FROM usuarios;
      if(strpos($sql, 'SELECT') !== false) {
        
        return $query->rowCount() > 0 ? $query->fetchAll() : false; // no hay resultados
  
      } elseif(strpos($sql, 'INSERT') !== false) {
  
        $id = $link->lastInsertId();
        $link->commit();
        return $id;
  
      } elseif(strpos($sql, 'UPDATE') !== false) {
  
        $link->commit();
        return true;
  
      } elseif(strpos($sql, 'DELETE') !== false) {
  
        if($query->rowCount() > 0) {
          $link->commit();
          return true;
        }
        
        $link->rollBack();
        return false; // Nada ha sido borrado
  
      } else {
  
        // ALTER TABLE | DROP TABLE 
        $link->commit();
        return true;
        
      }
    } catch (PDOException $e) {
      $error = $query->errorInfo();
      $link->rollBack();

      // index 0 es el tipo de error
      // index 1 es el código de error
      // index 2 es el mensaje de error al usuario
      throw new Exception(sprintf('Hubo un error: %s<br><small>%s</small>', $e->getMessage(), $error[2]));
    }
  }
}