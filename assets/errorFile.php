<?php
	$docs = $_GET['document'];
	foreach ($docs as $key => $value) {
		?>
		<div><a href="?doc=<?= $value ?>&action=check">Проблема в файле <?= $value ?></a></div> <?php
	}
?>