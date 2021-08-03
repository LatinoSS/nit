<?php
	$connect = realpath(__DIR__ . '/..' . "/globalConfig.php"); 
	require ($connect);
	$pathAsset = $GLOBALS['generalPathAsset'];
	$pathDocs = __DIR__ . "/docs/";
	$rootFolder = "web";
?>