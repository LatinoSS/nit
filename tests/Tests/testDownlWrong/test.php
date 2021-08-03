<?php
	global $_GET, $argv;	
	$_GET['action'] = $argv[1];
	$_GET['doc'] = $argv[2];
	$_GET['rootDocFolder'] = $argv[3];
	$_GET['doc'] = substr($_GET['doc'], 5);
	$_GET['action'] = substr($_GET['action'], 8);
	$_GET['rootDocFolder'] = substr($_GET['rootDocFolder'], 15);
	$connect = realpath(__DIR__ . '/..' . "/globalConfig.php"); 
	require ($connect);
	require ($GLOBALS['generalPathIndexPHP']);
?>