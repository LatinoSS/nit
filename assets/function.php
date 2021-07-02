<?php
	/*Функция скачивания документа конкретной версии*/
	function downloadDocVersion(){
		require('../assets/config.php');
		$doc = $_GET['doc'];
		$json = file_get_contents("{$pathDocs}{$doc}/index.json");
		$data = json_decode($json, true);
		$filename = $_GET['filename'];
		$file = "{$pathDocs}{$doc}/{$filename}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$filename}");
		readfile($file);
	}
	/*Функция ищет все pdf файлы по заданной папке*/
	function checkExistDocs(string $value){
		require('../assets/config.php');
		$directory = "{$pathDocs}{$value}";
		$files = scandir($directory);
		$pdf = ".pdf";
		foreach ($files as $filename) {
			$pos = strpos($filename, $pdf);
			if ($pos !== false) {
				$arrayFileName[] = $filename;
			}
		}
		return $arrayFileName;
	}
	/*Функция ищет все pdf файлы по заданной папке и делает по ним index.json с их параметрами.*/
	function downloadWrongJson(){
		require('../assets/config.php');
		$doc = $_GET['doc'];
		$json = file_get_contents("{$pathDocs}{$doc}/index.json");
		$data = json_decode($json, true);
		$fname = "index.json";
		$text = "{
	\"Title\": \"{$data[Title]}\",
  	\"BigTitle\": \"{$data[BigTitle]}\",
  	\"versions\": [ \n";
  		$fileCreate = tmpfile();
		$fileCreate_path = stream_get_meta_data($fileCreate)['uri'];
  		$arrayName = checkExistDocs($doc); //Поиск всех pdf файлов в заданой папке
  		$count = count($arrayName);
		for ($i=0; $i < $count; $i++) {
			$filename = "{$pathDocs}{$doc}/{$arrayName[$i]}";
			$md5 = md5_file($filename);
			$size = filesize($filename);
			$filedata = date("d.m.y", filectime($filename));
			$text = $text . "		{
			\"FileName\": \"{$arrayName[$i]}\",
			\"Size\": {$size},
			\"Md5\": \"{$md5}\",
			\"Data\": \"{$filedata}\"
		},\n";
		}
		$text = substr($text,0,-2);
		$text = $text . "
	]
}";
		header('Content-Type: text/json');
		header("Content-Disposition: attachment; filename={$fname}");
		ob_end_clean();
		echo "$text";
		readfile($fileCreate_path);
		fclose($fileCreate);
		exit();
	}
	/*Функция скачивания документов на главной странице*/
	function downloadMainDoc(){
		require('../assets/config.php');
		$doc = $_GET['doc'];
		$action = $_GET['action'];
		$json = file_get_contents("{$pathDocs}{$doc}/index.json");
		$data = json_decode($json, true);
		$count = count($data['versions']);
		$request = "{$data['versions'][$count-1]['FileName']}";
		$file = "{$pathDocs}{$doc}/{$request}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$request}");
		readfile($file);
	}
	/*Функция просмотра всех возможных документов и определение расхождений данных документа с данными из index.json*/
	/*Если расхождений нет, то откывается alert с соответствующим текстом*/
	/*Если расхождения есть, то откывается страница с проблемными документами, можно скачать коррекные данные*/
	function checkWrongDoc(){
		require('../assets/config.php');
		$flag = true;
		$json = file_get_contents("{$pathDocs}index.json");
		$data = json_decode($json, true);
		$array = array();
		foreach ($data as $key => $value) {
			$jsonDoc = file_get_contents("{$pathDocs}{$value}/index.json");
			$dataDoc = json_decode($jsonDoc, true);
			$countDoc = count($dataDoc['versions']);
			for ($j=0; $j < $countDoc; $j++) {
				$filename = "{$pathDocs}{$value}/{$dataDoc['versions'][$j]['FileName']}";
				$md5 = md5_file($filename);
				$size =filesize($filename);
				if ($md5 != $dataDoc['versions'][$j]['Md5'] || $size != $dataDoc['versions'][$j]['Size']) {
					$flag = false;
					if (!in_array($value, $array)) {
						$array[] = $value;
					}
				}
				$filedata = date("d.m.y", filectime($filename));
				$textMain[$value] = $textMain[$value] . "		{
			\"FileName\": \"{$dataDoc['versions'][$j]['FileName']}\",
			\"Size\": {$size},
			\"Md5\": \"{$md5}\",
			\"Data\": \"{$filedata}\"
		},\n";
			}
			$textMain[$value] = substr($textMain[$value],0,-2);
			$textMain[$value] = $textMain[$value] . "
	]
}";
			$arrayName = checkExistDocs($value); //Поиск всех pdf файлов в заданой папке
  			$count = count($arrayName);
			for ($i=0; $i < $count; $i++) {
				$filename = "{$pathDocs}{$value}/{$arrayName[$i]}";
				$md5 = md5_file($filename);
				$size = filesize($filename);
				$filedata = date("d.m.y", filectime($filename));
				$text[$value] = $text[$value] . "		{
			\"FileName\": \"{$arrayName[$i]}\",
			\"Size\": {$size},
			\"Md5\": \"{$md5}\",
			\"Data\": \"{$filedata}\"
		},\n";
			}
			$text[$value] = substr($text[$value],0,-2);
			$text[$value] = $text[$value] . "
	]
}";
			if (strcasecmp($textMain[$value], $text[$value]) !== 0) {
				$flag = false;
				if (!in_array($value, $array)) {
					$array[] = $value;
				}
			}
		}
		if ($flag == true) {
			$message = "Ошибок и несоответствий в файлах не найдено";
			echo "<script type='text/javascript'>alert('{$message}');</script>";
		} else {
			foreach ($array as $value) {
				$request = $request . "document[]={$value}&";
			}
			header ("Location: ../web/index.php?{$request}");
		}
	}
?>