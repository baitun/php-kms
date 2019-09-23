<?php 
	/**
	 * Код который должен вставляться в начале каждого .php файла
	 * (инклюдится в index.php и admin.php)
	 */

	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	if(isset($_GET['debug'])){
		set_error_handler('myErrorHandler');
		register_shutdown_function('fatalErrorShutdownHandler');
	}

	function myErrorHandler($code, $message, $file, $line) {
	  $result['result']=false;
	  $result['error_text']="Внутренняя ошибка 500. ".$message;
	  $result['error'] = array(
	  	'code' => $code, 
	  	'message' => $message, 
	  	'file' => $file, 
	  	'line' => $line
	  );
	  echo var_dump($result);
	  die();
	}
	function fatalErrorShutdownHandler(){
	  $last_error = error_get_last();
	  if ($last_error['type'] === E_ERROR) {
	    // fatal error
	    myErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
	  }
	}

	header('Content-Type: text/html; charset=utf-8');
	include_once(__DIR__."/include/SimpleDB.class.php");
	include_once(__DIR__."/include/km.class.php");
 ?>