<style style="text/css">
* { font-family: arial; }
</style> 
<h4>Menlo Park Toastmasters</h4>

<a href="roaster_update.php">Update roaster</a>
<h5>Line-up for the TM meeting</h5>

<div>
	<table border=1>
		<tbody>
			<tr>
				<td>
					<label for="prefix">Prefix</label>
				</td>
				<td colspan=100>
					<textarea name"prefix" rows=10 cols=100>
					</textarea>
				</td>
			</tr>
			<tr>
				<td>Toastmaster:</td>
				<td>
					<input list="people">
				</td>
				<td>
						<a href="">Resign</a>
				</td>
				<td>
						<a href="">Remind</a>
				</td>
			</tr>
			<tr><td>General evaluator:</td> <td><input list="people"> </td> </tr>
			<tr><td>Speaker 1:</td> <td><input list="people"> </td> </tr>
			<tr><td>Speaker 2:</td> <td><input list="people"> </td> </tr>
			<tr><td>Speaker 3:</td> <td><input list="people"> </td> </tr>
			<tr><td>Evaluator 1:</td> <td><input list="people"> </td> </tr>
			<tr><td>Evaluator 2:</td> <td><input list="people"> </td> </tr>
			<tr><td>Evaluator 3:</td> <td><input list="people"> </td> </tr>
			<tr><td>Table topics:</td> <td><input list="people"> </td> </tr>
			<tr><td>Grammarian:</td> <td><input list="people"> </td> </tr>
			<tr><td>Ah Counter:</td> <td><input list="people"> </td> </tr>
			<tr><td>Timer:</td> <td><input list="people"> </td> </tr>
			<tr><td>Jokemaster:</td> <td><input list="people"> </td> </tr>
			<tr><td>Tip of the day:</td> <td><input list="people"> </td> </tr>
			<tr>
				<td>
					<label for="postfix">Postfix</label>
				</td>
				<td colspan=100>
					<textarea name"prefix" rows=10 cols=100>
					</textarea>
				</td>
			</tr>
		</tbody>
	</table>
	<datalist id="people">
	<?php
		$csv_paths = array_filter(scandir(realpath(dirname(__FILE__))), function($v, $k) {
		  return preg_match('/.*csv$/', $v) == 1;
		}, ARRAY_FILTER_USE_BOTH);
		$csv_path = $csv_paths[8];

		print_r($csv_paths);
		print "trying to open $csv_path";
		$fp = fopen($csv_path, "r");
		if (!$fp) {
			print_r("error");
			exit();
		}
		while (($data = fgetcsv($fp)) !== FALSE) {
			//print_r($data);
			$person_name = $data[1];
			print "<option value=\"$person_name\">";
		}
	?>
	</datalist>
</div>
