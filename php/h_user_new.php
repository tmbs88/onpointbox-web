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
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/FileHandler.php';

	$response = array('error' => TRUE);
	
	if(isset($_POST['uname']) && isset($_POST['uemail']) && isset($_POST['utipo'])) {
		
		$nome = $_POST['uname'];
		
		$email = $_POST['uemail'];
		$tel = null;
		$tipo = $_POST['utipo'];
		
		$dtnasc = null;
		if(isset($_POST['udtborn'])) {
			if($_POST['udtborn'] != "") {
				$dtnasc = $_POST['udtborn'];
			}
		}
		$sexo = null;
		if(isset($_POST['ugender'])) {
			if($_POST['ugender'] != "") {
				$sexo = $_POST['ugender'];
			}
		}
		
		if(isset($_POST['uphone'])) {
			if($_POST['uphone'] != "") {
				$tel = $_POST['uphone'];
			}
		}
		
		$pass = uniqid();
		$hashedpass = $hash = hash('sha256', $pass);

		$tipoval=UserType::getConstKey($tipo);
		
		if(isset($_FILES["fileImport"]) && $_FILES["fileImport"]["error"] == 0) {													
			
			$fileHandler = new FileHandler();
			
			
			$resfile = $fileHandler->create($_FILES["fileImport"]["tmp_name"]);
			$response['file']=$resfile;
			if(!$resfile["error"]) {
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->insertUser($nome, $email, $hashedpass, $tel, $tipoval, $dtnasc, $sexo, $resfile['name']);
				$response['db']=$resdb;
				if($resdb['error']) $resfile = $fileHandler->delete($resfile['name']);
			}
			if($resfile['error'] || $resdb['error']) $response['error']=TRUE;
			else $response['error']=FALSE;
			
		}else{
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->insertUser($nome, $email, $hashedpass, $tel, $tipoval, $dtnasc, $sexo);
			$response['error'] = $resdb['error'];
			$response['db'] = $resdb;
		}
		
		if(!$response['error']){
			$sett = Settings::readFile();
			$resmail = send_email("Password ".$sett->name,
								array($sett->email->username => $sett->name),
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
		
	}
		
	echo json_encode($response);	
	exit();
?>