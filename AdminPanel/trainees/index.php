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

session_start();
	if (isset($_SESSION['uemail']) && isset($_SESSION['ulevel'])) {
		
		if($_SESSION['ulevel'] == 1) echo file_get_contents($_SERVER["DOCUMENT_ROOT"].'/AdminPanel/trainees/trainees.html');
		
		else header('location: /AdminPanel/login');

	} else {
		header('location: /AdminPanel/login');
	}
	session_write_close();
	session_commit();
	exit();
?>