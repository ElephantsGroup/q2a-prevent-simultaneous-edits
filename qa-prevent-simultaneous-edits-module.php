<?php

/*
	Question2Answer Plugin: Prevent Simultaneous Edits
	License: http://www.gnu.org/licenses/gpl.html
*/

	class qa_prevent_simultaneous_edits_module {
		
		// initialize db-table 'edit_preventer' if it does not exist yet
		function init_queries($tableslc) {
			$tablename=qa_db_add_table_prefix('edit_preventer');
			
			if(!in_array($tablename, $tableslc)) {
				return 'CREATE TABLE IF NOT EXISTS `'.$tablename.'` (
				  `postid` int(10) unsigned NOT NULL,
				  `accessed` datetime NOT NULL,
				  `userid` int(10) unsigned DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';

			}
		}
		
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/