<?php

	function qa_get_permit_options() {
		$permits = qa_get_permit_options_base();
		$permits[] = 'permit_view_locked_edits';
		return $permits;
	}
	function qa_get_request_content() {
		$qa_content = qa_get_request_content_base();
		
		// permissions
		
		if(isset($qa_content['form_profile']['fields']['permits'])) {
			$ov = $qa_content['form_profile']['fields']['permits']['value'];
			$ov = str_replace('[profile/permit_view_locked_edits]',qa_lang('qa_prevent_sim_edits_lang/permit_view_locked_edits'),$ov);
			$qa_content['form_profile']['fields']['permits']['value'] = $ov;
		}
		return $qa_content;
	}						
/*							  
		Omit PHP closing tag to help avoid accidental output
*/							  
						  

