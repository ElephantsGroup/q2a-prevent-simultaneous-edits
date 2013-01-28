<?php

/*
	Question2Answer Plugin: Prevent Simultaneous Edits
	License: http://www.gnu.org/licenses/gpl.html
*/

	class qa_html_theme_layer extends qa_html_theme_base {

		// override body_script to insert javascript warning
		function body_script() {
		
			// check if user comes to edit page
			if (isset($this->content['form_q_edit'])) {
			
				// clean all entries from database that are older than 10 min
				qa_db_query_sub('DELETE FROM `^edit_preventer`
									WHERE `accessed` < (NOW() - INTERVAL 10 MINUTE)
								');
				
				// get userid
				$userid = qa_get_logged_in_userid();
				// get postid
				$postid = $this->content['q_view']['raw']['postid'];
				
				// check if post has been edited within last 10 minutes
				// query events
				$queryEditPost = qa_db_query_sub('SELECT postid,accessed,userid
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
					// get name of user that has been editing
					$username = qa_db_read_one_assoc(qa_db_query_sub('SELECT handle FROM ^users WHERE userid = #', $row['userid']));
					
					// notice frontend and bring back to question page
					$this->output('<script type="text/javascript">
						alert("'.qa_lang_html('qa_prevent_sim_edits_lang/post_edited_by').' '.$username['handle'].'.\n'.qa_lang_html('qa_prevent_sim_edits_lang/try_again_later').'");
						history.go(-1);
					</script>');
				}
				
				if(!$postEditExists) {
					// ignore 5 min (300 sec) after question post, so that edit by owner is not saved to edit_preventer
					// if more than 5 min save edit
					if( time() - $this->content['q_view']['raw']['created'] > 300) {
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