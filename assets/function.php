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
	/*Функция нормального отображения текста в файле*/
	function fix_readable_utf8($json_txt)
	{
		return preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');}, $json_txt);
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
		$documentCheckCorrect = preg_replace("/[\w_ -]/", "", $_GET['doc']);
		$filenameCheckCorrect = preg_replace("/[\w_ -]/", "", $_GET['filename']);
		if (($_GET['doc'] == $documentCheckCorrect) || ($_GET['filename'] == $filenameCheckCorrect))
    		exit_bad_request('bad parameter doc or filename');
		$doc = $_GET['doc'];
		$filename = $_GET['filename'];
		$file = "{$GLOBALS['pathDocs']}{$doc}/{$filename}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$filename}");
		readfile($file);
	}
	/*Функция ищет все pdf файлы по заданной папке*/
	function collect_pdf_file_names(string $value){
		$directory = "{$GLOBALS['pathDocs']}{$value}";
		$files = scandir($directory);
		$pdf = "pdf";
		foreach ($files as $filename) {
			$check_extension = pathinfo($filename);
			if ($check_extension['extension'] == $pdf) {
				$arrayFileName[] = $filename;
			}
		}
		return $arrayFileName;
	}
	/*Функция ищет все pdf файлы по заданной папке и делает по ним index.json с их параметрами.*/
	function downloadWrongJson(){
		$documentCheckCorrect = preg_replace("/[\w_ -]/", "", $_GET['doc']);
		if (($_GET['doc'] == $documentCheckCorrect))
    		exit_bad_request('bad parameter doc');
		$doc = $_GET['doc'];
		$doc_json_txt = file_get_contents("{$GLOBALS['pathDocs']}{$doc}/index.json");
		$decoded_doc_json = json_decode($doc_json_txt, true);
		$json_filename = "index.json";
		$jsonOutput = array(
			"Title" => $decoded_doc_json[Title],
			"BigTitle" => $decoded_doc_json[BigTitle],
			"versions" => array() );
  		$arrayNames = collect_pdf_file_names($doc);
  		foreach ($arrayNames as $i => $arrayName) {
  			$filename = "{$GLOBALS['pathDocs']}{$doc}/{$arrayName}";
			$md5 = md5_file($filename);
			$size = filesize($filename);
			$filedata = date("d.m.y", filectime($filename));
			$jsonOutput['versions'][$i] = array(
				"FileName" => $arrayName,
				"Size" => $size,
				"Md5" => $md5,
				"Data" => $filedata );
  		}
		$textJson = fix_readable_utf8(prettyPrint(json_encode($jsonOutput)));
		header('Content-Type: application/json');
		header("Content-Disposition: attachment; filename={$json_filename}");
		ob_end_clean();
		echo "$textJson";
		exit();
	}
	/*Функция скачивания документов на главной странице*/
	function downloadMainDoc(){
		$documentCheckCorrect = preg_replace("/[\w_ -]/", "", $_GET['doc']);
		if ($_GET['doc'] == $documentCheckCorrect)
    		exit_bad_request('bad parameter doc');
		$doc = $_GET['doc'];
		$doc_json_txt = file_get_contents("{$GLOBALS['pathDocs']}{$doc}/index.json");
		$decoded_doc_json = json_decode($doc_json_txt, true);
		$count = count($decoded_doc_json['versions']);
		$desired_file = $decoded_doc_json['versions'][$count-1]['FileName'];
		$file = "{$GLOBALS['pathDocs']}{$doc}/{$desired_file}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$desired_file}");
		readfile($file);
	}
	/*Функция выдачи файлов несоответствий или собщения о том, что ошибок нет*/
	function out_wrong_doc_file_names($wrong_docs_folder_names)
	{
		if (empty($wrong_docs_folder_names))
		{
			$message = "Ошибок и несоответствий в файлах не найдено";
			echo "<script type='text/javascript'>alert('{$message}');</script>";
		}
		else
		{
			foreach ($wrong_docs_folder_names as $nameDoc)
			{
				$request = $request . "document[]={$nameDoc}&";
			}
			header ("Location: ../web/index.php?{$request}");
		}
	}
	/*Функция создания структуры на основе данных реальных файлов*/
	function read_real_doc_description($doc_folder_name)
	{
		$pdf_file_names = collect_pdf_file_names($doc_folder_name);
		foreach ($pdf_file_names as $i => $pdf_file_name) {
			$filename = "{$GLOBALS['pathDocs']}{$doc_folder_name}/{$pdf_file_name}";
			$md5 = md5_file($filename);
			$size = filesize($filename);
			$filedata = date("d.m.y", filectime($filename));
			$array_of_structures['versions'][$i] = array(
			    "FileName"  => $pdf_file_name,
			    "Size" => $size,
			    "Md5" => $md5,
			    "Data" => $filedata );
		}
		return $array_of_structures;
	}
	/*Функция создания структуры на основе данных взятых из index.json.txt*/
	function read_index_doc_description($doc)
	{
		$count = count($doc['versions']);
		for ($i=0; $i < $count; $i++) {
			$path = $doc['versions'][$i];
			$fileName = $path['FileName'];
			$size = $path['Size'];
			$md5 = $path['Md5'];
			$data = $path['Data'];
			$array_of_structures['versions'][$i] = array(
			    "FileName"  => $fileName,
			    "Size" => $size,
			    "Md5" => $md5,
			    "Data" => $data );
		}
		return $array_of_structures;
	}
	/*Функция сравнения двух структур, представленных в виде строк
	Если строки разные, значит данные не совпадают*/
	function checkWrongDoc()
	{
		$doc_folder_names_json_txt = file_get_contents("{$GLOBALS['pathDocs']}index.json");
		$doc_folder_names = json_decode($doc_folder_names_json_txt, true);
		$wrong_docs_folder_names = array();
		foreach ($doc_folder_names as $doc_folder_name)
		{
			$doc_json_txt = file_get_contents("{$GLOBALS['pathDocs']}{$doc_folder_name}/index.json");
			$doc = json_decode($doc_json_txt, true);
			$real_doc = serialize(read_real_doc_description($doc_folder_name));
			$index_doc = serialize(read_index_doc_description($doc));
			if (strcasecmp($real_doc, $index_doc) !== 0){
				if (!in_array($doc_folder_name, $wrong_docs_folder_names)) {
					$wrong_docs_folder_names[] = $doc_folder_name;
				}
			}
		}
		out_wrong_doc_file_names($wrong_docs_folder_names);
	}
?>