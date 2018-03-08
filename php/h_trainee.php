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

    switch ($_POST['option']) {
			case "edit":
				
				$nome = $_POST['uname'];
				$email = $_POST['uemail'];
				$oldemail = $_POST['oldemail'];
				$tel = null;
				$response = array('error' => TRUE);
				
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
				
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->getUserByEmail($oldemail);
				
				if(isset($_FILES["fileImport"]) && $_FILES["fileImport"]["error"] == 0) {													
					
					$fileHandler = new FileHandler();
					
					if($resdb['foto'] == NULL) {
						$resfile = $fileHandler->create($_FILES["fileImport"]["tmp_name"]);
						$response['file']=$resfile;
						if(!$resfile["error"]) {
							$dbHandler = new DbHandler();
							$resdb = $dbHandler->updateUser($resdb['id'], $nome, $email, $tel, $dtnasc, $sexo, $resfile['name']);
							$response['db']=$resdb;
							if($resdb['error']) $resfile = $fileHandler->delete($resfile['name']);
						}
						if($resfile['error'] || $resdb['error']) $response['error']=TRUE;
					}else{
						$resfile = $fileHandler->replace($resdb['foto'], $_FILES["fileImport"]["tmp_name"]);
						$response['file']=$resfile;
						if(!$resfile["error"]) {
							$dbHandler = new DbHandler();
							$resdb = $dbHandler->updateUser($resdb['id'], $nome, $email, $tel, $dtnasc, $sexo, $resfile['name']);
							$response['db']=$resdb;
							if($resdb['error']) $resfile = $fileHandler->delete($resfile['name']);
						}
						if(!$resfile['error'] && !$resdb['error']) $response['error']=FALSE;
					}
				}else{
					$dbHandler = new DbHandler();
					$resdb = $dbHandler->updateUser($resdb['id'], $nome, $email, $tel, $dtnasc, $sexo, $resdb['foto']);
					$response['error'] = $resdb['error'];
					$response['db']=$resdb;
				}
				
				echo json_encode($response);
				break;
			case "editstate":
				$email = $_POST['uemail'];
				$response = array('error' => TRUE);
				
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->getUserByEmail($email);
				$resdb = $dbHandler->updateUserState($resdb['id']);
				$response['db']=$resdb;
				$response['error']=$resdb['error'];
				
				echo json_encode($response);
				break;
			case "editpass":
				break;
			case "editfoto":
				break;
			case "delete":
				$usermail = $_POST['uemail'];
				$response = array('error' => TRUE);
				$resfile = array('error' => FALSE);
				
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->getUserByEmail($usermail);
				if($resdb['foto'] != NULL) {
					$fileHandler = new FileHandler();
					$resfile = $fileHandler->delete($resdb['foto']);
					$response['file']=$resfile;
				}
				$resdb = $dbHandler->deleteUser($resdb['id']);
				$response['db']=$resdb;
				if(!$resfile['error'] && !$resdb['error']) $response['error']=FALSE;
				
				echo json_encode($response);
				break;
			case "selectid":
				$userid = $_POST['uid'];
				
				$esett = Settings::readFile();
				$esett = $esett->imagesFolder;
				
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->getUserById($userid);
				
				//add default path for NULL cases
				if($resdb['img'] == NULL) $resdb['img'] = "default.gif";
				$resdb['img'] = $esett.$resdb['img'];
				echo json_encode($resdb);
				break;
			case "selectlist":
				$selector = substr($_POST['radio'], 1);
				
				$tipoval=UserType::getConstKey($_POST['type']);
				
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->getUserList($tipoval, $selector);
				echo json_encode($resdb);
				break;
	}
	 
?>