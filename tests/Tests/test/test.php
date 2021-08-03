<?php
	global $_GET, $argv;
	$_GET['doc'] = $argv[1];
	$_GET['action'] = $argv[2];
	$_GET['doc'] = substr($_GET['doc'], 5);
	$_GET['action'] = substr($_GET['action'], 8);
	$connect = realpath(__DIR__ . '/..' . "/globalConfig.php"); 
	require ($connect);
	require ($GLOBALS['generalPathIndexPHP']);
?>