<?php

function page_takeover($landing_page_url) {
	$landing_page = get_resource($landing_page_url);
	if(!empty($landing_page)) {
		echo $landing_page;
		exit;
	}
}


function get_resource($url) {
    $resource = '';
$test = array();
	if(!empty($url)) {
		$response = file_get_contents($url);
		if(!function_exists('url_to_absolute')) {
		  require_once(dirname(__FILE__).'/../vendor/url-to-absolute/url-to-absolute.php');
		}
		$doc = new DOMDocument();
		libxml_use_internal_errors(TRUE); //disable libxml errors

		$doc->loadHTML($response);
		libxml_clear_errors(); //remove errors for yucky html
		$doc_xpath = new DOMXPath($doc);
		
		foreach($doc_xpath->query('//a | //link') as $element) {
			$href = $element->getAttribute('href');
		  if(isset($href) && strlen($href) && $href[0] != "#") {
			$element->setAttribute('href', url_to_absolute($url, $href) );
		  }
		}
		foreach($doc_xpath->query('//img | //script')  as $element) {
			$src = $element->getAttribute('src');
		  if(isset($src) && strlen($src)) {
			$element->setAttribute('src', url_to_absolute($url, $src) );
		  }
		}
		foreach($doc_xpath->query('//form')  as $element) {
			$action = $element->getAttribute('action');
		  if(isset($action) && strlen($action)) {
			$element->setAttribute('action', url_to_absolute($url, $action) );
		  } else {
			$element->setAttribute('action', $url);
		  }
		}
		$resource = $doc->saveHTML();
	  }
    return $resource;
}

?>
