<?php

/*
	Question2Answer Plugin: Prevent Simultaneous Edits
	License: http://www.gnu.org/licenses/gpl.html
*/

	class qa_html_theme_layer extends qa_html_theme_base {

		function doctype()
		{
			if($this->request == 'admin/permissions' && qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN) {
				$permits[] = 'permit_view_locked_edits';
				foreach($permits as $optionname) {
					$value = qa_opt($optionname);
					$optionfield=array(
						'id' => $optionname,
						'label' => qa_lang_html('qa_prevent_sim_edits_lang/'.$optionname).':',
						'tags' => 'NAME="option_'.$optionname.'" ID="option_'.$optionname.'"',
						'error' => qa_html(@$errors[$optionname]),
					);
					
					$permitoptions=qa_admin_permit_options(QA_PERMIT_ALL, QA_PERMIT_ADMINS, (!QA_FINAL_EXTERNAL_USERS) && qa_opt('confirm_user_emails'));
					
					if (count($permitoptions)>1)
						qa_optionfield_make_select($optionfield, $permitoptions, $value,
							($value==QA_PERMIT_CONFIRMED) ? QA_PERMIT_USERS : min(array_keys($permitoptions)));
					$this->content['form']['fields'][$optionname]=$optionfield;

					$this->content['form']['fields'][$optionname.'_points']= array(
						'id' => $optionname.'_points',
						'tags' => 'NAME="option_'.$optionname.'_points" ID="option_'.$optionname.'_points"',
						'type'=>'number',
						'value'=>qa_opt($optionname.'_points'),
						'prefix'=>qa_lang_html('admin/users_must_have').'&nbsp;',
						'note'=>qa_lang_html('admin/points')
					);
					$checkboxtodisplay[$optionname.'_points']='(option_'.$optionname.'=='.qa_js(QA_PERMIT_POINTS).') ||(option_'.$optionname.'=='.qa_js(QA_PERMIT_POINTS_CONFIRMED).')';
				}
				qa_set_display_rules($this->content, $checkboxtodisplay);
			}
			parent::doctype();
		}
	
		// override body_script to insert javascript warning
		function body_script() {
		
			if(!(bool)qa_opt('pse_active')) {
				qa_html_theme_base::body_script();
				return;
			}
		
			// check if user comes to edit page
			if (isset($this->content['form_q_edit'])) {
							
				// get userid
				$userid = qa_get_logged_in_userid();
				// get postid
				$postid = $this->content['q_view']['raw']['postid'];
				
				// check if post has been edited within last 10 minutes
				// query events
				$queryEditPost = qa_db_query_sub('SELECT postid,UNIX_TIMESTAMP(accessed) AS accessed,userid
											FROM `^edit_preventer`
											WHERE `postid`=$
											ORDER BY postid DESC
											LIMIT 1
											', $postid);

				$postEditExists = false;
				$sameUserEditsAgain = false;
				while ( ($row = qa_db_read_one_assoc($queryEditPost,true)) !== null ) {
					$postEditExists = true;
					
					// do not warn
					if($userid == $row['userid']) {
						$sameUserEditsAgain = true;
						// update edit time
						qa_db_query_sub('UPDATE `^edit_preventer` SET
								`accessed` = NOW()
								WHERE `postid`=$
							', $postid);
						break;
					}
					else if(time()-$row['accessed'] > qa_opt('pse_lock_time')) {
						// update edit time
						qa_db_query_sub('UPDATE `^edit_preventer` SET
								`accessed` = NOW(),
								`userid` = $
								WHERE `postid`=$
							', $userid, $postid);
						break;
					}
					// get name of user that has been editing
					if(QA_FINAL_EXTERNAL_USERS)
						if((bool)qa_opt('pse_EEU'))
						{
							$query = 'SELECT ' . qa_opt('pse_EUTH') . ' AS handle FROM `' . qa_opt('pse_EUT') .  '` WHERE ' . qa_opt('pse_EUTK') . ' = ' . $row['userid'];
							$username = qa_db_read_one_assoc(qa_db_query_sub($query))['handle'];
						}
						else
							$username = qa_lang_html('qa_prevent_sim_edits_lang/a_user');
					else
						$username = qa_db_read_one_assoc(qa_db_query_sub('SELECT handle FROM ^users WHERE userid = #', $row['userid']))['handle'];
					
					// notice frontend and bring back to question page
					$this->output('<script type="text/javascript">
						alert("'.qa_lang_html('qa_prevent_sim_edits_lang/post_edited_by').' '.$username.'.\n'.qa_lang_html_sub('qa_prevent_sim_edits_lang/try_again_later', qa_opt('pse_lock_time')).'");
						history.go(-1);
					</script>');
				}
				
				if(!$postEditExists) {
					// ignore 5 min (300 sec) after question post, so that edit by owner is not saved to edit_preventer
					// if more than 5 min save edit
					if( time() - $this->content['q_view']['raw']['created'] > qa_opt('pse_ignore_time')) {
						// should not happen as only members are allowed to edit
						// if($userid===NULL) { $userid = 1; }
						
						// if no post edit exists, then insert data into table
						qa_db_query_sub('INSERT INTO ^edit_preventer (postid, accessed, userid) VALUES ($, NOW(), $)', $postid, $userid);					
					}
				}

			}
			
			
			// call default method output
			qa_html_theme_base::body_script();			

		}

	} // end
	

	
/*
	Omit PHP closing tag to help avoid accidental output
*/