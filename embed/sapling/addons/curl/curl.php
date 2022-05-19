<?php

if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

// cURL
$FUNCS->register_tag('curl', function($params, $node){
	global $FUNCS, $CTX;

	extract($FUNCS->get_named_vars(
		array(
			'token' => '',
			'type' => 'form-urlencoded',
			'url' => '',
			'method' => '',
			'headers' => '',
			'data' => '',
			'into' => '',
			'as_json' => '',
			'scope' => ''
		),
		$params)
	);

	$token = trim($token);
	$url = trim($url);
	$method = trim($method);
	$headers = trim($headers);
	$data = trim($data);
	$into = trim($into);
	$as_json = trim($as_json);
	$scope = trim($scope);
	$type = trim($type);

	if (!extension_loaded('curl')) {
		die("Error in tag '".$node->name."': curl extension not available");
	}

	if (!strlen($url)) {
		die("Error in tag '".$node->name."': url empty");
	}

	$headers = array();

	if ($type == 'json') {
		array_push($headers, 'Accept: application/json','Content-Type: application/json');
	} else if ($type == 'form-urlencoded') {
		array_push($headers, 'Content-Type: application/x-www-form-urlencoded');
	}
	if (strlen($token)) {
		array_push($headers, "Authorization: Bearer $token");
	}

	$ch = curl_init();
	if ($type == 'form-urlencoded') {
		curl_setopt($ch, CURLOPT_URL, $url . http_build_query($data));
	} else {curl_setopt($ch, CURLOPT_URL, $url);}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	if ($method == 'post') {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	} elseif ($method == 'put') {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	} elseif ($method == 'patch') {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	} elseif ($method == 'delete') {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_USERAGENT, "CouchCMS " . $CTX->get('k_cms_version') );
	curl_setopt($ch, CURLOPT_REFERER, $CTX->get('k_page_link') );

	$response = curl_exec($ch);
	curl_close($ch);
	if(strlen($into)) {
		if($as_json) {
			$CTX->set($into, $FUNCS->json_decode($response), 'global');
		} elseif(strlen($scope)) {
			$CTX->set($into, $response, $scope);
		} else {
			$CTX->set($into, $response);
		}
	} else {
		return $response;
	}
});