<?php
class FileSystemDao
{
	public function __construct()
	{
		
	}
	
	public static function chProjectDir($project = null)
	{
		if($project)
		{
			chdir(Environment::$conf['configDir'] . $project -> projectName);
		}else{
			chdir(Environment::$conf['configDir']);
		}
	}
	
	public static function chConfigDir($config)
	{
		self::chProjectDir($config -> project);
		chdir($config -> configName);
	}
	
	public static function chPublishedDir($config)
	{
		self::chConfigDir($config);
		chdir('published');
	}
	
	public static function chVersionsDir($config)
	{
		self::chConfigDir($config);
		chdir('versions');
	}
		
	public static function mkProjectDir($dirName)
	{
		if(mkdir($dirName))
		{
			return true;
		}else{
			return false;
		}
	}
	
	public static function mkConfigDir($dirName)
	{
		if(mkdir($dirName))
		{
			chdir($dirName);
			mkdir('published');
			mkdir('versions');
			chdir('..');
			return true;
		}else{
			return false;
		}
	}
	
	public static function dirExists($dirName)
	{
		if(file_exists($dirName) && is_dir($dirName))
		{
			return true;
		}else{
			return false;
		}
	}
	
	public static function fileExists($fileName)
	{
		if(file_exists($fileName) && is_file($fileName))
		{
			return file_get_contents($fileName);
		}else{
			return false;
		}
	}
	
	public static function listDirs()
	{
		$dirNames = array();
		foreach(scandir('.') as $dir)
		{
			if(is_dir($dir) && $dir != '.' && $dir != '..'  && $dir != '.svn')
			{
				array_push($dirNames , $dir);
			}
		}
		return $dirNames;
	}
	
	public static function listFiles()
	{
		$files = array();
		foreach(scandir('.') as $f)
		{
			if(is_file($f))
			{
				array_push($files , $f);
			}
		}
		return $files;
	}
	
	public static function listFileVersions()
	{
		$files = self::listFiles();
		$versions = array();
		foreach($files as $f)
		{
			$v = explode('.' , $f);
			$v = $v[1];
			array_push($versions , $v);
		}
		return $versions;
	}
	
	public static function writeFile($fileName , $content)
	{
		if(file_exists($fileName))
		{
			return false;
		}else{
            return file_put_contents($fileName , $content);
		}
	}
	
	public static function doPublishProcess($fileName)
	{
		foreach(scandir('.') as $file)
		{
			if(is_file($file))
			{
				unlink($file);
			}
		}
		chdir('../versions');
		copy($fileName , '../published/' . $fileName);
	}
	
}
?>
