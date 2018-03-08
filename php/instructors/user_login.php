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
	
    include_once $_SERVER["DOCUMENT_ROOT"].'/php/include/DbHandler.php';
	
	$response = array('error' => TRUE);
	
	if(isset($_POST['uemail']) && isset($_POST['upass'])) {
		$email = $_POST['uemail'];
		$pass = $_POST['upass'];
		// get hash key from sent password
		$hashedpass = $hash = hash('sha256', $pass);
		// query the database to check id user and password match
		// First check if user already existed in db
		$dbHandler = new DbHandler();
		$resdb = $dbHandler->checkLogin($email, $hashedpass, 2);
		$response['error'] = $resdb['error'];
		$response['db'] = $resdb;
		
		if(!$response['error']) {			
				session_start();
				$_SESSION['uemail']= $email;
				$_SESSION['ulevel']= $resdb['level'];
				session_commit();
				session_write_close();
        }
		
	}
	
	echo json_encode($response);
	exit();
?>