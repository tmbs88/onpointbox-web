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
	
	if(isset($_POST['uemail'])) {		
		$email = $_POST['uemail'];

		$dbHandler = new DbHandler();
		$user = $dbHandler->getUserByEmail($email);

		if ($user['id'] != null) {			
			$pass = uniqid();
			$hashedpass = $hash = hash('sha256', $pass);
			
			$resdb = $dbHandler->updateUserPass($user['id'], $hashedpass);
			$response['error'] = $resdb['error'];
			$response['db'] = $resdb;
			
			if(!$response['error']) {
				$sett = Settings::readFile();
				$sett = $sett->name;
				$resmail = send_email("Password $sett",
								array('p4@ts7361.lusoaloja.com' => $sett), //TODO: add a env variable here
								array($email => $user['name']),
					  			'<p>Your login information is:</p>'.
					  			'<p>Username: '.$email.'</p>'.
					  			'<p>New Password: '.$pass.'</p>');
				if($resmail == 0) {
					$response['error'] = true;
					$response["error_message"] = "Email not sent.";
				} else if ($resmail == 1) {
					$response['error'] = false;
					$response["error_message"] = "Email sent.";
				}
			} 
			
		}else {
			$response["error_message"] = "Email does not exist.";
		} 
		echo json_encode($response);	
	}
	
		
	exit();
?>