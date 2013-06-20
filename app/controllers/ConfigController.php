<?php
class ConfigController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function listProjects()
	{
		$projectNames = ProjectModel::getProjectNames();
		$this -> __['projectNames'] = $projectNames;
		$this -> render("listProjects.php");
	}
	
	public function createProject()
	{
		$projectName = $_POST['project_name'];
		$project = new ProjectModel($projectName);
		if($project -> create())
		{
			$this -> redirect(array(
				'action' => 'listProjects'
			));
		}else{
			$this -> setFlash(array('tip' => 'project already exists'));
			$this -> redirect(array(
				'action' => 'listProjects'
			));
		}
	}
	
	public function createConfig()
	{
		$projectName = $_POST['project_name'];
		$configName = $_POST['config_name'];
        $configContent = isset($_POST['config_content']) ? $_POST['config_content'] : ' ';
		if(ProjectModel::getProject($projectName))
		{
			$config = new ConfigModel($projectName , $configName);
			if($result = $config -> create($configContent))
			{
				$this -> redirect(array(
					'action' => 'getConfigs',
					'params' => array(
						'project_name' => $projectName
					)
				));
			}else{
				$this -> setFlash(array('tip' => 'config already exists'));
				$this -> redirect(array(
					'action' => 'getConfigs',
					'params' => array(
						'project_name' => $projectName
					)
				));
			}
		}else{
			$this -> setFlash(array('tip' => 'project not exists'));
			$this -> redirect(array(
				'action' => 'getConfigs',
				'params' => array(
					'project_name' => $projectName
				)
			));
		}
	}
	
	public function modifyConfigFile()
	{
		$projectName = $_POST['project_name'];
		$configName = $_POST['config_name'];
        $configContent = $_POST['config_content'];
		if(ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if($configFile = $config -> createConfigFile($configContent))
				{
					
				}else{
					$this -> setFlash(array(
						'tip' => 'unknown error'
					));
				}
			}else{
				$this -> setFlash(array(
					'tip' => 'config not exists'
				));
			}
		}else{
			$this -> setFlash(array(
				'tip' => 'project not exists'
			));
		}
		$this -> redirect(array(
			'action' => 'getConfigs',
			'params' => array(
				'project_name' => $projectName
			)
		));
	}
	
	public function getConfigs()
	{
		$projectName = $_GET['project_name'];
		$project = ProjectModel::getProject($projectName);
		$configs = $project -> getConfigs();
		$this -> __['configs'] = $configs;
		$this -> __['projectName'] = $projectName;
		$this -> render('configsList.php');
	}
	
	public function publishConfig()
	{
		$projectName = $_GET['project_name'];
		$configName = $_GET['config_name'];
		$version = isset($_GET['version']) ? $_GET['version'] : null;
		if($project = ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if($result = $config -> publishConfig($version))
				{
					/*
						after publish post this config file to remote nodes
					*/
					SyncModel::post($project, $config);
				}else{
					$this -> setFlash(array(
						'tip' => 'unknown error'
					));
				}
			}else{
				$this -> setFlash(array(
					'tip' => 'config not exists'
				));
			}
		}else{
			$this -> setFlash(array(
				'tip' => 'project not exists'
			));
		}
		$this -> redirect(array(
			'action' => 'getConfigs',
			'params' => array(
				'project_name' => $projectName
			)
		));
	}
	
	public function rollback()
	{
		$projectName = $_GET['project_name'];
		$configName = $_GET['config_name'];
		$version = isset($_GET['version']) ? $_GET['version'] : null;
		if($project = ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if($result = $config -> publishConfig($version , true))
				{
					/*
						after publish post this config file to remote nodes
					*/
					SyncModel::post($project, $config);
				}else{
					$this -> setFlash(array(
						'tip' => 'unknown error'
					));
				}
			}else{
				$this -> setFlash(array(
					'tip' => 'config not exists'
				));
			}
		}else{
			$this -> setFlash(array(
				'tip' => 'project not exists'
			));
		}
		$this -> redirect(array(
			'action' => 'getConfigs',
			'params' => array(
				'project_name' => $projectName
			)
		));
	}
	
	public function editConfig()
	{
		$projectName = $_GET['project_name'];
		$configName = $_GET['config_name'];
		$version = isset($_GET['version']) ? $_GET['version'] : 0;
		if($project = ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if(empty($version)) {
					$version = $config->publishedVersion;
				}
				if($configFile = $config -> getConfigFile($version))
				{
					$this -> __['projectName'] = $projectName;
					$this -> __['configName'] = $configName;
					$this -> __['configFile'] = $configFile['fileContent'];
					$this -> render('editConfig.php');
					return;
				}else{
					$this -> setFlash(array(
						'tip' => 'version not exists'
					));
				}
			}else{
				$this -> setFlash(array(
					'tip' => 'config not exists'
				));
			}
		}else{
			$this -> setFlash(array(
				'tip' => 'project not exists'
			));
		}
		$this -> redirect(array(
			'action' => 'getConfigs',
			'params' => array(
				'project_name' => $projectName
			)
		));
	}
	
	public function getConfigFile()
	{
		$projectName = $_GET['project_name'];
		$configName = $_GET['config_name'];
		$version = isset($_GET['version']) ? $_GET['version'] : 0;
		if($project = ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if(empty($version)) {
					$version = $config->publishedVersion;
				}
				if($configFile = $config -> getConfigFile($version))
				{
					$this -> echoJson(array(
						'ret' => 1,
						'dat' => $configFile
					));
				}else{
					$this -> echoJson(array(
					'ret' => 0,
					'msg' => 'version_not_exists'
				));
				}
			}else{
				$this -> echoJson(array(
					'ret' => 0,
					'msg' => 'config_not_exists'
				));
			}
		}else{
			$this -> echoJson(array(
				'ret' => 0,
				'msg' => 'project_not_exists'
			));
		}
	}
}
?>
