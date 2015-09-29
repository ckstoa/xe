<?php
    if(!defined("__ZBXE__") || !defined("__XE__")) exit();
/*
    if($called_position == 'after_module_proc'){
        if( ( $this->act=='dispBoardContent' || (Context::get('document_list') || Context::get('notice_list')) ) && ($addon_info->notice_document_srl  || ($addon_info->notice_module_srl && ($this->module_srl != $addon_info->notice_module_srl)))) {

			if(!$addon_info->notice_display) $addon_info->notice_display='top';
			if(!$addon_info->notice_target) $addon_info->notice_target='notice';

			unset($args_notice);

			$args_notice->notice_document_srl = $addon_info->notice_document_srl;

			if($addon_info->notice_module_srl &&  ($this->module_srl != $addon_info->notice_module_srl)) {
				$args_notice->module_srl = $addon_info->notice_module_srl;

				$args_notice->category_srl = $addon_info->notice_category_srl;
				if($addon_info->notice_target=='notice') $args_notice->s_is_notice = 'Y';
				else $args_notice->s_is_notice='';
			}


			if(!$addon_info->notice_sort_index) $args_notice->sort_index = 'list_order';
			else $args_notice->sort_index = $addon_info->notice_sort_index;

			if(!$addon_info->notice_order_type) $args_notice->order_type = 'asc';
			else $args_notice->order_type = $addon_info->notice_order_type;

			$args_notice->list_count = $addon_info->notice_list_count;
			$args_notice->page_count =1;

			$args_notice->statusList = 'PUBLIC,SECRET';



			$output_notice = executeQueryArray('addons.sejin7940_all_notice.getNoticeList', $args_notice);

			if(count($output_notice->data)) {
				$oDocumentModel = getModel('document');

				foreach($output_notice->data as $key_notice => $val_notice) {
					$notice_list[$val_notice->document_srl] = $oDocumentModel->getDocument($val_notice->document_srl);
				}


				$target_addon = 'hot_document';
				$oAddonAdminModel = getAdminModel('addon');
				$target_addon_active = $oAddonAdminModel->isActivatedAddon($target_addon);
				$target_addon_info = $oAddonAdminModel->getAddonInfoXml($target_addon);

				if(!$target_addon_active || !(!$target_addon_info->mid_list || ( in_array(Context::get('mid'),$target_addon_info->mid_list) ))) {
					if(Context::get('document_list')) {
						// 전체공지를 일반공지 하단에 배치
						if($addon_info->notice_display=='bottom') {
							$notice_list = array_merge(Context::get('document_list'),$notice_list);
						}
						// 전체공지를 일반공지 상단에 배치
						elseif($addon_info->notice_display=='top') {
							$notice_list = array_merge($notice_list,Context::get('document_list'));
						}
					}
				}

				Context::set('document_list', $notice_list);
			}
		}
    }
*/
	
	
	
	
	

 
?>
