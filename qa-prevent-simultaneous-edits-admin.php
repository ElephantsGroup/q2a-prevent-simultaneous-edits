<?php

class qa_prevent_simultaneous_edits_admin
{
	private $optactive = 'pse_active';
	private $date_type = 'pse_date_type';
	private $lock_time = 'pse_lock_time';
	private $ignore_time = 'pse_ignore_time';

	function init_queries($tableslc)
	{
		$tablename=qa_db_add_table_prefix('edit_preventer');
		
		if(!in_array($tablename, $tableslc)) {
			return 'CREATE TABLE IF NOT EXISTS `'.$tablename.'` (
			  `postid` int(10) unsigned NOT NULL,
			  `accessed` datetime NOT NULL,
			  `userid` int(10) unsigned DEFAULT NULL
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		}
	}

	function option_default($option) {

		switch($option) {
			case 'date_type':
				return 1;
			case 'lock_time':
				return 120;
			case 'ignore_time':
				return 300;
			default:
				return null;
		}
		
	}

	function admin_form( &$qa_content )
	{
		$saved_msg = '';
		$date_types = array( 1 => qa_lang_html('qa_prevent_sim_edits_lang/admin_georgian'), 2 => qa_lang_html('qa_prevent_sim_edits_lang/admin_jalali'));
		
		if ( qa_clicked('pse_save') )
		{
			if( !$this->validate_data() )
			{
				$saved_msg = qa_lang_html('qa_prevent_sim_edits_lang/incorrect_entry');
			}
			else
			{
				if ( qa_post_text('pse_active') )
				{
					$sql = 'SHOW TABLES LIKE "^edit_preventer"';
					$result = qa_db_query_sub($sql);
					$rows = qa_db_read_all_assoc($result);
					if ( count($rows) > 0 )
					{
						qa_opt( $this->optactive, '1' );
					}
					else
					{
						$error = array(
							'type' => 'custom',
							'error' => qa_lang_html('qa_prevent_sim_edits_lang/admin_notable') . '<a href="' . qa_path('install') . '">' . qa_lang_html('qa_prevent_sim_edits_lang/admin_create_table') . '</a>',
						);
					}

					$saved_msg = qa_lang_html('admin/options_saved');
				}
				else
					qa_opt( $this->optactive, '0' );
				qa_opt( $this->date_type, (int)qa_post_text('date_type') );
				qa_opt( $this->lock_time, (int)qa_post_text('lock_time') );
				qa_opt( $this->ignore_time, (int)qa_post_text('ignore_time') );
			}
		}
		if ( qa_clicked('pse_reset') )
		{
			qa_opt($this->date_type, $this->option_default('date_type'));
			qa_opt($this->lock_time, $this->option_default('lock_time'));
			qa_opt($this->ignore_time, $this->option_default('ignore_time'));
		}
		
		$pse_active = qa_opt($this->optactive);
		$date_type = qa_opt($this->date_type);
		$lock_time = qa_opt($this->lock_time);
		$ignore_time = qa_opt($this->ignore_time);

		$form = array(
			'ok' => $saved_msg,

			'fields' => array(
				array(
					'type' => 'checkbox',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/admin_active'),
					'tags' => 'NAME="pse_active"',
					'value' => $pse_active === '1',
					'note' => qa_lang_html('qa_prevent_sim_edits_lang/admin_active_note'),
				),
				array(
					'type' => 'select',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/admin_date_type'),
					'tags' => 'NAME="date_type"',
					'value' =>  @$date_types[$date_type],
					'options' => $date_types,
				),
				array(
					'type' => 'number',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/lock_time'),
					'tags' => 'NAME="lock_time"',
					'value' => $lock_time,
					'suffix' => qa_lang_html('qa_prevent_sim_edits_lang/seconds'),
					'note' => qa_lang_html('qa_prevent_sim_edits_lang/lock_time_note'),
				),
				array(
					'type' => 'number',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/ignore_time'),
					'tags' => 'NAME="ignore_time"',
					'value' => $ignore_time,
					'suffix' => qa_lang_html('qa_prevent_sim_edits_lang/seconds'),
					'note' => qa_lang_html('qa_prevent_sim_edits_lang/ignore_time_note'),
				),
			),
			'buttons' => array(
				array(
					'label' => qa_lang_html('admin/save_options_button'),
					'tags' => 'name="pse_save"',
				),
				array(
					'label' => qa_lang_html('admin/reset_options_button'),
					'tags' => 'name="pse_reset"',
				),
			)
		);
		
		return $form;
	}

	private function validate_data()
	{
		$ret = true;
		
		/*$table = $_POST['external_users_table'];
		$table_key = $_POST['external_users_table_key'];
		$table_handle = $_POST['external_users_table_handle'];
	
		// check if table exists
		$sql = "SHOW TABLES LIKE '$table'";
		$result = qa_db_query_sub($sql);
		$rows = qa_db_read_all_assoc($result);
		if (count($rows) == 0)
			$ret = false;
		else
		{
			// check if id column exists
			$sql = "SHOW COLUMNS FROM `$table` LIKE '$table_key'";
			$result = qa_db_query_sub($sql);
			$rows = qa_db_read_all_assoc($result);
			if (count($rows) == 0)
				$ret = false;
				
			// check if id column exists
			$sql = "SHOW COLUMNS FROM `$table` LIKE '$table_handle'";
			$result = qa_db_query_sub($sql);
			$rows = qa_db_read_all_assoc($result);
			if (count($rows) == 0)
				$ret = false;
		}
			
		// check excluded users
		$userids = explode(',', $_POST['excluded_users']);
		foreach($userids as $id)
			if($id and !is_numeric(trim($id)))
			{
				$ret = false;
				break;
			}*/
		return $ret;
	}

}