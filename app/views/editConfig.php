<?php
	$projectName = $__['projectName'];
	$configName = $__['configName'];
	$configFile = $__['configFile'];
?>
<h1>
	config file <?php echo $configName; ?> for <?php echo $projectName; ?>
</h1>
<form action="/index.php?controller=config&action=modify_config_file" method="POST">
	<input type="hidden" name="project_name" value="<?php echo $projectName; ?>" />
	<input type="hidden" name="config_name" value="<?php echo $configName; ?>" />
	<textarea name="config_content" style="width:800px; height:500px;">
	<?php echo $configFile; ?>
	</textarea><br/>
	<input type="submit" value="create" />
</form>
