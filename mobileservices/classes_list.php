<?php

/**
 * OnPointBox a schedule manager for group classes
 * Copyright (C) 2015  Tiago Miguel Basilio da Silva
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

ini_set( 'error_reporting', E_ALL);
	
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/DbHandler.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/Other.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/FileHandler.php';
	
	//print_r($_POST);
	$response = array('error' => TRUE);
	
	if(isset($_POST['uid'])) {
    
		header('Content-type: application/json'); 

		$esett = Settings::readFile();
		$esett = $esett->regInterval;	
		
		$dbHandler = new DbHandler();
		$resdb = $dbHandler->selectRegEventsUser($esett, $_POST['uid'], true);
		$response['error'] = $resdb['error'];
		/*
		if(!$response['error']){
			foreach ($resdb['rows'] as &$value) {
				if($value['regid'] != NULL) {
					$value['name'] .= " - INSCRITO";
				}
			}
		} 
		 * 
		 */
		$response['db'] = $resdb;
		
		
		echo json_encode($response);
	}
?>