<?php
	$docs = $_GET['document'];
	$count = count($docs);
	for ($i=0; $i < $count; $i++) {
		?>
		<div><a href="?doc=<?= $docs[$i] ?>&action=check">Проблема в файле <?= $docs[$i] ?></a></div> <?php
	}
?>


