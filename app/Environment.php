<?php
	require dirname(__FILE__) . '/../Phails/commons/Environment.php';
	require 'fsDaos/FileSystemDao.php';
	Environment::$root = getcwd();
	Environment::init();
	Environment::$conf['configDir'] = dirname(__FILE__) . '/projects/';
	Environment::$conf['nodesList'] = array(
		"parent" 	=> "config.topgame.com",
		"disNodes"	=> array(
			"us.config.topgame.com"
		)
	);
?>
