<?php
class ProjectModel extends BaseModel
{
	public $projectName;

	public function __construct($projectName)
	{
		parent::__construct();
		$this -> projectName = $projectName;
	}
	
	public function create()
	{
		FileSystemDao::chProjectDir();
		if(!self::projectExists($this -> projectName))
		{
			return FileSystemDao::mkProjectDir($this -> projectName);
		}else{
			return false;
		}
	}
	
	public function getConfigs()
	{
		$configs = array();
		FileSystemDao::chProjectDir($this);
		foreach(FileSystemDao::listDirs() as $config)
		{
			array_push($configs , new ConfigModel($this -> projectName , $config));
		}
		return $configs;
	}
	
	public static function getProject($projectName)
	{
		FileSystemDao::chProjectDir();
		if(self::projectExists($projectName))
		{
			return new ProjectModel($projectName);
		}else{
			return false;
		}
	}
	
	public static function getProjectNames()
	{
		FileSystemDao::chProjectDir();
		$projectNames = FileSystemDao::listDirs();
		return $projectNames;
	}
	
	public static function projectExists($projectName)
	{
		if(FileSystemDao::dirExists($projectName))
		{
			return true;
		}else{
			return false;
		}
	}
}
?>
