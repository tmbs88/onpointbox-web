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
	
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/DbHandler.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/Other.php';
	
	$response = array('error' => TRUE);
	
	if(isset($_POST['uemail']) && isset($_POST['upass'])) {
		$email = $_POST['uemail'];
		$pass = $_POST['upass'];
		// get hash key from sent password
		$hashedpass = $hash = hash('sha256', $pass);
		// query the database to check id user and password match
		// First check if user already existed in db
		$dbHandler = new DbHandler();
		$resdb = $dbHandler->checkLogin($email, $hashedpass, 3);
		$response['error'] = $resdb['error'];
		$response['db'] = $resdb;
		
		if(!$response['error']) {
			//There were no authentication problems.
				//will fetch user data
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->getUserByEmailMobile($email);
			
			
			$esett = Settings::readFile();
			$esett = $esett->imagesFolder;	
			//add default path for NULL cases
			if($resdb['image'] == NULL) $resdb['image'] = "default.gif";
			
			$resdb['imagefolder'] = trim($esett, "/");
			//$resdb['image'] = $esett.$resdb['image'];
			$response['data'] = $resdb;
        }
		
	}
	
	echo json_encode($response);
	exit();
?>