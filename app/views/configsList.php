<?php
	$projectName = $__['projectName'];
	$configs = $__['configs'];
?>
<span style="color:red;"><?php
	if($this -> flash)
	{
		echo $this -> flash['tip'];
	}
?></span>
<h1>configs for <?php echo $projectName; ?></h1>
<table cellSpacing='20'><tbody>
	<tr>
		<th>config name</th>
		<th>published version</th>
		<th>latest version</th>
		<th>published filename</th>
		<th></th><th></th><th></th>
	</tr>
	<?php
		foreach($configs as $c){
	?>
		<tr>
			<td><?php echo $c -> configName; ?></td>
			<td><?php echo $c -> publishedVersion; ?></td>
			<td><?php echo $c -> latestVersion; ?></td>
			<td><?php echo $c -> publishedFilename; ?></td>
			<td><a href="/index.php?controller=config&action=edit_config&project_name=<?php echo $projectName?>&config_name=<?php echo $c -> configName?>">edit</a></td>
			<td><a href="/index.php?controller=config&action=publish_config&project_name=<?php echo $projectName; ?>&config_name=<?php echo $c -> configName?>">publish</a></td>
			<td><a href="/index.php?controller=config&action=roll_back&project_name=<?php echo $projectName; ?>&config_name=<?php echo $c -> configName?>">roll back</a></td>
		</tr>
	<?php
		}
	?>
</tbody></table>

<h3>create a new config for project <?php echo $projectName; ?></h3>
<form action="/index.php?controller=config&action=create_config" method="POST">
	<lable>config name:</label></br>
	<input type="hidden" name="project_name" value="<?php echo $projectName; ?>" />
	<input type="text" name="config_name" /></br>
	<textarea name="config_content" style="width:800px; height:500px;"></textarea><br/>
	<input type="submit" value="create" />
</form>

