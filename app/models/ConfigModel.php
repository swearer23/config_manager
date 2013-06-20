<?php
class ConfigModel extends BaseModel
{
	public $projectName;
	public $project;
	public $configName;
	public $publishedVersion;
	public $latestVersion;
	public $publishedFilename;

	public function __construct($projectName , $configName)
	{
		parent::__construct();
		$this -> projectName = $projectName;
		$this -> configName = $configName;
		$this -> project = new ProjectModel($projectName);
		FileSystemDao::chProjectDir($this -> project);
		if(FileSystemDao::dirExists($configName))
		{
			$this -> publishedVersion = $this -> getPublishedVersion();
			if($this -> publishedVersion){
				$this -> publishedFilename = $this -> versionToFileName($this->publishedVersion);
			}else{
				$this -> publishedFilename = '';
			}
			$this -> latestVersion = $this -> getLatestVersion();
		}
	}
	
	public function create($configContent = null)
	{
		if(self::configExists($this -> projectName , $this -> configName))
		{
			return false;
		}else{
			if(FileSystemDao::mkConfigDir($this -> configName))
			{
				if($configContent)
                {
                    return $this -> createConfigFile($configContent);
                }else{
                    return array(
					    'projectName' 	=> $this -> projectName,
					    'configName'	=> $this -> configName
				    );
                }
           	}
		}
	}
	
	public function createConfigFile($configContent)
	{
		$newVersion = $this -> getLatestVersion() + 1;
		$configFileName = 'config.' . $newVersion . '.xml';
		if(FileSystemDao::writeFile($configFileName , $configContent))
		{
			$configFile = array(
				'projectName'	=> $this -> projectName,
				'configName'	=> $this -> configName,
				'publishedVersion'	=> $this -> getPublishedVersion(),
				'version'		=> $newVersion,
				'content'		=> $configContent
			);
			return $configFile;
		}else{
			return false;
		}
	}
	
	public function getConfigFile($version)
	{
		$fileName = $this -> versionToFileName($version);
		if($version == $this->publishedVersion)
		{
			FileSystemDao::chPublishedDir($this);
		}else{
			FileSystemDao::chVersionsDir($this);
		}
		if($configFile = FileSystemDao::fileExists($fileName))
		{
			return array(
				'projectName' 		=> $this -> projectName,
				'configName'		=> $this -> configName,
				'thisVersion'		=> $version,
				'publishedVersion'	=> $this -> publishedVersion,
				'latestVersion'		=> $this -> latestVersion,
				'fileContent'		=> $configFile
			);
		}else{
			return false;
		}
	}
	
	public function publishConfig($version = null , $rollback = false)
	{
		FileSystemDao::chVersionsDir($this);
		if($rollback)
		{
			$version = $this -> getPublishedVersion();
			$version = $version == 1 ? $version : $version - 1;
		}else{
			$version = $version ? $version : $this -> getLatestVersion();
		}
		$fileName = $this -> versionToFileName($version);
		FileSystemDao::chVersionsDir($this);
		if(FileSystemDao::fileExists($fileName))
		{
			FileSystemDao::chPublishedDir($this);
			FileSystemDao::doPublishProcess($fileName);
			return array('publishedVersion' => $version);
		}else{
			return false;
		}
	}
	
	public static function getConfig($projectName , $configName)
	{
		if($config = self::configExists($projectName , $configName))
		{
			FileSystemDao::chConfigDir($config);
			return $config;
		}else{
			return false;
		}
	}
	
	private function getLatestVersion()
	{
		FileSystemDao::chVersionsDir($this);
		$versions = FileSystemDao::listFileVersions();
		$latestVersion = 0;
		foreach($versions as $v)
		{
			if($v > $latestVersion)
			{
				$latestVersion = $v;
			}
		}
		$latestVersion = $latestVersion > 0 ? $latestVersion : null;
		return $latestVersion;
	}
	
	private function getPublishedVersion()
	{
		FileSystemDao::chPublishedDir($this);
		$publishedVersion = 0;
		foreach(FileSystemDao::listFileVersions() as $v)
		{
			if($v > $publishedVersion)
			{
				$publishedVersion = $v;
			}
		}
		$publishedVersion = $publishedVersion > 0 ? $publishedVersion : null;
		return $publishedVersion;
	}
	
	private static function configExists($projectName , $configName)
	{
		$project = new ProjectModel($projectName);
		FileSystemDao::chProjectDir($project);
		if(FileSystemDao::dirExists($configName))
		{
			$config = new ConfigModel($projectName , $configName);
			return $config;
		}else{
			return false;
		}
	}
	
	private function versionToFileName($version)
	{
		return 'config.' . $version . '.xml';
	}
}
?>
