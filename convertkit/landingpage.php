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
	if(!empty($url)) {
		$response = get_request($url);
		if(!function_exists('str_get_html')) {
		  require_once(dirname(__FILE__).'/../vendor/simple-html-dom/simple-html-dom.php');
		}
		if(!function_exists('url_to_absolute')) {
		  require_once(dirname(__FILE__).'/../vendor/url-to-absolute/url-to-absolute.php');
		}
		$url_parts = parse_url($url);
		//$body = wp_remote_retrieve_body($response);
		$body = $response;
		$html = str_get_html($body);
		foreach($html->find('a, link') as $element) {
		  if(isset($element->href) && $element->href[0] != "#") {
			$element->href = url_to_absolute($url, $element->href);
		  }
		}
		foreach($html->find('img, script') as $element) {
		  if(isset($element->src)) {
			$element->src = url_to_absolute($url, $element->src);
		  }
		}
		foreach($html->find('form') as $element) {
		  if(isset($element->action)) {
			$element->action = url_to_absolute($url, $element->action);
		  } else {
			$element->action = $url;
		  }
		}
		$resource = $html->save();
	  }
    return $resource;
}

function get_request($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    $results = curl_exec($ch);
    curl_close($ch);
    return $results;
}

?>
