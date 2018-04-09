<pre>

<?php

function make_random_fn() {
	$random_str = "";
	for ($i = 0; $i < 4; $i++) {
		$ri = random_int (0x0, 0xffffffff);
		$random_str .= "x" . $ri;
	}
	return sha1($random_str);
}

function make_out_fn() {
	return "newmem." . make_random_fn();
}

foreach ($_POST as $key => $value) {
	print $key . " = " . $value;	
}

$sub_data = array(
	"post" => $_POST
	,
	"get" => $_GET
	,
	"server" => $_SERVER
	,
	"cookie" => $_COOKIE
	,
	"request" => $_REQUEST
);

$j_str = json_encode($sub_data, JSON_PRETTY_PRINT);
$fp = fopen(make_out_fn(), 'w');
fwrite($fp, $j_str);
fclose($fp);

?>

</pre>
