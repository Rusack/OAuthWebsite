<?php
namespace Model;

class TaskModel
{
  public $idTask;
	public $idToDoList;
	public $description;
	public $deadline;


	public function __construct($idTask, $idToDoList ,$description, $deadline){

    $this->idTask = $idTask;
		$this->idToDoList = $idToDoList;
		$this->description = $description;
		$this->deadline = $deadline;		
	}


    public static function findTaskByListId($id){
      $db = Db::getInstance();
      // Check $id value if this is an integer
      $id = intval($id);
      // Prepared request
      $req = $db->prepare('SELECT * FROM task WHERE idToDoList = :id');
      $req->execute(array('id' => $id ));
      // Get result
      $res = $req->fetchAll();

      return $res;
    }

	public static function findTaskById($id){
      $db = Db::getInstance();
      // Check $id value if this is an integer
      $id = intval($id);
      // Prepared request
      $req = $db->prepare('SELECT * FROM task WHERE idtask = :id');
      $req->execute(array('id' => $id ));
      // Get result
      $res = $req->fetch();

      $toDoList = new TaskModel($res['idTask'] ,$res['idToDoList'], $res['description'], $res['deadline']);

      return $toDoList;
    }

	public static function insertTask($idToDoList ,$description, $deadline){
      $db = Db::getInstance();

      // Replace special char with html equivalent
      $description = htmlentities($description, ENT_QUOTES);
      $idToDoList = intval($idToDoList);
      $deadline = date('Y-m-d',strtotime(str_replace('/','-', $deadline)));

      // Prepared request
      $req = $db->prepare(' CALL `AddTask`(:idToDoList, :description, :deadline, @res);');   

      $req->bindParam(':idToDoList', $idToDoList, \PDO::PARAM_INT); 
      $req->bindParam(':description', $description, \PDO::PARAM_STR);
      $req->bindParam(':deadline', $deadline, \PDO::PARAM_STR);

      $req->execute();

      $outputArray = $db->query('SELECT @res AS `res`;')->fetchAll();

      foreach($outputArray as $row)
      {
      if ($row['res'] == 1)
        $res = 1;
      elseif ($row['res'] == 0)
            $res = 0;
      else $res = -1;
      }

      return $res;
 
    }

    public static function deleteTask($id){

    	$db = Db::getInstance();

    	$id = intval($id);

    	$req = $db->prepare(' CALL `DeleteTask`(:id, @res);');   

      $req->bindParam(':id', $id, \PDO::PARAM_INT);

      $req->execute();

      $outputArray = $db->query('SELECT @res AS `res`;')->fetchAll();

      foreach($outputArray as $row)
      {
      if ($row['res'] == 1)
        $res = 1;
      elseif ($row['res'] == 0)
            $res = 0;
      else $res = -1;
      }

      return $res;
 

    }

    public static function editTask($idToDoList ,$description, $deadline){
      $db = Db::getInstance();

      // Replace special char with html equivalent
      $description = htmlentities($description, ENT_QUOTES);
      $idToDoList = intval($idToDoList);

      // Prepared request
      $req = $db->prepare(' CALL `EditTask`(:idToDoList, :description, :deadline, @res);');   

      $req->bindParam(':idToDoList', $name, \PDO::PARAM_STR);
      $req->bindParam(':description', $idUser, \PDO::PARAM_INT); 
      $req->bindParam(':deadline', $deadline, \PDO::PARAM_STR); 

      $req->execute();

      $outputArray = $db->query('SELECT @res AS `res`;')->fetchAll();

      foreach($outputArray as $row)
      {
      if ($row['res'] == 1)
        $res = 1;
      elseif ($row['res'] == 0)
            $res = 0;
      else $res = -1;
      }

      return $res;
 
    }
}
