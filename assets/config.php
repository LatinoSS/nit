<?php
	$pathDocs = "C:/OpenServer/domains/russianitpractice/";
	if (isset($_GET['rootDocFolder'])) {
		$pathDocs.= $_GET['rootDocFolder'];
	} else {
		$pathDocs.= "docs/";
	}
	$base_url= "http://russianitpractice/web/";
?>