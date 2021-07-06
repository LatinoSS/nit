<?php
	class ConfigFileNotFoundException extends Exception {}
	/*Try Catch на предмет отсутствия файла с конфигурациями или другого исключения*/
	try {
	    $config_file_path = "../assets/config.php";
	    if (!file_exists($config_file_path))
	    {
	    	throw new ConfigFileNotFoundException("Отсутствует файл с конфигурациями.");
	    } else {
	    	require("{$config_file_path}");
	    }
		require("{$pathAsset}function.php");
		/*Условия выполнения скачивания документа на главной странице*/
		if(!isset($_GET['action']) && isset($_GET['doc']) && !isset($_GET['filename']) && !isset($_GET['check'])) {
			downloadMainDoc();
		}
		/*Условия выполнения просмотра документов с расхожими данными*/
		if (isset($_GET['action'])) {
			if(($_GET['action'] == 'check') && !isset($_GET['doc'])) {
				checkWrongDoc();
			}
		}
		/*Условие выполнения скачивания документа конкретной версии*/
		if(isset($_GET['filename'])) {
			downloadDocVersion();
		}
		/*Условия выполнения скачивания index.json документа с корректными данными документов*/
		if (isset($_GET['action'])) {
			if (isset($_GET['doc']) && ($_GET['action'] == 'check')) {
				downloadWrongJson();
			}
		}
		/*Условия изменения отображаемого контента на странице*/
	 	if($_SERVER['REQUEST_URI'] == "/{$rootFolder}/index.php" || $_SERVER['REQUEST_URI'] == "/{$rootFolder}/index.php?action=check") {
	 		require("{$pathAsset}listDocs.php");
	 	} elseif (isset($_GET['document'])) {
			require("{$pathAsset}errorFile.php");
		} else {
			require("{$pathAsset}document.php");
		}
	} catch (ConfigFileNotFoundException $e) {
	    echo "ConfigFileNotFoundException: ".$e->getMessage();
	    die();
	} catch (Exception $e) {
	    echo $e->getMessage();
	    die();
	}
?>