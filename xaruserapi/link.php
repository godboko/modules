<?php
 
function menutree_userapi_link($args) {

	$delimiter = '|';
	
	extract($args);

	$info = explode($delimiter,$link);
	$data['text'] = $info[0];
	if (isset($info[1])) {
		$data['url'] = $info[1];
	} else {
		$data['url'] = xarServer::getCurrentURL(). '#';
	}
	if (isset($info[2])) {
		$data['status'] = $info[2];
	} else {
		$data['status'] = '1';
	}
	
	return $data;

}

?>