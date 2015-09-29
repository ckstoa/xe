<?php
if(!defined("__ZBXE__")) exit();

	if( $called_position == 'before_module_init' && $this->act == 'dispMemberSignUpForm' && $this->act =='dispMemberFindAccount' && Context::get('angular') != TRUE ){		
		exit();		
	}
		
	if( $called_position == 'before_module_init' && (!$this->mid || $this->mid == 'index') && !$this->act && !$this->module  ){		
		exit();		
	}

	if($addon_info->login_hooking == 'Y' && $called_position == 'before_module_init' ){
		
		Context::set('domain', BBS_HOST."/bbsv2/");

		if( ( $_COOKIE["STINFO"] ||  $_COOKIE["ST"] ) && !$_SESSION['is_logged']){
			
			$log_value = '';	
			if($_COOKIE["STINFO"]){
				
				$data = array(
				'STINFO' => $_COOKIE["STINFO"]
				);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://'.CTI_HOST.'/bbs/mem_check'); 
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
				   // 헤더는 제외하고 content 만 받음
				
				curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:')); 
				   // 응답 값을 브라우저에 표시하지 말고 값을 리턴
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				$content = curl_exec($ch); 
				curl_close($ch);
				$log_value = json_decode($content);

				
			}
			if(!$log_value->result->info->no){
				$data = array(
				'ST' => $_COOKIE["ST"]
				);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, BBS_API_HOSTS.'/bbs/xe_member/mem_check'); 
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				   // 헤더는 제외하고 content 만 받음
				
				curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:')); 
				   // 응답 값을 브라우저에 표시하지 말고 값을 리턴
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
				$content = curl_exec($ch); 
				curl_close($ch);
				$log_value = json_decode($content);
			}


		   if($log_value->result->info->no){
				$oMemberModel = getModel('member');

				$memInfo = $oMemberModel->getMemberInfoByMemberSrl($log_value->result->info->no);
				
				
			
				$_SESSION['is_logged'] = true;
				$_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['member_srl'] = $log_value->result->info->no;			
				setcookie('xe_logged', 'true', 0, '/');
				// Do not save your password in the session jiwojum;;
				//unset($this->memberInfo->password);
				// User Group Settings
				
				if($this->memberInfo->group_list) {
				   $group_srl_list = array_keys($this->memberInfo->group_list);
				   $_SESSION['group_srls'] = $group_srl_list;
					// If the group is designated as an administrator administrator
					$oMemberModel = getModel('member');
				
				}
				
				
				
				// Information stored in the session login user
				Context::set('is_logged', true);
				Context::set('logged_info', $memInfo);
				if($memInfo->is_admin == 'Y') $_SESSION['is_admin'] = 'Y';
				

			}
		}
	}
