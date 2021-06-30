<?php
	require('../assets/config.php');
	require("{$pathAsset}function.php");
	$doc = $_GET['doc'];
	$action = $_GET['action'];
	/*Условия выполнения скачивания документа на главной странице*/
	if(!isset($_GET['action']) && isset($_GET['doc']) && !isset($_GET['version']) && !isset($_GET['check'])) {
		downloadMainDoc();
	}
	/*Условия выполнения просмотра документов с расхожими данными*/
	if(($action == 'check') && !isset($_GET['doc'])) {
		checkWrongDoc();
	}
	/*Условие выполнения скачивания документа конкретной версии*/
	if(isset($_GET['version'])) {
		downloadDocVersion();
	}
	/*Условия выполнения скачивания index.json документа с корректными данными документов*/
	if (isset($_GET['doc']) && ($_GET['action'] == 'check')) {
		downloadWrongJson();
	}
	/*Условия изменения отображаемого контента на странице*/
 	if($_SERVER['REQUEST_URI'] == '/web/index.php' || $_SERVER['REQUEST_URI'] == '/web/index.php?action=check') {
 		require("{$pathAsset}listDocs.php");
 	} elseif (isset($_GET['document'])) {
		require("{$pathAsset}errorFile.php");
	} else {
		require("{$pathAsset}document.php");
	}
?>