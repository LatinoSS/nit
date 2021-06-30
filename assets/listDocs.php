<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Документы</title>
</head>
<body>
	<h1 class="title">Документы</h1>
	<div class="container">
	    <div class="col-sm">
	    	<div class="underTitle">
	    		<h2>Оферты</h2>
	    	</div>
			<div class="content">
				<table class="documents">
					<?php
						$jsonF = file_get_contents("../docs/index.json");
						$dataF = json_decode($jsonF, true);
						foreach ($dataF as $value) {
							$jsonN = file_get_contents("../docs/{$value}/index.json");
							$dataN = json_decode($jsonN, true);
							$name = $dataN['Title'];
							?>
							<tr class="row">
								<td><a href="?doc=<?= $value ?>" class="docLink columnDoc"><?= $name ?></a></td>
								<td><a href="?doc=<?= $value ?>&action=detail" class="docLink docFontSize columnDetail" target="_blank">(детали..)</a></td>
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