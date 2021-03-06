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
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/EmailSender.php';
	
	session_start();
	
	$response = array('error' => TRUE, 'session' => FALSE);
	if (isset($_SESSION['uemail']) && isset($_SESSION['ulevel'])) {
			
		if($_SESSION['ulevel'] >= 2) {
				
			if(isset($_POST['uoldp']) && isset($_POST['unewp'])) {
				
				
				$email = $_SESSION['uemail'];
				$old = hash('sha256', $_POST['uoldp']);
				$new = hash('sha256', $_POST['unewp']);
				
				$response['old']=$_POST['uoldp'];
				$response['new']=$_POST['unewp'];
				$response['oldh']=$old;
				$response['newh']=$new;
				
				$dbHandler = new DbHandler();
				$user = $dbHandler->getUserByEmail($email);
		
				if ($user['id'] != null) {
						
					$resdb = $dbHandler->checkUserPass($email, $old);
					$response['error'] = $resdb['error'];
					if($resdb['error']) $response['error_message'] = $resdb['error_message'];
					else {
						$resdb = $dbHandler->updateUserPass($user['id'], $new);
						$response['error'] = $resdb['error'];
						if($resdb['error']) $response['error_message'] = $resdb['error_message'];
					}
				}else {
					$response['error'] = TRUE;
					$response["error_message"] = "Email does not exist.";
				} 
				
				
			} else {
				$response['error'] = TRUE;
				$response['error_message'] = "Enter the required data.";
			}
			
		}else {
			$response['error'] = TRUE;
			$response['error_message'] = "You do not have permission to register.";
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