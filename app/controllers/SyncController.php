<?php
class SyncController extends BaseController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function receiveSync()
	{
		$results = SyncModel::writeTmpFile($_POST['projectName'], $_POST['configName'], $_POST['filename'], $_POST['content']);
		$tmpFilename = $results['tmpFilename'];
		$publishedFilename = $results['publishedFilename'];
		if(SyncModel::md5Verification($tmpFilename , $_POST['md5']))
		{
			SyncModel::moveUpdatedFile($_POST['filename'], $tmpFilename, $publishedFilename);
			$this -> echoJson(array(
				'ret' => 1
			));
		}else{
			$this -> echoJson(array(
				'ret' => 0,
				'msg' => 'file_md5_unmatched'
			));
		}
	}
}
?>
