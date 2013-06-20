<?php
class ApisController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function listProjects()
	{
		$projectNames = ProjectModel::getProjectNames();
		$results = array(
			'ret' 	=> 1,
			'dat' 	=> array(
				'projects' => $projectNames
			)
		);
		$this -> echoJson($results);
	}
	
	public function createProject()
	{
		$projectName = $_POST['project_name'];
		$project = new ProjectModel($projectName);
		if($project -> create())
		{
			$this -> echoJson(array(
				'ret' => 1
			));
		}else{
			$this -> echoJson(array(
				'ret' => 0,
				'msg' => 'project_exists'
			));
		}
	}
	
	public function createConfig()
	{
		$projectName = $_POST['project_name'];
		$configName = $_POST['config_name'];
        $configContent = isset($_POST['config_content']) ? $_POST['config_content'] : ' ';
        if(is_array($configContent)){
            $this -> echoJson(array(
                'ret' => 0,
                'msg' => 'format_of_config_content_expected_to_be_string(received_array)'
            ));
            return;
        };
		if(ProjectModel::getProject($projectName))
		{
			$config = new ConfigModel($projectName , $configName);
			if($result = $config -> create($configContent))
			{
				$this -> echoJson(array(
					'ret' => 1,
					'dat' => $result
				));
			}else{
				$this -> echoJson(array(
					'ret' => 0,
					'msg' => 'config_exists'
				));
			}
		}else{
			$this -> echoJson(array(
				'ret' => 0,
				'msg' => 'project_not_exists'
			));
		}
	}
	
	public function modifyConfigFile()
	{
		$projectName = $_POST['project_name'];
		$configName = $_POST['config_name'];
        $configContent = isset($_POST['config_content']) ? $_POST['config_content'] : '';
        if(is_array($configContent)){
            $this -> echoJson(array(
                'ret' => 0,
                'msg' => 'format_of_config_content_expected_to_be_string(received_array)'
            ));
            return;
        };
		if(ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if($configFile = $config -> createConfigFile($configContent))
				{
					$this -> echoJson(array(
						'ret' => 1,
						'dat' => $configFile
					));
				}else{
					$this -> echoJson(array(
						'ret' => 0,
						'msg' => 'unknown_error'
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
	
	public function getConfigs()
	{
		$projectName = $_GET['project_name'];
		if($project = ProjectModel::getProject($projectName))
		{
			$this -> echoJson(array(
				'ret' => 1,
				'dat' => array(
					'configs' => $project -> getConfigs()
				)
			));
		}else{
			$this -> echoJson(array(
				'ret' => 0,
				'msg' => 'project_not_exists'
			));
		}
	}
	
	public function publishConfig()
	{
		$projectName = $_POST['project_name'];
		$configName = $_POST['config_name'];
		$version = isset($_POST['version']) ? $_POST['version'] : null;
		if($project = ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if($result = $config -> publishConfig($version))
				{
					$this -> echoJson(array(
						'ret' => 1,
						'dat' => $result
					));
					/*
						after publish post this config file to remote nodes
					*/
					SyncModel::post($project, $config);
				}else{
					$this -> echoJson(array(
						'ret' => 0,
						'msg' => 'unknown_error'
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
	
	public function rollback()
	{
		$projectName = $_POST['project_name'];
		$configName = $_POST['config_name'];
		$version = isset($_POST['version']) ? $_POST['version'] : null;
		if($project = ProjectModel::getProject($projectName))
		{
			if($config = ConfigModel::getConfig($projectName , $configName))
			{
				if($result = $config -> publishConfig($version , true))
				{
					$this -> echoJson(array(
						'ret' => 1,
						'dat' => $result
					));
					/*
						after publish post this config file to remote nodes
					*/
					SyncModel::post($project, $config);
				}else{
					$this -> echoJson(array(
						'ret' => 0,
						'msg' => 'unknown_error'
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
