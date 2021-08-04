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
		return preg_replace_callback('/\\\\u(\w{4})/', function ($matches) {
			return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
		}, $json_txt);
	}
	function nice_json_encode($obj)
	{
		return fix_readable_utf8(prettyPrint(json_encode($obj)));
	}
	/*Функция выхода с ошибкой*/
	function exit_with_error($error_text)
	{
	  	if (function_exists('write_to_log_auth_info'))
	    	write_to_log_auth_info();
		exit;
	}
	/*Функция выхода с неправильным запросом*/
	function exit_bad_request($error_text)
	{
		header("HTTP/1.1 400 Bad Request");
		echo 'bad request';
		exit_with_error($error_text);
	}
	/*Функция проверки get элементов*/
	function SafeGetFilenameArg($name)
	{
		if (!isset($_GET[$name]))
			exit_bad_request('skipped argument $name');
		$value = $_GET[$name];
		$value_without_wrong_chars = preg_replace("/[\w_ -]/", "", $value);
		if ($value == $value_without_wrong_chars)
    		exit_bad_request('bad argument $name');
    	return $value;
	}
	/*Функция скачивания документа конкретной версии*/
	function downloadDocVersion()
	{
		global $pathDocs;
		$doc = SafeGetFilenameArg("doc");
		$filename = SafeGetFilenameArg("filename");
		$file = "{$pathDocs}{$doc}/{$filename}";
		header('Content-Type: application/pdf');
		header("Content-Disposition: attachment; filename={$filename}");
		readfile($file);
	}
	/*Функция ищет все pdf файлы по заданной папке*/
	function collect_pdf_file_names($value)
	{
		global $pathDocs;
		$directory = "{$pathDocs}{$value}";
		$files = scandir($directory);
		$pdf = "pdf";
		foreach ($files as $filename) {
			$check_extension = pathinfo($filename);
			if ($check_extension['extension'] == $pdf)
				$arrayFileName[] = $filename;
		}
		return $arrayFileName;
	}
	/*Функция скачивания файлов index.json.txt с ошибками*/
	function downloadWrongJson()
	{
		global $pathDocs;
		$doc_folder_name = SafeGetFilenameArg("doc");
		$doc_json_txt = file_get_contents("{$pathDocs}{$doc_folder_name}/index.json");
		$doc = json_decode($doc_json_txt, true);
		$real_doc = read_real_doc_description($doc, $doc_folder_name);
		header('Content-Type: application/json');
		header("Content-Disposition: attachment; filename=index.json");
		ob_end_clean();
		echo fix_readable_utf8(prettyPrint(json_encode($real_doc)));
		exit();
	}
	/*Функция скачивания документов на главной странице*/
	function downloadMainDoc()
	{
		global $pathDocs;
		$doc = SafeGetFilenameArg("doc");
		$doc_json_txt = file_get_contents("{$pathDocs}{$doc}/index.json");
		$decoded_doc_json = json_decode($doc_json_txt, true);
		$count = count($decoded_doc_json['versions']);
		$desired_file = $decoded_doc_json['versions'][$count-1]['FileName'];
		$file = "{$pathDocs}{$doc}/{$desired_file}";
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
			echo "{$message}";
		}
		else
		{
			$request = "";
			foreach ($wrong_docs_folder_names as $nameDoc)
			{
				$request.= "document[]={$nameDoc}&";
			}
				header ("Location: ../web/index.php?{$request}");																			
		}
	}
	/*Функция дополнения имеющейся json структуры недостоющими данными, взятыми на основе хранимых файлов*/
	function read_real_doc_description($doc, $doc_folder_name)
	{
		global $pathDocs;
		$existed_versionsByFileNames = array();
		$count = count($doc['versions']);
		for ($i=0; $i < $count; $i++) {
			$existed_versionsByFileNames[$doc['versions'][$i]['FileName']] = $doc['versions'];
		}
		$new_versions = array();
		$pdf_file_names = collect_pdf_file_names($doc_folder_name);
		foreach ($pdf_file_names as $i => $pdf_file_name) {
			$filepath = "{$pathDocs}{$doc_folder_name}/{$pdf_file_name}";
			$version = null;
			if (isset($existed_versionsByFileNames[$pdf_file_name]))
			{
				$version = $existed_versionsByFileNames[$pdf_file_name];
				$version = array(
					'FileName' => $existed_versionsByFileNames[$pdf_file_name][$i]['FileName'],
					'Size' => filesize($filepath),
					'Md5' => md5_file($filepath),
					'Date' => $existed_versionsByFileNames[$pdf_file_name][$i]['Date'] );
				unset($existed_versionsByFileNames[$pdf_file_name]);
			} else {
				$version = array(
					'FileName' => $pdf_file_name,
					'Size' => filesize($filepath),
					'Md5' => md5_file($filepath),
					'Date' => date("d.m.y", filectime($filepath)) );
			}
			$new_versions[] = $version;
		}
		$doc['versions'] = $new_versions;
		return $doc;
	}
	/*Функция сравнения двух структур, представленных в виде строк
	Если строки разные, значит данные не совпадают*/
	function checkWrongDoc()
	{
		global $pathDocs;
		$doc_folder_names_json_txt = file_get_contents("{$pathDocs}index.json");
		$doc_folder_names = json_decode($doc_folder_names_json_txt, true);
		$wrong_docs_folder_names = array();
		foreach ($doc_folder_names as $doc_folder_name)
		{
			$doc_json_txt = file_get_contents("{$pathDocs}{$doc_folder_name}/index.json");
			$doc = json_decode($doc_json_txt, true);
			$serialized_doc = serialize($doc);
			$real_doc = serialize(read_real_doc_description($doc, $doc_folder_name));
			if (strcasecmp($real_doc, $serialized_doc) !== 0){
				if (!in_array($doc_folder_name, $wrong_docs_folder_names)) {
					$wrong_docs_folder_names[] = $doc_folder_name;
				}
			}
		}
		out_wrong_doc_file_names($wrong_docs_folder_names);
	}
	function CheckAction()
	{
		if (isset($_GET['doc'])){
			downloadWrongJson();
		} else {
			checkWrongDoc();
		}
	}
	function MainAction()
	{
		if (isset($_GET['doc']))
			downloadMainDoc();
	}
	function DetailAction()
	{
		global $pathAsset, $base_url;
		if (isset($_GET['doc']))
			require("{$pathAsset}document.php");
	}
	function getDoclist()
	{
		global $pathDocs, $base_url;
		$docs_json_txt = file_get_contents("{$pathDocs}index.json");
		$docs = json_decode($docs_json_txt, true);
		$documents= array();
		foreach ($docs as $dname)
		{
			$doc_json_txt = file_get_contents("$pathDocs$dname/index.json");
			$doc= json_decode($doc_json_txt, true);
			$url= "{$base_url}index.php?doc=$dname";
			$documents[]= array(
				'Title'=>$doc['Title']
				,'url'=>array( 'doc'=>$url ,'detail'=>$url.'&action=detail' )
			);
		}
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: *');
		header('Content-Type: application/json');
		echo nice_json_encode($documents);
	}
?>