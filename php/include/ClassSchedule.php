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
	
	//agendaCron();
	
	function agendaCron(){
		
		$dbHandler = new DbHandler();
		$resdb = $dbHandler->getClassListId(1);
		//print_r($resdb);
		
		
		if($resdb != NULL) {
			foreach ($resdb as $value) {
				agendaId($value);
			}
		}
	}
	

	function agendaId($cid){
		$sett = Settings::readFile();
		$length = $sett->timeschedule;
		
		$dbHandler = new DbHandler();
		$resdb = $dbHandler->getClassById($cid);
		//print_r($resdb);
		
		$START = new DateTime($resdb['dtini']);
		$FINISH = new DateTime($resdb['dtfim']);
		
		$carray = $resdb['period'];
		//print_r("/n --->");
		//print_r($cid);
		//var_dump($carray);
		//print_r("<--- /n");
		if($resdb['period'] != "") {
		//print_r($carray);

			//calcula dia de hoje e ultimo dia.
			$NOW = new DateTime(date("Y-m-d"));
			//$NOW = new DateTime("2015-04-16");
			//$minus = $length;//-$NOW->format('N');
			$di = 'P'.$length.'D';
			
			$date = new DateTime(date("Y-m-d"));
			$PLUS = $date->add(new DateInterval($di));
			//mudar algoritmo se NOW for domingo(==WE) !
			
			//print_r($NOW);
			//print_r($PLUS7);
			
			
			
			if($resdb['dtfim']=="" || $FINISH > $PLUS) $FINISH = $PLUS;
			if(($NOW < $START && $PLUS < $FINISH) || $NOW > $FINISH){
				//nao agenda
				//print_r("nao agenda");
			}
			else{
				if ($NOW > $START) $START = $NOW;
				//print_r($START);
				//print_r($FINISH);
				
				
				$array_dias = array();
				
				
				//DIVIDIR SEMANAS
				
				$ITERATOR = new DateTime($START->format("Y-m-d"));
				while ($ITERATOR <= $FINISH) {
					$array_dia = array('day' => $ITERATOR->format("Y-m-d"), 'dayn' => $ITERATOR->format('N'));
					
					$array_dias[] = $array_dia;//$ITERATOR->format("Y-m-d");
					
					$ITERATOR->add(new DateInterval('P1D'));
					
					//print_r("  ->".$ITERATOR->format("Y-m-d")."==".$ITERATOR->format('N'));
				}
				
				//print_r($array_dias);
				 
				$array_final = array();
				//procurar classes para os dias do array
				foreach ($array_dias as $key1 => $value1) {
					
					foreach ($carray as $key2 => $value2) {
							
						if ($value1['dayn']==$value2['dayn']) {
							
							$array_class = array('data' => $value1['day'],
												'hour' => $value2['hour'],
												'duration' => $value2['duration'],
												'id_tipo' => $resdb['id'], 
												'id_period' => $value2['id']);
												
							defineDateTime($array_class);
							
							$array_final[] = $array_class;
						}
					}
				}
				
				
				//compara com os já existentes entre o start e finish
				removeExtra($START, $FINISH, $resdb['id'], $array_final);
				
				//print_r($array_final);
				//adiciona o que restar
				insertInBd($array_final);
			}
		
		//print_r($NOW);
		//print_r($minus);
		//print_r($WE);
		}
		
	}

	function removeExtra($start, $finish, $id_tipoaula, &$arr_final) {
		$dbHandler = new DbHandler();
		$finish->setTime(23, 59);
		$array = $dbHandler->selectEvent($start->format("Y-m-d H:i:s"), 
										$finish->format("Y-m-d H:i:s"),
										$id_tipoaula);

		if($array != NULL){
			foreach ($array as $key1 => $value1) {
			
				foreach ($arr_final as $key2 => $value2) {
					$pieces = explode(" ", $value1['event_start']);
					if($value1['id_periodaula'] == $value2['id_period'] && $pieces[0] == $value2['data'] )  unset($arr_final[$key2]);
				}
			}
		}
		
	}
	
	function insertInBd(&$array) {
		//print_r(" entra na bd ");
		//print_r($array);
		$dbHandler = new DbHandler();
		foreach ($array as $key => $value) {
			
			$dbHandler->insertEvent($value['start_date'], $value['end_date'], $value['id_tipo'], $value['id_period']);
		}
	}

	function defineDateTime(&$array) {

		$event_start = new DateTime($array['data']);
		
		$pieces = explode(":", $array['hour']);
		$event_start->setTime($pieces[0], $pieces[1]);
		
		$array['start_date'] = $event_start->format("Y-m-d H:i");
		
		$event_end = new DateTime($event_start->format("Y-m-d H:i"));
		
		$pieces = explode(":", $array['duration']);
		$event_end->add(new DateInterval('PT'.$pieces[0].'H'.$pieces[1].'M'));
		
		$array['end_date'] = $event_end->format("Y-m-d H:i");
	}
	
	//agendaSemana();
	//agendaId($_POST['cid'], 7);
?>