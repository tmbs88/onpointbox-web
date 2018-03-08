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

// start session mechanism
session_start();

// unset session variables and destroy session
session_unset();
session_destroy();

// close session mechanism
session_write_close();

// redirect to login page
header('location: '.$_SERVER["DOCUMENT_ROOT"]."");
?>