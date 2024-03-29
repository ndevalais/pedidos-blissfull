<?php
require_once("Rest.inc.php");

class API extends REST {
	public $data = "";
	const DB_SERVER = "localhost";
	const DB_USER = "Database_Username";
	const DB_PASSWORD = "Database_Password";
	const DB = "Database_Name";

	private $db = NULL;
	
	public function __construct() {
		parent::__construct();	// Init parent contructor
		$this->dbConnect();		// Initiate Database connection
	}
	
	//Database connection
	private function dbConnect(){
		/*$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
		if($this->db)
			mysql_select_db(self::DB,$this->db);*/
	}
	
	//Public method for access api.
	//This method dynmically call the method based on the query string
	public function processApi(){
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0)
			$this->$func();
		else
			$this->response('',406); 
			// If the method not exist with in this class, response would be "Page not found".
	}
	
	private function login(){
		if($this->get_request_method() != "POST") {
			$this->response('',406);
		}
		
		$email = $this->_request['email'];  
		$password = $this->_request['pwd'];
		
		// Input validations
		if(!empty($email) and !empty($password)) {
			//if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
				//$sql = mysql_query("SELECT user_id, user_fullname, user_email FROM users WHERE user_email = '$email' AND user_password = '".md5($password)."' LIMIT 1", $this->db);
				//if(mysql_num_rows($sql) > 0) {
				//	$result = mysql_fetch_array($sql,MYSQL_ASSOC);
			
					// If success everythig is good send header as "OK" and user details
					 $this->response($this->json($result), 200);
				//}
				//$this->response('', 204); // If no records "No Content" status
			//} else {
			//	$error = array('status' => "Failed", "msg" => "Invalid Email address");
			//	$this->response($this->json($error), 400);
			//}
		} else { 
			
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 400);		
		}
	}
	
	private function users(){
		if($this->get_request_method() != "GET") {
			$this->response('',406);
		}		
			$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
			$this->response($this->json($error), 200);		
	}
	
	/*
	private function deleteUser(){
	}
	*/
	//Encode array into JSON
	private function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}
}

// Initiiate Library
$api = new API;
$api->processApi();
?>