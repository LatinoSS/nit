<?php
	$doc = $_GET['doc'];
	$json = file_get_contents("../docs/{$doc}/index.json");
	$data = json_decode($json, true);
	$count = count($data['versions']);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title><?= $data['Title'] ?></title>
</head>
<body>
<header>
	<div id="headerInside">
		<span id="logo"></span>
		<span id="companyName"><a href="/" id="name">Помощник арбитражного управляющего</a></span>
		<span id="returnBack"><a href="/" class="naw">Главная</a></span>
	</div>
</header>
<h1 class="title"><?= $data['Title'] ?></h1>
<div class="container">
	<div class="row">
		<div class="col-lg">
			<div class="underTitle">
				<h3>
					<?= $data['BigTitle'] ?>
				</h3>
			</div>
	    </div>
	    <div class="col-lg">
	    	<div class="tableUpd">
	    		<table>
					<tr class="colorTr">
				    	<th class="th">Версия</th>
				    	<th>Название файла</th>
				    	<th>Размер(байт)</th>
				    	<th>Контрольная сумма(хэш) md5</th>
				    	<th>Дата изменения</th>
				 	</tr>
				 	<?php
				 		for ($i=0; $i < $count; $i++) { ?>
				 			<tr>
						    	<td><?= $data['versions'][$i]['Version'] ?></td>
						    	<td><a href="?doc=<?= $doc ?>&version=<?= $data['versions'][$i]['Version'] ?>" class="dowloadLink"><?= $data['versions'][$i]['FileName'] ?></a></td>
						    	<td><?= $data['versions'][$i]['Size'] ?></td>
						    	<td><?= $data['versions'][$i]['Md5'] ?></td>
						    	<td><?= $data['versions'][$i]['Data'] ?></td>
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