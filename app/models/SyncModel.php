<?php
class SyncModel extends BaseModel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public static function post($project, $config)
	{
		$filename = self::getFileName($project, $config);
		$md5 = self::genMD5($filename);
		$ch = curl_init();
		$remoteNode = Environment::$conf['nodesList']['disNodes'];
		$urlSuffix  = '/?controller=Sync&action=receiveSync';
		$postData = array(
			"projectName" 	=> $config -> projectName,
			"configName"	=> $config -> configName,
			"versionName"	=> $config -> publishedVersion,
			"filename"		=> $filename,
			"content"		=> file_get_contents($filename),
			"md5"			=> $md5
		);
		function curlPost($ch, $url, $postData)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		}
		foreach($remoteNode as $node)
		{
			$url = $node . $urlSuffix;
			curlPost($ch , $url , $postData);
			$result = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($result , TRUE);
			if($result['ret'] != 1)
			{
				curlPost($ch, $url, $postData);
			}
		}
	}
	
	public static function writeTmpFile($projectName, $configName, $filename, $file)
	{
		FileSystemDao::chProjectDir();
		$project = new ProjectModel($projectName);
		if(!ProjectModel::projectExists($projectName))
		{
			$project -> create();
		}
		$config = new ConfigModel($projectName, $configName);
		$config->create();
		$tmpFilename = $filename . '.tmp';
		FileSystemDao::chPublishedDir($config);
		FileSystemDao::writeFile($tmpFilename , $file);
		return array(
			"tmpFilename"		=> $tmpFilename,
			"publishedFilename"	=> $config -> publishedFilename
		);
	}
	
	public static function md5Verification($filename, $remoteMD5)
	{
		$md5 = self::genMD5($filename);
		if($md5 == $remoteMD5)
		{
			return true;
		}else{
			return false;
		}
	}
	
	public static function moveUpdatedFile($filename, $tmpFilename, $publishedFilename)
	{
		unlink($publishedFilename);
		rename($tmpFilename , $filename);
	}
	
	private static function getFileName($project, $config)
	{
		FileSystemDao::chPublishedDir($config);
		$fileName = array_pop(FileSystemDao::listFiles());
		return $fileName;
	}
	
	private static function genMD5($filename)
	{
		$md5 = md5_file($filename);
		return $md5;
	}
	
}
?>
