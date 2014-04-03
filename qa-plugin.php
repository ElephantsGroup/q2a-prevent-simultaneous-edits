<?php

/*
	Plugin Name: Prevent Simultaneous Edits
	Plugin URI: https://github.com/ElephantsGroup/q2a-prevent-simultaneous-edits
	Plugin Description: Prevents simultaneous post edits by your users
	Plugin Version: 1.3.0
	Plugin Date: 2014-04-01
	Plugin Author: echteinfachtv
	Plugin Author URI: http://www.echteinfach.tv
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: https://raw.github.com/ElephantsGroup/q2a-prevent-simultaneous-edits/master/qa-plugin.php
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	// event
	qa_register_plugin_module('event', 'qa-prevent-simultaneous-edits-event.php', 'qa_prevent_simultaneous_edits_event', 'prevent simultaneous edits event');

	// admin
	qa_register_plugin_module('module', 'qa-prevent-simultaneous-edits-admin.php', 'qa_prevent_simultaneous_edits_admin', 'prevent simultaneous edits admin');

	// layer that inserts javascript alert
	qa_register_plugin_layer('qa-prevent-simultaneous-edits-layer.php', 'Simultaneous Edits Preventer Layer');
	
	// language file
	qa_register_plugin_phrases('lang/qa-prevent-simultaneous-edits-lang-*.php', 'qa_prevent_sim_edits_lang');

	// current edits page
	qa_register_plugin_module('page', 'qa-prevent-simultaneous-edits-page.php', 'qa_prevent_simultaneous_edits_page', 'Locked edits page');
	
	// override file
	qa_register_plugin_overrides('qa-prevent-simultaneous-edits-overrides.php');
/*
	Omit PHP closing tag to help avoid accidental output
*/