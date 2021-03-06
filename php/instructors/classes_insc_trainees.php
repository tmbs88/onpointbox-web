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
	
	session_start();
	
	$response = array('error' => TRUE, 'session' => FALSE);
	if (isset($_SESSION['uemail']) && isset($_SESSION['ulevel'])) {
	
		if($_SESSION['ulevel'] <= 2) {
			
			if(isset($_POST['cid'])) {
				$esett = Settings::readFile();
				$esett = $esett->imagesFolder;
				$tipoval=UserType::getConstKey('Trainee');
				
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->getUserList2($tipoval, $_POST['cid']);
				//adicionar caminho default pra casos de NULL
				foreach ($resdb['rows'] as $key => &$value) {
					if($resdb['rows'][$key]['img'] == NULL) $resdb['rows'][$key]['img'] = "default.gif";
					$resdb['rows'][$key]['img'] = $esett.$resdb['rows'][$key]['img'];
				}
				
					
				$response['error'] = $resdb['error'];
				$response['db'] = $resdb;
				if($resdb['error']) $response['error_message'] = $resdb['error_message'];
				
			}else {
				$response['error'] = TRUE;
				$response['error_message'] = "Missing class data";
			}
		}else {
			$response['error'] = TRUE;
			$response['error_message'] = "You do not have permissions.";
		}
		
		$response['session'] = TRUE;
	} else {
		$response['error'] = TRUE;
		$response['error_message'] = "";
	}
	echo json_encode($response);
	
	session_write_close();
	session_commit();
	exit();
?>