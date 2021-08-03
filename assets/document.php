<?php
	$doc = $_GET['doc'];
	$jsonDoc = file_get_contents("{$GLOBALS['pathDocs']}{$doc}/index.json");
	$dataDoc = json_decode($jsonDoc, true);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="<?= $base_url ?>style.css">
	<title><?= $dataDoc['Title'] ?></title>
</head>
<body>
<header>
	<div id="headerInside">
		<span id="logo"></span>
		<span id="companyName"><a href="/" id="name">Помощник арбитражного управляющего</a></span>
		<span id="returnBack"><a href="/" class="naw">Главная</a></span>
	</div>
</header>
<h1 class="title"><?= $dataDoc['Title'] ?></h1>
<div class="container">
	<div class="row">
		<div class="col-lg">
			<div class="underTitle">
				<h3>
					<?= $dataDoc['BigTitle'] ?>
				</h3>
			</div>
	    </div>
	    <div class="col-lg">
	    	<div class="tableUpd">
	    		<table>
					<tr class="colorTr">
				    	<th>Название файла</th>
				    	<th>Размер(байт)</th>
				    	<th>Контрольная сумма(хэш) md5</th>
				    	<th>Дата изменения</th>
				 	</tr>
				 	<?php
				 		foreach ($dataDoc['versions'] as $i => $value) { ?>
				 			<tr>
						    	<td><a href="?doc=<?= $doc ?>&filename=<?= $dataDoc['versions'][$i]['FileName'] ?>&action=doc-version" class="dowloadLink"><?= $dataDoc['versions'][$i]['FileName'] ?></a></td>
						    	<td><?= $dataDoc['versions'][$i]['Size'] ?></td>
						    	<td><?= $dataDoc['versions'][$i]['Md5'] ?></td>
						    	<td><?= $dataDoc['versions'][$i]['Date'] ?></td>
						 	</tr>
				 	<?php
				 		} ?>
				 	<tr class="colorTr sizeFont">
				 		<td colspan="5">Для проверки контрольной суммы (хэша) md5 скачанного файла можно использовать сторонние сервисы, например <a href="http://onlinemd5.com/" class="colorA">http://onlinemd5.com/</a>, <a href="https://md5file.com/calculator" class="colorA">https://md5file.com/calculator</a> или <a href="https://hash.online-convert.com/ru/md5-generator" class="colorA">https://hash.online-convert.com/ru/md5-generator</a>.</td>
				 	</tr>
				</table>
	    	</div>
	    </div>
	</div>
</div>
</body>
</html>