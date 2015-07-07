<?php

	/*
		Author: Ano Tisam
		Email: an0tis@gmail.com
		Website: http://www.whupi.com
		Description: JSON feed for Korero Open Dictionary front-end search
		LICENSE: Free for personal and commercial use licensed under the Creative Commons Attribution 3.0 License, which means you can:

					Use them for personal stuff
					Use them for commercial stuff
					Change them however you like

				... all for free, yo. In exchange, just give the AUthor credit for the program and tell your friends about it :)
	*/
	// Initialize Session data
	if (session_id() == "") session_start(); 
	
	// Turn on output buffering
	ob_start(); 
	
	include_once "manage/ewcfg10.php";
	include_once "manage/ewmysql10.php";
	include_once "db.php";
	include_once "search.php";
  
	$params['fields'] 		= 'id, maori, english, description'; 	//SQL Field Names to display
	$params['column']		= explode(',','maori, english');		//SQL What fields to match against
	$params['sim_field']	= 'maori';								//SQL Jaro Winkler comparison field
	$params['table'] 		= 'words';								//SQL Table Name
	$params['limit'] 		= 30;									//SQL Limit
	
	$query 					= sanitize($_GET['q']);	
	$data 					= get_data($query, $params);
	
	/*Output as JSON*/
	header('Content-Type: application/json');
	echo(json_encode($data));	

?>