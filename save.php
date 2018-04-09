<pre>

<?php

include_once('lib.php');

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
