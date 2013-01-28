<?php

/*
	Plugin Name: Prevent Simultaneous Edits
	Plugin URI: https://github.com/echteinfachtv/q2a-prevent-simultaneous-edits
	Plugin Description: Prevents simultaneous post edits by your users
	Plugin Version: 0.1
	Plugin Date: 2013-01-28
	Plugin Author: echteinfachtv
	Plugin Author URI: http://www.echteinfach.tv
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.5
	Plugin Update Check URI: https://raw.github.com/echteinfachtv/q2a-prevent-simultaneous-edits/master/qa-plugin.php
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}

	// layer that inserts javascript alert
	qa_register_plugin_layer('qa-prevent-simultaneous-edits-layer.php', 'Simultaneous Edits Preventer Layer');
	
	// language file
	qa_register_plugin_phrases('qa-prevent-simultaneous-edits-lang.php', 'qa_prevent_sim_edits_lang');

	// admin module, only used to create database table
	qa_register_plugin_module('module', 'qa-prevent-simultaneous-edits-module.php', 'qa_prevent_simultaneous_edits_module', 'Create Table for Simultaneous Edits');
	
	
/*
	Omit PHP closing tag to help avoid accidental output
*/