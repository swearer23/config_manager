<?php
	$projects = $__['projectNames'];
?>
<h1 style="color:red;">
<?php
	if($this -> flash){
		echo $this -> flash["tip"];
	}
?>
</h1>
<table>
	<tbody>
		<tr>
			<th>project name</th>
		</tr>
		<?php
			foreach($projects as $p)
			{
		?>
		<tr>
			<td><a href="/index.php?controller=config&action=get_configs&project_name=<?php echo $p;?>"><?php echo $p;?></a></td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>

<h2>new project</h2>
<form action="/index.php?controller=config&action=create_project" method="POST">
	<label>project name</label><br/>
	<input type="text" name="project_name" />
	<input type="submit" value="create" />
</form>

