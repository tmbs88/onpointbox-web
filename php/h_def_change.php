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
	
	session_start();
	
	$response = array('error' => TRUE, 'session' => FALSE);
	if (isset($_SESSION['uemail']) && isset($_SESSION['ulevel'])) {
			
		if($_SESSION['ulevel'] == 1) {
				
			if(isset($_POST['tad'])){
				//edita apenas o timeschedule
				$esett = Settings::readFile();
				$esett->timeschedule = $_POST['tad'] * 1;
				Settings::writeFile($esett);
				
				$response['error'] = FALSE;
			}else if(isset($_POST['lid']) && isset($_POST['lih']) && isset($_POST['lim'])){
				//edita apenas o reginterval
				$daysToHours = $_POST['lid'] * 24;
				$hoursToMinutes = ($daysToHours + $_POST['lih']) * 60;
				$minutesToSeconds = ($hoursToMinutes + $_POST['lim']) * 60;
				
				$esett = Settings::readFile();
				$esett->regInterval = $minutesToSeconds;
				Settings::writeFile($esett);
				
				$response['error'] = FALSE;
			}else {
				$response['error'] = TRUE;
				$response['error_message'] = "Data not changed, please try again.";
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