<?php
require_once("Rest.inc.php");

class API extends REST {
	public $data = "";
	public $debug = 0;
	public $oauth = "";
	public $api = false;
	
	const DB_SERVER = "localhost";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB = "lenceria";
/*
	const DB_SERVER = "localhost";
	const DB_USER = "dilovlas_casinos";
	const DB_PASSWORD = "AgQ6OpCWwKFW";
	const DB = "dilovlas_casinos";	
*/
	private $db = NULL;
	

	public function __construct() {
		parent::__construct();			// Inicio constructor
		$this->dbConnect();				// Inicializo Base de datos
	}

	// Base de datos conexion
	private function dbConnect() {
		$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
		if($this->db)
			mysql_select_db(self::DB,$this->db);
	}

	// Metodo publico de acceso a la API 
	// Este método dynmically llamar al método basado en la cadena de consulta
	public function processApi() {
		
		if($this->debug==1) echo "INICIO API CON DEBUGGER  <br />";
		
		if (!empty($_REQUEST['rquest'])) {
			if($this->debug==1) echo "OBTENGO API  <br />";
			$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); 
		} else {
			if($this->debug==1) echo "NO EXISTE LA API <br />";
			$this->response('',404); 
		}
		// Si el metodo no existe en esta clase, response would be "Page not found".
	}

	//------------------------------------------------------------------------------------------------------
	// Funcion de authentication  - Verifica si el cliente tiene permiso para ingresar
	public function authentication() {
		$select = "SELECT Count(oauth.OAuth) as cant FROM oauth WHERE oauth.OAuth = '".$this->oauth."'";
		$sql = mysql_query($select, $this->db);
		if($this->debug==1) echo "funcion:Authentication - $sql <br />";
		if(mysql_num_rows($sql) > 0){
			$result = mysql_fetch_array($sql,MYSQL_ASSOC);
			if ($result["cant"]>0) $this->api = true;
		}
		return $this->api;
	}
	//Encode array into JSON
	public function json($data) {
		if(is_array($data)){
			return json_encode($data);
		}
	}
	//------------------------------------------------------------------------------------------------------
	// funcion Oauth - Obtengo ip y valido
	private function oauth_ip() {
		$ip=$_SERVER['REMOTE_ADDR'];
		$oauth_ip = md5( $ip  );
	
		$select = "SELECT oauth_ip FROM oauth_ip WHERE oauth_ip = '$oauth_ip' LIMIT 1";
		$sql = mysql_query($select, $this->db);
		if(mysql_num_rows($sql) > 0){
			$result = mysql_fetch_array($sql,MYSQL_ASSOC);
			$this->response($this->json($result), 200);
		} else { 
			$error = array('status' => "Failed", "msg" => "Invalid Oauth IP $ip");
			$this->response($this->json($error), 206); // If no records "No Content" status
		}
	}
	
	private function setCookie($campo, $valor, $tiempo = 0, $dominio = "/") {
		if ($tiempo==0)
			$time = time() + 365 * 24 * 60 * 60; // Caduca en un año 
		else 
			$time = $tiempo;
			
		if($this->debug==1) echo "Seteo cookie $campo - $valor <br />";
		setcookie($campo, $valor, $time, $dominio); 	
		/*if(isset($_COOKIE[$campo])) { 
			setcookie($campo, $valor, $time); 
		*/
	}

	private function validaOAuth() {
			
	}
	//------------------------------------------------------------------------------------------------------
	// Funcion de Login 
	private function login() { 
		
		// Validación cruzada, si el método de la petición es POST de lo contrario volverá estado "No aceptable"
		if($this->get_request_method() != "GET") {
			$this->response('',406);
		}

		// Obtengo parametros
		$usuario = "";
		$password = "";
		if (isset($_REQUEST['usuario'])) $usuario = $_REQUEST['usuario'];  
		if (isset($_REQUEST['password'])) $password = $_REQUEST['password'];  	

		if($usuario<>"" && $password<>"" ) {
			if(filter_var($usuario, FILTER_VALIDATE_EMAIL)){
				if($this->debug==1) echo "Valido por E-Mail <br />";
				$select = "SELECT * FROM usuarios WHERE email = '$usuario' AND clave = '".md5($password)."' LIMIT 1";
				$sql = mysql_query($select, $this->db);
				if(mysql_num_rows($sql) > 0){
					$result = mysql_fetch_array($sql,MYSQL_ASSOC);
					$this->setCookie('usuario',$result['token']);
					$this->response($this->json($result), 200);
				} else { 
					$error = array('status' => "Failed", "msg" => "Invalid usuario or Password ");
					$this->response($this->json($error), 206); // If no records "No Content" status
				}
			} else {
				if($this->debug==1) echo "Valido por usuario <br />";
				$sql = mysql_query("SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '".md5($password)."' LIMIT 1", $this->db);
				if(mysql_num_rows($sql) > 0){
					$result = mysql_fetch_array($sql,MYSQL_ASSOC);
					$this->setCookie('usuario',$result['token']);
					$this->response($this->json($result), 200);
				} else { 
					$error = array('status' => "Failed", "msg" => "Invalid usuario or Password ");
					$this->response($this->json($result), 206); // If no records "No Content" status
				}
			}
		} else {
			$error = array('status' => "Failed", "msg" => "Empty usuario or Password ");
			$this->response($this->json($error), 204);
		}
	}

	private function users() { }
	
	private function deleteUser() { }


}
//--------------------------------------------------------------------------------
// Initiiate Library
$api = new API;

// Obtengo debug
if (!empty($_REQUEST['debug']))
	$api->debug = 1;
	
// Obtengo autentificacion
//if($api->debug==1) echo "<HTML>";
if($api->debug==1) echo "OBTENGO AUTENTIFICADOR  <br />";
if(isset($_REQUEST["oauth"])) {
	$api->oauth = $_REQUEST['oauth'];  
	
	if ($api->authentication()) {
		
		// PROCESO API ----------------------------------------------------------------
		$api->processApi();
		
	} else {
		$error = array('status' => "Failed OAuth", "msg" => "Invalid authentication");
		$api->response($api->json($error), 400);
	}
} else {
	if($api->debug==1) echo "Empty authentication  <br />";
	$error = array('status' => "Empty OAuth", "msg" => "Empty authentication");
	$api->response($api->json($error), 400);
}
//if($api->debug==1) echo "</HTML>";
//http://www.restapitutorial.com/httpstatuscodes.html#
//http://www.emenia.es/como-crear-urls-amigables-con-htaccess/
?>