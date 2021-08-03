<?php
require realpath(__DIR__ . '/..' . "/assets/config.php");
require realpath(__DIR__ . '/..' . "/assets/function.php");
try
{
	$action= (!isset($_GET['action'])) ? 'main' : $_GET['action'];
	switch ($action)
	{
		case 'main': MainAction(); break; // Условие выполнения скачивания документа на главной странице
		case 'check': CheckAction(); break; // Условие выполнения скачивания index.json документа с корректными данными документов
		case 'doc-version': downloadDocVersion(); break; // Условие выполнения скачивания документа конкретной версии
		case 'doc-list': getDoclist(); break; // Условие создания структуры данных для передачи по ajax
		case 'detail': DetailAction(); break; // Условие изменения отображаемого контента на странице
	}
	// Условия изменения отображаемого контента на странице
 	if ($_SERVER['REQUEST_URI'] == "/{$rootFolder}/index.php" || $_SERVER['REQUEST_URI'] == "/{$rootFolder}/index.php?action=check") {
 		require("{$pathAsset}listDocs.php");
 	} elseif (isset($_GET['document'])) {
		require("{$pathAsset}errorFile.php");
	}
}
catch (Exception $e)
{
    echo $e->getMessage();
    die();
}
