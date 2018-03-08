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

	$sett = Settings::readFile();
	$sett = $sett->database;

	define('DB_USERNAME', $sett->DB_USERNAME);
	define('DB_PASSWORD', $sett->DB_PASSWORD);
	define('DB_HOST', $sett->DB_HOST);
	define('DB_NAME', $sett->DB_NAME);
	 
	define('USER_CREATED_SUCCESSFULLY', 0);
	define('USER_CREATE_FAILED', 1);
	define('USER_ALREADY_EXISTED', 2);
	
	define('USER_TYPE_ADMIN', 1);
	define('USER_TYPE_PROF', 2);
	define('USER_TYPE_ALUNO', 3);
	
	define('USER_LOGIN_SUCCESS', 0);
	define('USER_LOGIN_PASS_FAIL', 1);	
	define('USER_LOGIN_EXIST_FAIL', 2);
	
	define('USER_PASS_UPDATE_SUCCESS', 0);
	define('USER_PASS_UPDATE_FAIL', 1);

	define('STATE_UP_SUCCESS', 0);
	define('STATE_UP_FAIL_UPDATE', 1);	
	define('STATE_UP_FAIL_SELECT', 2);
	

	class DbConnect {
 
	    private $conn;
	 
	    function __construct() {        
	    }
	 
	    /**
	     * Establishing database connection
	     * @return database connection handler
	     */
	    function connect() {
	 
	        // Connecting to mysql database
	        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	 
	        // Check for database connection error
	        if (mysqli_connect_errno()) {
	            echo "Failed to connect to MySQL: " . mysqli_connect_error();
	        }
	 
	        // returing connection resource
	        return $this->conn;
	    }
	 
	}
	
?>