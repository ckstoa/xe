<?php
function qUpload($addon_info, $urlPath, $srl, $urlUpload){
	/*$oFM = getModel("file");

	$columnList = array('source_filename', 'uploaded_filename', 'upload_target_srl', 'module_srl', 'direct_download');
	$file_obj = $oFM->getFile($srl, $columnList);*/

	$ch = curl_init();

	$conn_id = ftp_connect($addon_info->upload);
	$login_result = ftp_login($conn_id, $addon_info->upload_id, $addon_info->upload_pw);

	if(preg_match("/\.(jpg|jpeg|gif|png|wmv|wma|mpg|mpeg|avi|swf|flv|mp1|mp2|mp3|mp4|asf|wav|asx|mid|midi|asf|mov|moov|qt|rm|ram|ra|rmm|m4v)$/i", $urlUpload))
		$xx = "." . pathinfo($urlUpload, PATHINFO_EXTENSION);
	else return;

	$fp = fopen($urlUpload, "r");
	ftp_fput($conn_id, $addon_info->upload_fol . "/" . md5($urlUpload) . ".dat" . $xx, $fp, FTP_BINARY);

	ftp_close($conn_id);  
	fclose($fp);

	/*$args = new stdClass;
	$args->file_srl = Context::get("file_srl");
	$args->uploaded_filename = ;
	$args->module_srl = $file_obj->module_srl;
	$args->upload_target_srl = $file_obj->upload_target_srl;
	executeQuery('file.updateFile', $args);*/

	@unlink($urlUpload);
	return $urlPath . md5($urlUpload) . ".dat" . $xx;
}
