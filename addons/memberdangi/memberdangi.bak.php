<?php
if(!defined("__ZBXE__")) exit();

	

	if($addon_info->login_hooking == 'Y' && $called_position == 'before_module_init' ){
		
		Context::set('domain', BBS_HOST."/bbsv2/");
		if($_SESSION['cti_admin']){
			$_SESSION['is_admin'] = 'Y';
		}
		if($_SESSION['ipaddress']){
			$_SESSION['ipaddress'] = $_SERVER['REMOTE_ADDR'];
			$_SESSION['is_logged'] = true;
			setcookie('xe_logged', 'true', 0, '/');
		}
		
		if($_COOKIE["ST"] && !$_SESSION['is_logged']){
			
			
			// curl이 설치 되었는지 확인
			if (function_exists('curl_init')) {
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

			} else {
			   // curl 라이브러리가 설치 되지 않음. 
			}
								
			//echo '<br />'.$content;
			   $log_value = json_decode($content);
			   //echo BBS_API_HOST."<br />";
			  // print_r($log_value);
			   //exit();

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

	if($this->act == 'dispBoardContentList' && $called_position == 'after_module_proc' ){
			
		
		$this->add('per_page', $this->list_count);
		//Context::set('is_logged', true) = $this->list_count;


	}
	
	
	
	

?>