/*
	if( in_array($this->mid, array('eng_teacher_qna','eng_teacher_data','eng_teacher_notice')) && $called_position == 'before_module_proc' ){
		if(Context::get('auth_test') == TRUE ){
			$oLogIfo = Context::get('logged_info');
			$data = array(
			'id' => $oLogIfo->user_id,
			'saleinfo_id' => Context::get('search_keyword'),
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, BBS_API_HOSTS."/bbs/xe_member/member_auth"); 
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			   // 헤더는 제외하고 content 만 받음
			
			curl_setopt($ch,CURLOPT_HTTPHEADER,array('Expect:')); 
			   // 응답 값을 브라우저에 표시하지 말고 값을 리턴
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			$content = curl_exec($ch); 
			curl_close($ch);
			$log_value = json_decode($content);
			
			print_r($log_value->result);
			exit();
		}
	}
*/
	

	if( in_array($this->mid, array('eng_teacher_qna','eng_teacher_data','eng_teacher_notice')) && $called_position == 'before_module_proc' && !_IS_DEV_QA ){
			
		unset($args_auth);
		unset($mode);

		$oLog = Context::get('logged_info');
		
		$args_auth->s_user_id = $oLog->user_id;
		$args_auth->s_salesinfo_id = Context::get('search_keyword');
		
		if(!$args_auth->s_salesinfo_id || !$args_auth->s_user_id){
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array('error'=>'-4','message'=>'로그인 하지 않았거나 필수 파라메터 부족', 'info'=>$args_auth->s_salesinfo_id.$args_auth->s_user_id));
			exit();
		}

		$is_admin = '';
		if($oLog->group_list){
			foreach ($oLog->group_list as $key => $value) {
				if($value == 'admin' || $value == 'teacher' ){
					$is_admin = 'Y';
				}
			}
		}
		
		if($is_admin != 'Y'){
			
			$output_notice = executeQueryArray('addons.memberdangi.getAuthList', $args_auth);
			
			
			if($output_notice->data){
				foreach ($output_notice->data as $key => $value) {
					if(strtotime(date('Y-m-d')) >= strtotime($value->open_time) && strtotime(date('Y-m-d')) <= strtotime($value->close_time)  ){
						$mode[$key] = 'edit';
					}else if(strtotime(date('Y-m-d')) <= strtotime($value->close_time + 604800 )  ){
						$mode[$key] = 'view';
					}else{
						$mode[$key] = 'end';
					}
				}
			}
				
			if(in_array('edit', $mode)){
					
			}else if(in_array('view', $mode) && in_array($this->act, array('procBoardInsertComment','InsertDocument','DeleteDocument','DeleteComment','procFileUpload')) ){
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(array('error'=>'-2','message'=>'반별게시판 쓰기권한없음'));
				exit();
			}else{
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(array('error'=>'-3','message'=>'반별게시판 권한없음'));
				exit();
			}
		
		}
	}


	if($this->act == 'dispBoardContentList' && $called_position == 'after_module_proc' ){
			
		$this->add('per_page', $this->list_count);
		//Context::set('is_logged', true) = $this->list_count;
		$oLog = Context::get('logged_info');
		$this->add('member_info', $oLog);
		$this->add('is_admin', $_SESSION['is_admin']);
	}

	if( $called_position == 'after_module_proc' ){
		$oLog = Context::get('logged_info');	
		$this->add('is_admin',  $oLog->is_admin);
	}
	
	
	if( ($this->act == 'dispBoardContentList' || $this->act == 'dispBoardNoticeList' ) && $called_position == 'after_module_proc' && Context::get('angular') == TRUE ){
		
		if($this->act == 'dispBoardContentList'){
			$list_data = 'document_list';
		}else if($this->act == 'dispBoardNoticeList'){
			$list_data = 'notice_list';
		}	
		
		$list_arr = Context::get($list_data);
		$oDocumentModel = getModel('document');
		$oMember = getModel('member');
		
		foreach ($list_arr as $key => $value) {
			$document_srl = $value->document_srl;
			$member_srl = $value->variables['member_srl'];
			$oDocument = $oDocumentModel->getDocument($document_srl);
			$groupimg = $oMember->getGroupImageMark($member_srl);
			if($groupimg)$list_arr[$key]->variables['user_name']='<img src="http://'.BBS_HOST.$groupimg->src.'">';
			
			$comment_list = $oDocument->getComments();
			
			$file_list = $oDocument->getUploadedFiles();
			
			if($file_list){
				foreach ($file_list as $key3 => $value3) {
					$file_list[$key3]->download_url = htmlspecialchars_decode($value3->download_url);
				}
			}else{
				$file_list = array();
			}
			
			
			
			$extravars = $oDocument->getExtraVars();
			
			$dount = $oDocument->get('uploaded_count');
			$extra_arr = array(
				'saleinfo_id' => $extravars[1]->value,
				'answer_yn' => $extravars[2]->value,
				'file_list' => $file_list,
				
			);
			
			$list_arr[$key]->variables['extra_vars'] = $extra_arr;
			$list_arr[$key]->variables['regdate'] =  date('Y-m-d H:i:s', strtotime($list_arr[$key]->variables['regdate']));
			$list_arr[$key]->variables['last_update'] =  date('Y-m-d H:i:s', strtotime($list_arr[$key]->variables['last_update']));
			$c_count = $list_arr[$key]->variables['comment_count'];
			$list_arr[$key]->variables['comment_count'] = array('count' => $c_count );
			foreach ($comment_list as $key2 => $value2) {
				if($value2){
					$list_arr[$key]->variables['comment_count']['comment_lists'][$value2->variables['comment_srl']] = $value2->variables;
					$list_arr[$key]->variables['comment_count']['comment_lists'][$value2->variables['comment_srl']]['regdate'] =  date('Y-m-d H:i:s', strtotime($value2->variables['regdate']));
					$list_arr[$key]->variables['comment_count']['comment_lists'][$value2->variables['comment_srl']]['last_update'] =  date('Y-m-d H:i:s', strtotime($value2->variables['last_update']));
				} 
			}
			
			
		}
		$this->add('mid', Context::get('mid'));
		
		Context::set($list_data,$list_arr);
		

	}

	
	
	
	
	if( $called_position == 'after_module_proc' && $this->act == 'dispBoardContentView'   ){
					
		$oDocumentModel = getModel('document');
		$document_srl = Context::get('document_srl');
		$oDocument = $oDocumentModel->getDocument($document_srl);
		
		$file_list = $oDocument->getUploadedFiles();
			
		if($file_list){
			foreach ($file_list as $key3 => $value3) {
				$file_list[$key3]->download_url = htmlspecialchars_decode($value3->download_url);
			}
		}else{
			$file_list = array();
		}
			
		$this->add('file_list', $file_list);
		
	}
	
	
	if( $called_position == 'after_module_proc' && $this->act == 'procBoardInsertComment' && Context::get('search_keyword')  ){
					
		$document_srl = Context::get('document_srl');
		$module_srl = $this->module_srl;
		
		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($document_srl);
		$extravars = $oDocument->getExtraVars();
		
		$oLog = Context::get('logged_info');
		$is_teacher = '';
		if($oLog->group_list){
			foreach ($oLog->group_list as $key => $value) {
				if($value == 'admin' || $value == 'teacher' ){
					$is_teacher = 'Y';
				}
			}
		}
		
		if($extravars[2]->value != 'Y' &&  $is_teacher == 'Y' ){
			
			$oDocu = &getController('document');	
			$oInsert = $oDocu->insertDocumentExtraVar($module_srl, $document_srl, 2, 'Y', 'answer_yn');
			
		}
		
		
	}
	
	
	
	
	
	
	if( $this->act == 'procFileUpload' && $called_position == 'before_module_proc' && Context::get('angular') == TRUE ){
		
		$file_info = $_FILES['Filedata'];	
		
		$module_srl = $this->module_srl;
		if(!$module_srl) $module_srl = Context::get('module_srl');
		$upload_target_srl = Context::get('document_srl');
		
		$ofile = &getController('file');
		$addon_file = $ofile->insertFile($file_info, $module_srl, $upload_target_srl);
		$ofile->setFilesValid($upload_target_srl);
		$oDocu = &getController('document');	
		$oInsert = $oDocu->updateUploaedCount(array($upload_target_srl));
		
		echo json_encode($addon_file);
		Context::setResponseMethod('JSON');
		if($output->error != '0') $this->stop($output->message);
		
	}

	if( $this->act == 'procFileDelete' && $called_position == 'before_module_proc' && Context::get('angular') == TRUE ){

		Context::set('editor_sequence', 1);
		$_SESSION['upload_info'][1] = new stdClass;
		$_SESSION['upload_info'][1]->enabled = true;
		$_SESSION['upload_info'][1]->upload_target_srl = Context::get('document_srl');

		
	}

	
	
	if($called_position == 'before_module_proc' && $this->act=='dispBoardContentView' && $document_srl = Context::get('document_srl')) {
		
		$args->document_srl = $document_srl;

		executeQuery('addons.memberdangi.updateReadedCount', $args);
	
	}
	
	
	

?>