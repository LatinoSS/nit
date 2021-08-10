<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Документы</title>
</head>
<body>
	<header>
		<div id="headerInside">
			<span id="logo"></span>
		</div>
	</header>
	<h1 class="title">Документы</h1>
	<div class="container">
	    <div class="col-sm">
	    	<div class="underTitle">
	    		<h4>Договоры</h4>
	    	</div>
			<div class="content">
				<table class="documents">
					<?php
						global $pathDocs;
						$jsonAllFiles = file_get_contents("{$pathDocs}index.json");
						$dataAllFiles = json_decode($jsonAllFiles, true);
						foreach ($dataAllFiles as $dataFile) {
							$jsonNameDoc = file_get_contents("{$pathDocs}{$dataFile}/index.json");
							$dataNameDoc = json_decode($jsonNameDoc, true);
							$name = $dataNameDoc['Title'];
							?>
							<tr class="row">
								<td><a href="?doc=<?= $dataFile ?>&action=main" class="docLink columnDoc"><?= $name ?></a></td>
								<td><a href="?doc=<?= $dataFile ?>&action=detail" class="docLink docFontSize columnDetail" target="_blank">(детали..)</a></td>
							</tr>
							<?php
						}
					?>
				</table>
			</div>
	    </div>
	</div>
</body>
</html>