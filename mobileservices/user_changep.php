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

ini_set('error_reporting', E_ALL);
	
	//include_once dirname(__FILE__) . './include/DbHandler.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/DbHandler.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/Other.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/EmailSender.php';

	$response = array('error' => TRUE);
	
	if(isset($_POST['uemail']) && isset($_POST['uoldp']) && isset($_POST['unewp'])) {
					
		$email = $_POST['uemail'];
		$old = hash('sha256', $_POST['uoldp']);
		$new = hash('sha256', $_POST['unewp']);

		$dbHandler = new DbHandler();
		$user = $dbHandler->getUserByEmail($email);

		if ($user['id'] != null) {
				
			$resdb = $dbHandler->checkUserPass($email, $old);
			if(!$resdb['error']) {
				
				$resdb = $dbHandler->updateUserPass($user['id'], $new);
			}
			
			$response = $resdb;
		}else {
			
			$response["error_message"] = "Email does not exist.";
		} 
		echo json_encode($response);	
	}
	
	exit();
?>