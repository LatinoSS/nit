<?php
	$pathAsset = "C:/OpenServer/domains/russianitpractice/assets/";
	$pathDocs = "C:/OpenServer/domains/russianitpractice/";
	if (isset($_GET['rootDocFolder'])) {
		$pathDocs.= $_GET['rootDocFolder'];
	} else {
		$pathDocs.= "docs/";
	}
	$rootFolder = "web";
	$base_url= "http://russianitpractice/web/";
?>