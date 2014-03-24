<?php

/*
	Question2Answer Plugin: Prevent Simultaneous Edits
	License: http://www.gnu.org/licenses/gpl.html
*/

class qa_prevent_simultaneous_edits_page
{
	private $directory;
	private $urltoroot;

	public function load_module( $directory, $urltoroot )
	{
		$this->directory = $directory;
		$this->urltoroot = $urltoroot;
	}

	public function suggest_requests()
	{
		return array(
			array(
				'title' => qa_lang_html('qa_prevent_sim_edits_lang/page_title'),
				'request' => 'locked_edits',
				'nav' => null,
			),
		);
	}

	public function match_request( $request )
	{
		return $request == 'locked_edits';
	}

	public function process_request( $request )
	{
		$qa_content = qa_content_prepare();
	
		if((bool)qa_opt('pse_active')) {
		
			$jalali_date = (qa_opt('pse_date_type') == "2");

			if($jalali_date) require_once 'jdf.php';

			$qa_content['css_src'][] = $this->urltoroot . 'main.css';
			$qa_content['title'] = qa_lang_html('qa_prevent_sim_edits_lang/page_title');
			
			if(QA_FINAL_EXTERNAL_USERS)
				if((bool)qa_opt('pse_EEU'))
				{
					$sql = 'SELECT EP.postid, UNIX_TIMESTAMP(EP.accessed) AS accessed, U.' . qa_opt('pse_EUTH') . ' AS user, P.type, P.title' .
						' FROM ^edit_preventer AS EP INNER JOIN ^posts AS P ON EP.postid=P.postid' .
						' INNER JOIN `' . qa_opt('pse_EUT') . '` AS U ON EP.userid=U.' . qa_opt('pse_EUTK');
				}
				else
					$sql = 'SELECT EP.postid, UNIX_TIMESTAMP(EP.accessed) AS accessed, EP.userid AS user, P.type. P.title FROM ^edit_preventer AS EP INNER JOIN ^posts AS P ON EP.postid=P.postid';
			else
				$sql = 'SELECT EP.postid, UNIX_TIMESTAMP(EP.accessed) AS accessed, U.handle AS user, P.type, P.title FROM ^edit_preventer AS EP INNER JOIN ^posts AS P ON EP.postid=P.postid
					INNER JOIN ^users AS U ON EP.userid=U.userid';
			$locked_posts = qa_db_read_all_assoc(qa_db_query_sub($sql));

			$locked_table = '<table class="locked-posts"><tr><th>' . qa_lang_html('qa_prevent_sim_edits_lang/post') .
				'</th><th>' . qa_lang_html('qa_prevent_sim_edits_lang/post_type') .
				'</th><th>' . qa_lang_html('qa_prevent_sim_edits_lang/lock_date') .
				'</th><th>' . qa_lang_html('qa_prevent_sim_edits_lang/user') . '</th></tr>';
			foreach($locked_posts as $post)
			{
				$locked_table .= '<tr>';
				$locked_table .= '<td><a href="' . qa_path_html(qa_q_request($post['postid'], $post['title']), null, qa_opt('site_url')) . '">' . $post['title'] . '</a></td>';
				switch($post['type'])
				{
					case 'Q':
						$locked_table .= '<td>' . qa_lang_html('qa_prevent_sim_edits_lang/question') . '</td>';
					break;
					case 'A':
						$locked_table .= '<td>' . qa_lang_html('qa_prevent_sim_edits_lang/answer') . '</td>';
					break;
					case 'C':
						$locked_table .= '<td>' . qa_lang_html('qa_prevent_sim_edits_lang/comment') . '</td>';
					break;
					default:
						$locked_table .= '<td>' . qa_lang_html('qa_prevent_sim_edits_lang/unknown') . '</td>';
					break;
				}
				if($jalali_date)
					$locked_table .= '<td>' . jdate('l j F [H:i:s]', $post['accessed']) . '</td>';
				else
					$locked_table .= '<td>' . date('l j F [H:i:s]', $post['accessed']) . '</td>';
				if(QA_FINAL_EXTERNAL_USERS)
					$locked_table .= '<td>' . $post['user'] . '</td>';
				else
					$locked_table .= '<td><a href="' . qa_path_html('user/'.$post['user'], null, qa_opt('site_url')) . '">' . $post['user'] . '</a></td>';
				$locked_table .= '</tr>';
			}
			$locked_table .= '</table>';
			
			$qa_content['custom'] = $locked_table;
		}
		else {
			$qa_content['error'] = qa_lang_html('qa_prevent_sim_edits_lang/deactivated');
		}
		
		return $qa_content;
	}

}
