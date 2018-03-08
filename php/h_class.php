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
	require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/ClassSchedule.php';
	
	//print_r($_POST);

    switch ($_POST['option']) {
		case "new":
			$response = array('error' => TRUE);
			if(isset($_POST['cname'])) {
				$nome = $_POST['cname'];
				
				$dtini = null;
				if(isset($_POST['cdtinit'])) {
					if($_POST['cdtinit'] == "") {
						$dtini = date("Y-m-d"); ;
					}else {
						$dtini = $_POST['cdtinit'];
					}
				}
				$dtfim = null;
				if(isset($_POST['cdtend'])) {
					if($_POST['cdtend'] == "") {
						$dtfim = NULL;
					}else {
						$dtfim = $_POST['cdtend'];
					}
				}
				$max = 0;
				if(isset($_POST['cmax'])) {
					if($_POST['cmax'] == "") {
						$max =NULL;
					}else {
						$max = $_POST['cmax'];
					}
				}
				$desc = null;
				if(isset($_POST['cdesc'])) {
					if($_POST['cdesc'] == "") {
						$desc = NULL;
					}else {
						$desc = $_POST['cdesc'];
					}
				}
				
				//ciclo dos horarios
				$period = $_POST['cperiod'];
				foreach ($period as $key => $value) {
					$period[$key] = explode(",", $value);
				}
				 
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->insertClass($nome, $dtini, $dtfim, $max, $desc, $period);
				$response['db']=$resdb;
				$response['error'] = $resdb['error'];
				//agenda id...
				if($response['error']==FALSE) agendaId($resdb['id']);
			}
			
			echo json_encode($response);	
			exit();
		break;
		case "edit":
			$response = array('error' => TRUE);
			
			if(isset($_POST['cid']) && isset($_POST['cname'])) {
				$response['post']=$_POST;
				$id = $_POST['cid'];
				$nome = $_POST['cname'];
				
				$dtini = null;//mudar para NOW!!!
				if(isset($_POST['cdtinit'])) {
					if($_POST['cdtinit'] == "") {
						$dtini = date("Y-m-d"); ;
					}else {
						$dtini = $_POST['cdtinit'];
					}
				}
				$dtfim = null;
				if(isset($_POST['cdtend'])) {
					if($_POST['cdtend'] == "") {
						$dtfim = NULL;
					}else {
						$dtfim = $_POST['cdtend'];
					}
				}
				$max = 0;
				if(isset($_POST['cmax'])) {
					if($_POST['cmax'] == "") {
						$max =NULL;
					}else {
						$max = $_POST['cmax'];
					}
				}
				$desc = null;
				if(isset($_POST['cdesc'])) {
					if($_POST['cdesc'] == "") {
						$desc = NULL;
					}else {
						$desc = $_POST['cdesc'];
					}
				}
				
				//schedule cycle
				$period = null;
				if(isset($_POST['cperiod'])) {
					$period = $_POST['cperiod'];
					foreach ($period as $key => $value) {
						$period[$key] = explode(",", $value);
					}
				}
				
				 
				$dbHandler = new DbHandler();
				$resdb = $dbHandler->updateClass($id, $nome, $dtini, $dtfim, $max, $desc, $period);
				$response['db']=$resdb;
				$response['error'] = $resdb['error'];
				//agenda id..
				if($response['error']==FALSE) agendaId($id);
			}

			echo json_encode($response);	
			exit();
			break;
		case "editstate":
			$classid = $_POST['cid'];
			$response = array('error' => TRUE);
			
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->updateClassState($classid);
			$response['db']=$resdb;
			$response['error']=$resdb['error'];
			
			echo json_encode($response);
			break;
		case "delete":
			$classid = $_POST['cid'];
			$response = array('error' => TRUE);
			
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->deleteClass($classid);
			$response['db'] = $resdb;
			$response['error'] = $resdb['error'];
			
			echo json_encode($response);
			break;
		case "selectid":
			$classid = $_POST['cid'];
			
						
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->getClassById($classid);
			
			echo json_encode($resdb);
			break;
		case "selectclasses":
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->getClassList();
			echo json_encode($resdb);
			break;
		case "selectprofs":			
			$tipoval=UserType::getConstKey($_POST['type']);
			
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->getUserList($tipoval, $_POST['name']);
			echo json_encode($resdb);
			break;
		case "deleteperiod":
			$periodid = $_POST['pid'];
			$response = array('error' => TRUE);
			
			$dbHandler = new DbHandler();
			$resdb = $dbHandler->deletePeriod($periodid);
			$response['db'] = $resdb;
			$response['error'] = $resdb['error'];
			
			echo json_encode($response);
			break;
	}
	 
?>