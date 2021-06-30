<?php
	/*Функция скачивания документа конкретной версии*/
	function downloadDocVersion(){
		$doc = $_GET['doc'];
		$json = file_get_contents("../docs/{$doc}/index.json");
		$data = json_decode($json, true);
		$version = $_GET['version'];
		$version = $version - 1;
		$request = "{$data['versions'][$version]['FileName']}";
		$file = "../docs/{$doc}/{$data['versions'][$version]['FileName']}";
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename={$request}");
		readfile($file);
	}
	/*Функция скачивания index.json документа с корректными данными документов*/
	function downloadWrongJson(){
		$doc = $_GET['doc'];
		$json = file_get_contents("../docs/{$doc}/index.json");
		$data = json_decode($json, true);
		$count = count($data['versions']);
		$fname = "index.json";
		$text = "{
	\"Title\": \"{$data[Title]}\",
  	\"BigTitle\": \"{$data[BigTitle]}\",
  	\"versions\": [ \n";
  		$fileCreate = tmpfile();
		$fileCreate_path = stream_get_meta_data($fileCreate)['uri'];
		for ($i=0; $i < $count; $i++) {
			$filename = "../docs/{$doc}/{$data['versions'][$i]['FileName']}";
			$md5 = md5_file($filename);
			$size = filesize($filename);
			$text = $text . "		{
			\"Version\": {$data['versions'][$i]['Version']},
			\"FileName\": \"{$data['versions'][$i]['FileName']}\",
			\"Size\": {$size},
			\"Md5\": \"{$md5}\",
			\"Data\": \"{$data['versions'][$i]['Data']}\"
		},\n";
		}
		$text = substr($text,0,-2);
		$text = $text . "
	]
}";
		fwrite($fileCreate, $text);
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename={$fname}");
		ob_end_clean();
		readfile($fileCreate_path);
		fclose($fileCreate);
		exit();
	}
	/*Функция скачивания документов на главной странице*/
	function downloadMainDoc(){
		$doc = $_GET['doc'];
		$action = $_GET['action'];
		$json = file_get_contents("../docs/{$doc}/index.json");
		$data = json_decode($json, true);
		$count = count($data['versions']);
		for ($i=0; $i < $count; $i++) {
			$request = "{$data['versions'][$i]['FileName']}";
			$file = "../docs/{$doc}/{$data['versions'][$i]['FileName']}";
		}
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename={$request}");
		readfile($file);
	}
	/*Функция просмотра всех возможных документов и определение расхождений данных документа с данными из index.json*/
	/*Если расхождений нет, то откывается alert с соответствующим текстом*/
	/*Если расхождения есть, то откывается страница с проблемными документами, можно скачать коррекные данные*/
	function checkWrongDoc(){
		$flag = true;
		$json = file_get_contents("../docs/index.json");
		$data = json_decode($json, true);
		$countFolder = count($data);
		$array = array();
		for ($i=0; $i < $countFolder; $i++) {
			$jsonDoc = file_get_contents("../docs/{$data[$i]}/index.json");
			$dataDoc = json_decode($jsonDoc, true);
			$countDoc = count($dataDoc['versions']);
			for ($j=0; $j < $countDoc; $j++) {
				$filename = "../docs/{$data[$i]}/{$dataDoc['versions'][$j]['FileName']}";
				$md5 = md5_file($filename);
				$size =filesize($filename);
				if ($md5 != $dataDoc['versions'][$j]['Md5'] || $size != $dataDoc['versions'][$j]['Size']) {
					$flag = false;
					if (!in_array($data[$i], $array)) {
						$array[] = $data[$i];
					}
				}
			}
		}
		if ($flag == true) {
			$message = "Ошибок и несоответствий в файлах не найдено";
			echo "<script type='text/javascript'>alert('{$message}');</script>";
		} else {
			$lengthArray = count($array);
			for ($i=0; $i < $lengthArray; $i++) {
				$request = $request . "document[]={$array[$i]}&";
			}
			header ("Location: ../web/index.php?{$request}");
		}
	}
?>