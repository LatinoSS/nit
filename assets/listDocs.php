<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Документы</title>
</head>
<body>
	<header>
		<div class="headerInsideMain">
			<span id="logo"></span>
			<span id="companyDoc">Официальные документы ООО РИТ</span>
		</div>
	</header>
	<div class="containerMain" id="container-margin-top">
	    <div class="col-sm">
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
							$tag = $dataNameDoc['tags'];
							$tags = '';
							foreach ($tag as $key => $value) {
								$tags.= "&tag={$key}";
							}
							?>
							<tr class="row">
								<td><a href="?doc=<?= $dataFile ?>&action=main" class="docLink columnDoc"><?= $name ?></a></td>
								<td><a href="?doc=<?= $dataFile ?>&action=detail<?= $tags ?>" class="docLink docFontSize columnDetail" target="_blank">(детали..)</a></td>
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