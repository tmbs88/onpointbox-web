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
	
    require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/DbConnect.php';
	require_once ($_SERVER["DOCUMENT_ROOT"].'/dhtmlxScheduler_v4/connector/scheduler_connector.php');
	require_once ($_SERVER["DOCUMENT_ROOT"].'/dhtmlxScheduler_v4/connector/db_mysqli.php');
 
	$db = new DbConnect();
	 
	$calendar = new schedulerConnector($db->connect(),"MySQLi");//connector initialization
	$calendar->render_table("aula","id","event_start,event_end,text");
?>