<?php

class qa_prevent_simultaneous_edits_event
{
	function process_event( $event, $userid, $handle, $cookieid, $params )
	{
		if(($event == 'q_edit' || $event == 'a_edit' || $event == 'c_edit' || $event == 'q_view') && (bool)qa_opt('pse_active'))
		{
			// clean all entries from database that are older than 10 min
			qa_db_query_sub('DELETE FROM `^edit_preventer`
								WHERE `accessed` < (NOW() - INTERVAL ' . (qa_opt('pse_lock_time')/60) . ' MINUTE)
							');
		}
	}
}