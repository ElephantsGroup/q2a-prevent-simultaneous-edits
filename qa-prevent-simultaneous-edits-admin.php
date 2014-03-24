<?php

class qa_prevent_simultaneous_edits_admin
{
	private $optactive = 'pse_active';
	private $date_type = 'pse_date_type';
	private $lock_time = 'pse_lock_time';
	private $ignore_time = 'pse_ignore_time';
	private $enabled_external_users = 'pse_EEU';
	private $external_users_table = 'pse_EUT';
	private $external_users_table_key = 'pse_EUTK';
	private $external_users_table_handle = 'pse_EUTH';

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
			case 'enabled_external_users':
				return 0;
			case 'external_users_table':
				return '';
			case 'external_users_table_key':
				return '';
			case 'external_users_table_handle':
				return '';
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
				if ( qa_post_text('enabled_external_users') ) qa_opt( $this->enabled_external_users, '1' );
				else qa_opt( $this->enabled_external_users, '0' );
				qa_opt( $this->external_users_table, qa_post_text('external_users_table') );
				qa_opt( $this->external_users_table_key, qa_post_text('external_users_table_key') );
				qa_opt( $this->external_users_table_handle, qa_post_text('external_users_table_handle') );
			}
		}
		if ( qa_clicked('pse_reset') )
		{
			qa_opt($this->date_type, $this->option_default('date_type'));
			qa_opt($this->lock_time, $this->option_default('lock_time'));
			qa_opt($this->ignore_time, $this->option_default('ignore_time'));
			qa_opt($this->enabled_external_users, $this->option_default('enabled_external_users'));
			qa_opt($this->external_users_table, $this->option_default('external_users_table'));
			qa_opt($this->external_users_table_key, $this->option_default('external_users_table_key'));
			qa_opt($this->external_users_table_handle, $this->option_default('external_users_table_handle'));
		}
		
		$pse_active = qa_opt($this->optactive);
		$date_type = qa_opt($this->date_type);
		$lock_time = qa_opt($this->lock_time);
		$ignore_time = qa_opt($this->ignore_time);
		$enabled_external_users = qa_opt($this->enabled_external_users);
		$external_users_table = qa_opt($this->external_users_table);
		$external_users_table_key = qa_opt($this->external_users_table_key);
		$external_users_table_handle = qa_opt($this->external_users_table_handle);

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
				array(
					'type' => 'checkbox',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/enabled_external_users'),
					'tags' => 'NAME="enabled_external_users"',
					'value' => $enabled_external_users === '1',
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/external_users_table'),
					'tags' => 'NAME="external_users_table"',
					'value' => $external_users_table,
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/external_users_table_key'),
					'tags' => 'NAME="external_users_table_key"',
					'value' => $external_users_table_key,
				),
				array(
					'type' => 'text',
					'label' => qa_lang_html('qa_prevent_sim_edits_lang/external_users_table_handle'),
					'tags' => 'NAME="external_users_table_handle"',
					'value' => $external_users_table_handle,
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
		
		$table = $_POST['external_users_table'];
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
			
		return $ret;
	}

}