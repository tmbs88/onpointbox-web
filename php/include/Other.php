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

class Settings{
//		const emailServer='baco.serverlion.org';
//		const emailPort='465';
//		const emailEnc='ssl';
//		const emailUsername='p4@ts7361.lusoaloja.com';
//		const emailPassword='p4admin"';
//		const imagesfolder='/uimages';
		
		public static function readFile(){
			$filedir = $_SERVER["DOCUMENT_ROOT"]."/settings.json";
			$file = json_decode(file_get_contents($filedir));
			return $file;
		}

		public static function writeFile($f){
			$file = $_SERVER["DOCUMENT_ROOT"]."/settings.json";
			//$oldfile = $file.$_SERVER['REQUEST_TIME'];
			//if(copy($file, $oldfile)){
				file_put_contents($file, json_encode($f, TRUE));
			//}
			
			
			//$json = json_decode(file_get_contents($file));
			//print_r($json);
		}
		
	}

	class UserType{
	
		const Admin = 1;
	    const Instructor = 2;
	    const Trainee = 3;
		
		static function getConstList(){
			$array = array( UserType::Admin => 'Administrador',
	              			UserType::Instructor => 'Instructor',
	              			UserType::Trainee => 'Trainee'
	            			);

			return $array;
		}
		
		static function getConstVal($var){
			$array = UserType::getConstList();

			return $array[$var];
		}

		static function getConstKey($var){
			$array = UserType::getConstList();

			return array_search($var, $array);
		}
		
	}
	
	class WeekDays{
		const Monday = 1;
		const Tuesday = 2;
		const Wednesday = 3;
		const Thursday = 4;
		const Friday = 5;
		const Saturday = 6;
		const Sunday = 7;
		
		static function getConstList(){
			$array = array( Weekdays::Monday => 'Monday',
							Weekdays::Tuesday => 'Tuesday',
							Weekdays::Wednesday => 'Wednesday',
							Weekdays::Thursday => 'Thursday',
							Weekdays::Friday => 'Friday',
							Weekdays::Saturday => 'Saturday',
							Weekdays::Sunday => 'Sunday'
	            			);

			return $array;
		}
		
		static function getConstVal($var){
			$array = WeekDays::getConstList();

			return $array[$var];
		}

		static function getConstKey($var){
			$array = Weekdays::getConstList();

			return array_search($var, $array);
		}
	}
	
	
?>