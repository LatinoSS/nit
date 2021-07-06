<?php
	$docs = $_GET['document'];
	foreach ($docs as $key => $doc) {
		?>
		<div><a href="?doc=<?= $doc ?>&action=check">Проблема в файле <?= $doc ?></a></div> <?php
	}
?>