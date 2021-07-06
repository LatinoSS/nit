<?php
	/*Функция форматирования json файла*/
	function prettyPrint( $json )
	{
	    $result = '';
	    $level = 0;
	    $in_quotes = false;
	    $in_escape = false;
	    $ends_line_level = NULL;
	    $json_length = strlen( $json );
	    for( $i = 0; $i < $json_length; $i++ ) {
	        $char = $json[$i];
	        $new_line_level = NULL;
	        $post = "";
	        if( $ends_line_level !== NULL ) {
	            $new_line_level = $ends_line_level;
	            $ends_line_level = NULL;
	        }
	        if ( $in_escape ) {
	            $in_escape = false;
	        } else if( $char === '"' ) {
	            $in_quotes = !$in_quotes;
	        } else if( ! $in_quotes ) {
	            switch( $char ) {
	                case '}': case ']':
	                    $level--;
	                    $ends_line_level = NULL;
	                    $new_line_level = $level;
	                    break;
	                case '{': case '[':
	                    $level++;
	                case ',':
	                    $ends_line_level = $level;
	                    break;
	                case ':':
	                    $post = " ";
	                    break;
	                case " ": case "\t": case "\n": case "\r":
	                    $char = "";
	                    $ends_line_level = $new_line_level;
	                    $new_line_level = NULL;
	                    break;
	            }
	        } else if ( $char === '\\' ) {
	            $in_escape = true;
	        }
	        if( $new_line_level !== NULL ) {
	            $result .= "\n".str_repeat( "\t", $new_line_level );
	        }
	        $result .= $char.$post;
	    }
	    return $result;
	}
	/*Функция выхода с ошибкой*/
	function exit_with_error($error_text)
	{
	  	write_to_log($error_text);
	  	if (function_exists('write_to_log_auth_info'))
	    	write_to_log_auth_info();
		write_to_log('$_GET:');
		write_to_log($_GET);
		exit;
	}
	/*Функция выхода с неправильным запросом*/
	function exit_bad_request($error_text)
	{
		header("HTTP/1.1 400 Bad Request");
		echo 'bad request';
		exit_with_error($error_text);
	}
	/*Функция скачивания документа конкретной версии*/
	function downloadDocVersion(){
		$documentCheckCorrect = preg_replace("/[^\w_ -]/", "", $_GET['doc']);
		$filenameCheckCorrect = preg_replace("/[^\w_ -]/", "", $_GET['filename']);
		if (('' == $documentCheckCorrect) || ('' == $filenameCheckCorrect))
    		exit_bad_request('bad parameter doc or filename');
		$doc = $_GET['doc'];
		$filename = $_GET['filename'];
		$file = "{$GLOBALS['pathDocs']}{$doc}/{$filename}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$filename}");
		readfile($file);
	}
	/*Функция ищет все pdf файлы по заданной папке*/
	function checkExistDocs(string $value){
		$directory = "{$GLOBALS['pathDocs']}{$value}";
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
		$documentCheckCorrect = preg_replace("/[^\w_ -]/", "", $_GET['doc']);
		if (('' == $documentCheckCorrect))
    		exit_bad_request('bad parameter doc');
		$doc = $_GET['doc'];
		$jsonDocData = file_get_contents("{$GLOBALS['pathDocs']}{$doc}/index.json");
		$decodeJsonDocData = json_decode($jsonDocData, true);
		$jsonfilename = "index.json";
		$text = "{\"Title\": \"{$decodeJsonDocData[Title]}\", \"BigTitle\": \"{$decodeJsonDocData[BigTitle]}\", \"versions\": [ \n";
  		$arrayName = checkExistDocs($doc); //Поиск всех pdf файлов в заданой папке
  		$count = count($arrayName);
		for ($i=0; $i < $count; $i++) {
			$filename = "{$GLOBALS['pathDocs']}{$doc}/{$arrayName[$i]}";
			$md5 = md5_file($filename);
			$size = filesize($filename);
			$filedata = date("d.m.y", filectime($filename));
			$text = $text . "{\"FileName\": \"{$arrayName[$i]}\", \"Size\": {$size}, \"Md5\": \"{$md5}\", \"Data\": \"{$filedata}\"},\n";
		}
		$text = substr($text,0,-2);
		$text = $text . "]}";
		$textJson = prettyPrint($text);
		header('Content-Type: application/json');
		header("Content-Disposition: attachment; filename={$jsonfilename}");
		ob_end_clean();
		echo "$textJson";
		exit();
	}
	/*Функция скачивания документов на главной странице*/
	function downloadMainDoc(){
		$documentCheckCorrect = preg_replace("/[^\w_ -]/", "", $_GET['doc']);
		if ('' == $documentCheckCorrect)
    		exit_bad_request('bad parameter doc');
		$doc = $_GET['doc'];
		$jsonDocData = file_get_contents("{$GLOBALS['pathDocs']}{$doc}/index.json");
		$decodeJsonDocData = json_decode($jsonDocData, true);
		$count = count($decodeJsonDocData['versions']);
		$request = "{$decodeJsonDocData['versions'][$count-1]['FileName']}";
		$file = "{$GLOBALS['pathDocs']}{$doc}/{$request}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$request}");
		readfile($file);
	}
	/*Функция просмотра всех возможных документов и определение расхождений данных документа с данными из index.json
	Если расхождений нет, то откывается alert с соответствующим текстом
	Если расхождения есть, то откывается страница с проблемными документами, можно скачать коррекные данные*/
	function checkWrongDoc(){
		$flag = true;
		$jsonDocs = file_get_contents("{$GLOBALS['pathDocs']}index.json");
		$decodeJsonDocs = json_decode($jsonDocs, true);
		$array = array();
		foreach ($decodeJsonDocs as $key => $decodeJsonDoc) {
			$jsonDoc = file_get_contents("{$GLOBALS['pathDocs']}{$decodeJsonDoc}/index.json");
			$decodeDataDoc = json_decode($jsonDoc, true);
			$countDoc = count($decodeDataDoc['versions']);
			for ($j=0; $j < $countDoc; $j++) {
				$filename = "{$GLOBALS['pathDocs']}{$decodeJsonDoc}/{$decodeDataDoc['versions'][$j]['FileName']}";
				$md5 = md5_file($filename);
				$size =filesize($filename);
				if ($md5 != $decodeDataDoc['versions'][$j]['Md5'] || $size != $decodeDataDoc['versions'][$j]['Size']) {
					$flag = false;
					if (!in_array($decodeJsonDoc, $array)) {
						$array[] = $decodeJsonDoc;
					}
				}
				$filedata = date("d.m.y", filectime($filename));
				$textMain[$decodeJsonDoc] = $textMain[$decodeJsonDoc] . "{\"FileName\": \"{$decodeDataDoc['versions'][$j]['FileName']}\", \"Size\": {$size}, \"Md5\": \"{$md5}\", \"Data\": \"{$filedata}\"},\n";
			}
			$textMain[$decodeJsonDoc] = substr($textMain[$decodeJsonDoc],0,-2);
			$textMain[$decodeJsonDoc] = $textMain[$decodeJsonDoc] . "]}";
			$arrayName = checkExistDocs($decodeJsonDoc); //Поиск всех pdf файлов в заданой папке
  			$count = count($arrayName);
			for ($i=0; $i < $count; $i++) {
				$filename = "{$GLOBALS['pathDocs']}{$decodeJsonDoc}/{$arrayName[$i]}";
				$md5 = md5_file($filename);
				$size = filesize($filename);
				$filedata = date("d.m.y", filectime($filename));
				$text[$decodeJsonDoc] = $text[$decodeJsonDoc] . "{\"FileName\": \"{$arrayName[$i]}\", \"Size\": {$size}, \"Md5\": \"{$md5}\", \"Data\": \"{$filedata}\"},\n";
			}
			$text[$decodeJsonDoc] = substr($text[$decodeJsonDoc],0,-2);
			$text[$decodeJsonDoc] = $text[$decodeJsonDoc] . "]}";
			if (strcasecmp($textMain[$decodeJsonDoc], $text[$decodeJsonDoc]) !== 0) {
				$flag = false;
				if (!in_array($decodeJsonDoc, $array)) {
					$array[] = $decodeJsonDoc;
				}
			}
		}
		if ($flag == true) {
			$message = "Ошибок и несоответствий в файлах не найдено";
			echo "<script type='text/javascript'>alert('{$message}');</script>";
		} else {
			foreach ($array as $nameDoc) {
				$request = $request . "document[]={$nameDoc}&";
			}
			header ("Location: ../web/index.php?{$request}");
		}
	}
?>