<?php 
namespace Model;
/**
* User model, handle request relative to user table
*/
class UserModel 
{
	public $idUser;
	public $username;
	public $firstName;
	public $lastName;
	public $mail;
  public $security_level;


	public function __construct($idUser, $username, $firstName, $lastName, $mail, $security_level){

		$this->idUser = $idUser;
    $this->username = $username;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->mail = $mail;
    $this->security_level = $security_level;
			
	}

	public static function checkUser($username, $passwordTry){

	  $db = Db::getInstance();

      // Replace special char with html equivalent
      $username = htmlentities($username, ENT_QUOTES);
      $passwordTry = htmlentities($passwordTry, ENT_QUOTES);

      // Prepared request
      $req = $db->prepare('SELECT * FROM user WHERE username = :username');
      $req->execute(array('username' => $username));
      
      // Get result
      $res = $req->fetch();

      if ($res == false) {
      	// No user with this username
      	return -1;
      }
      else {

      	$validPassword = password_verify($passwordTry, $res['password']);
        
      	if ($validPassword) {
      		return $res['idUser'];
      	}
      	else
      		return -1;
      }
	}



	public static function findUserById($id){
      $db = Db::getInstance();
      // Check $id value if this is an integer
      $id = intval($id);
      // Prepared request
      $req = $db->prepare('SELECT * FROM user WHERE idUser = :id');
      $req->execute(array('id' => $id ));
      // Get result
      $res = $req->fetch();

      $user = new UserModel($res['idUser'], $res['username'], $res['firstName'], $res['lastName'], $res['mail'], $res['security_level']);

      return $user;
    }

	public static function insertUser($username ,$password, $firstName, $lastName, $mail, $security_level){
      $db = Db::getInstance();

      // Replace special char with html equivalent
      $username = htmlentities($username, ENT_QUOTES);
      $password = htmlentities($password, ENT_QUOTES);
      $firstName = htmlentities($firstName, ENT_QUOTES);
      $lastName = htmlentities($lastName, ENT_QUOTES);
      $mail = htmlentities($mail, ENT_QUOTES);
      $security_level = intval($security_level);

      //hash
      $password = password_hash($password, PASSWORD_BCRYPT);

      // Prepared request
      $req = $db->prepare(' CALL `AddUser`(:username, :password, :firstName, :lastName, :mail, :security_level, @res);');   

      $req->bindParam(':username', $username, \PDO::PARAM_STR);
      $req->bindParam(':password', $password, \PDO::PARAM_STR); 
      $req->bindParam(':firstName', $firstName, \PDO::PARAM_STR); 
      $req->bindParam(':lastName', $lastName, \PDO::PARAM_STR); 
      $req->bindParam(':mail', $mail, \PDO::PARAM_STR);
      $req->bindParam(':security_level', $security_level, \PDO::PARAM_INT); 
      //$req->bindParam(':res', $res, \PDO::PARAM_INT|\PDO::PARAM_INPUT_OUTPUT,11);

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

    public static function deleteUser($id){

    	$db = Db::getInstance();

    	$id = intval($id);

    	$req = $db->prepare(' CALL `DeleteUser`(:id, @res);');   

      	$req->bindParam(':id', $id, \PDO::PARAM_INT);
        //$req->bindParam(':res', $res, \PDO::PARAM_INT,11);

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

    public static function editUser($username ,$password, $firstName, $lastName, $mail, $security_level){
      $db = Db::getInstance();

      // Replace special char with html equivalent
      $username = htmlentities($username, ENT_QUOTES);
      $firstName = htmlentities($firstName, ENT_QUOTES);
      $lastName = htmlentities($lastName, ENT_QUOTES);
      $mail = htmlentities($mail, ENT_QUOTES);
      $security_level = intval($security_level);

      //hash
      if ($password != '') {
        $password = htmlentities($password, ENT_QUOTES);
      	$password = password_hash($password, PASSWORD_BCRYPT);
      }

      // Prepared request
      $req = $db->prepare(' CALL `EditUser`(:username, :password, :firstName, :lastName, :mail, :security_level, @res);');   

      $req->bindParam(':username', $username, \PDO::PARAM_STR);
      $req->bindParam(':password', $password, \PDO::PARAM_STR); 
      $req->bindParam(':firstName', $firstName, \PDO::PARAM_STR); 
      $req->bindParam(':lastName', $lastName, \PDO::PARAM_STR); 
      $req->bindParam(':mail', $mail, \PDO::PARAM_STR);
      $req->bindParam(':security_level', $security_level, \PDO::PARAM_INT); 
      //$req->bindParam(':res', $res, \PDO::PARAM_INT,11);

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
    }
}
