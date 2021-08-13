<?php
	global $pathDocs, $base_url;
	$doc = $_GET['doc'];
	$tag = $_GET['tag'];
	$jsonDoc = file_get_contents("{$pathDocs}{$doc}/index.json");
	$dataDoc = json_decode($jsonDoc, true);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title><?= $dataDoc['Title'] ?></title>
</head>
<body>
<header>
	<div class="headerInside">
		<span id="logo"></span>
		<span id="companyDoc">Официальные документы ООО РИТ</span>
		<span id="returnBack"><a href="<?= $base_url ?>" class="naw">Главная</a></span>
	</div>
</header>
<div class="container">
	<h1 class="title"><?= $dataDoc['tags'][$tag] ?></h1>
	<div class="row">
	    <div class="col-lg">
	    	<div class="tableUpd">
	    		<table>
					<tr class="colorTr">
				    	<th>Дата</th>
				    	<th>Версия</th>
				    	<th>Размер (байт)</th>
				    	<th>Контрольная сумма (хэш) md5</th>
				 	</tr>
				 	<?php
				 		foreach ($dataDoc['versions'] as $i => $value) { ?>
				 			<tr>
				 				<td><?= $dataDoc['versions'][$i]['Date'] ?></td>
						    	<td><a href="?doc=<?= $doc ?>&filename=<?= $dataDoc['versions'][$i]['FileName'] ?>&action=doc-version" class="dowloadLink"><?= $dataDoc['versions'][$i]['FileName'] ?></a></td>
						    	<td><?= $dataDoc['versions'][$i]['Size'] ?></td>
						    	<td><?= $dataDoc['versions'][$i]['Md5'] ?></td>
						 	</tr>
				 	<?php
				 		} ?>
				 	<tr class="colorTr sizeFont">
				 		<td colspan="5">Для проверки контрольной суммы (хэша) md5 скачанного файла можно использовать сторонние сервисы, например <a href="http://onlinemd5.com/" class="colorA"target="_blank">http://onlinemd5.com/</a>, <a href="https://md5file.com/calculator" class="colorA" target="_blank">https://md5file.com/calculator</a> или <a href="https://hash.online-convert.com/ru/md5-generator" class="colorA" target="_blank">https://hash.online-convert.com/ru/md5-generator</a>.</td>
				 	</tr>
				</table>
	    	</div>
	    </div>
	</div>
</div>
</body>
</html>