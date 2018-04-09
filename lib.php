<?php

function out_dir_name() {
	$here = getcwd();
	$base = "newmem";

	$dn = $here . "/" . $base;

	return $dn;
}

function has_enough_disk_space() {
	$dn = out_dir_name();
	$mb = 1024.0*1024.0;
	$fsz_mb = disk_free_space($dn) / $mb;
	return ($fsz_mb > 1.0);
}

function pre_check_all() {
	$dn = out_dir_name();
	if (!file_exists($dn) || !is_dir($dn)) {
		return[-__LINE__, "The server isn't properly setup to accept your application"];
	}
	if (stat($dn)['mode'] == 40777) {
		return[-__LINE__, "The server's directory isn't properly configured"];
	}
	if (!has_enough_disk_space()) {
		return [-__LINE__, "No space on disk to save your submission"];
	}
	return [0, "ok"];
}

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

function state_init() {
	session_start();
}

function state_trans_from_to($from, $to) {
	if ($from != null) {
		if ($_SESSION['state'] != $from) {
			header("Location: /error.html");
		}
	}
	$_SESSION['state'] = $to;
}

?>
