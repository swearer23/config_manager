<?php
	require '../app/Environment.php';
	function __autoload($classname){
		Common::requireClass($classname);
	}
	
	$request = Common::getRequest();
	$controller = $request["controller"];
    $action = $request["action"];

	$class = new $controller;
	
	$class->$action();
?>
