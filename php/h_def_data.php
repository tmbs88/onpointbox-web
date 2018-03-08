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
				
			$esett = Settings::readFile();
			$ta = $esett->timeschedule;
			$li = $esett->regInterval;	
			
			//divide em dias, horas, minutos
			$secondsInAMinute = 60;
		    $secondsInAnHour  = 60 * $secondsInAMinute;
		    $secondsInADay    = 24 * $secondsInAnHour;
		
		    // extract days
		    $days = floor($li / $secondsInADay);
		
		    // extract hours
		    $hourSeconds = $li % $secondsInADay;
		    $hours = floor($hourSeconds / $secondsInAnHour);
		
		    // extract minutes
		    $minuteSeconds = $hourSeconds % $secondsInAnHour;
		    $minutes = floor($minuteSeconds / $secondsInAMinute);
		
		    
		
		    // return the final array
		    $obj = array(
		        'd' => (int) $days,
		        'h' => (int) $hours,
		        'm' => (int) $minutes
		    );
			
			
			$response['ta'] = $ta;
			$response['li'] = $obj;
			$response['error'] = FALSE;
			
			
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