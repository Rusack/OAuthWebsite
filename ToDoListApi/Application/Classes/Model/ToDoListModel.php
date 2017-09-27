<?php
namespace Model;

class ToDoListModel
{
	public $idToDoList;
	public $idUser;
	public $name;


	public function __construct($idToDoList ,$idUser, $name){

		$this->idToDoList = $idToDoList;
		$this->idUser = $idUser;
		$this->name = $name;		
	}


	public static function findToDoListByUserId($id){
      $db = Db::getInstance();
      // Check $id value if this is an integer
      $id = intval($id);
      // Prepared request
      $req = $db->prepare('SELECT * FROM toDoList WHERE idUser = :id');
      $req->execute(array('id' => $id ));
      // Get result
      $res = $req->fetchAll();

      return $res;
    }

	public static function findToDoListById($id){
      $db = Db::getInstance();
      // Check $id value if this is an integer
      $id = intval($id);
      // Prepared request
      $req = $db->prepare('SELECT * FROM toDoList WHERE idToDoList = :id');
      $req->execute(array('id' => $id ));
      // Get result
      $res = $req->fetch();

      $toDoList = new ToDoListModel($res['idToDoList'], $res['idUser'], $res['name']);

      return $toDoList;
    }

	public static function insertToDoList($idUser ,$name){
      $db = Db::getInstance();

      // Replace special char with html equivalent
      $name = htmlentities($name, ENT_QUOTES);
      $idUser = intval($idUser);

      // Prepared request
      $req = $db->prepare(' CALL `AddToDoList`(:idUser, :name, @res);');   

      $req->bindParam(':idUser', $idUser, \PDO::PARAM_INT); 
      $req->bindParam(':name', $name, \PDO::PARAM_STR);

      $req->execute();

      $outputArray = $db->query('SELECT @res AS `res`;')->fetchAll();

      foreach($outputArray as $row)
      {
        print_r($row);
      if ($row['res'] == 1)
        $res = 1;
      elseif ($row['res'] == 0)
            $res = 0;
      else $res = -1;
      }

      return $res;
 
    }

    public static function deleteToDoList($id){

    	$db = Db::getInstance();

    	$id = intval($id);

    	$req = $db->prepare(' CALL `DeleteToDoList`(:id, @res);');   

      	$req->bindParam(':id', $id, \PDO::PARAM_INT);

        $req->execute();


      $outputArray = $db->query('SELECT @res AS `res`;')->fetchAll();

      foreach($outputArray as $row)
      {
        print_r($row);
      if ($row['res'] == 1)
        $res = 1;
      elseif ($row['res'] == 0)
            $res = 0;
      else $res = -1;
      }

      return $res;
 

    }

    public static function editToDoList($idUser ,$name){
      $db = Db::getInstance();

      // Replace special char with html equivalent
      $name = htmlentities($name, ENT_QUOTES);
      $idUser = intval($idUser);

      // Prepared request
      $req = $db->prepare(' CALL `EditToDoList`(:idUser, :name, @res);');   

      $req->bindParam(':name', $name, \PDO::PARAM_STR);
      $req->bindParam(':idUser', $idUser, \PDO::PARAM_INT); 

      $req->execute();

      $outputArray = $db->query('SELECT @res AS `res`;')->fetchAll();

      foreach($outputArray as $row)
      {
        print_r($row);
      if ($row['res'] == 1)
        $res = 1;
      elseif ($row['res'] == 0)
            $res = 0;
      else $res = -1;
      }

      return $res;
 
    }
}
