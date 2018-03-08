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
	
    require_once $_SERVER["DOCUMENT_ROOT"].'/php/include/Other.php';

    class FileHandler {
 
		private $imgpath;
		private $response;
				   
	    function __construct() {
	        $sett = Settings::readFile();
			$this->imgpath = $_SERVER["DOCUMENT_ROOT"]."/".$sett->imagesFolder;
			$this->response = array("error" => false);
	    }
		
		public function create($tempfile) {
			$imgname = uniqid().".jpg";
			$this->response["tempfile"]= $tempfile;
			$this->response["imgpath"]= $this->imgpath;
			if (!file_exists($this->imgpath)) {
				$this->response["mkdir"] = mkdir($this->imgpath, 0755);
			}
			
			if(!move_uploaded_file($tempfile, $this->imgpath.$imgname)){
				$this->response["error"] = true;
				$this->response["error_message"] = "move_uploaded_file";
			}
			
			$this->response['name']= $imgname;
			
			return $this->response;
		}
		
		public function replace($old, $new) {
			$this->delete($old);
			$this->create($new);
			
			return $this->response;
		}
		
		public function delete($imgname) {
			if(!unlink($this->imgpath.$imgname)){
				$this->response["error"] = false;
				$this->response["error_message"] = "unlink";
			}
			
			return $this->response;
		}
		
    }
?>