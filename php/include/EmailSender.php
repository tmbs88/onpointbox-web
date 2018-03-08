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
	require_once $_SERVER["DOCUMENT_ROOT"].'/vendor/autoload.php';
	
	
	function send_email($subject, $from, $to, $body){
		
		$esett = Settings::readFile();
		$esett = $esett->email;
		
	    // Create the Transport
		$transport = Swift_SmtpTransport::newInstance($esett->server, $esett->port, $esett->enc)
		  ->setUsername($esett->username)
		  ->setPassword($esett->password)
		  ;
	
		/*
		You could alternatively use a different transport such as Sendmail or Mail:
	
		// Sendmail
		$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
	
		// Mail
		$transport = Swift_MailTransport::newInstance();
		*/
	
		// Create the Mailer using your created Transport
		$mailer = Swift_Mailer::newInstance($transport);
	
		// Create a message
		$message = Swift_Message::newInstance($subject)
		  ->setFrom($from)
		  ->setTo($to)
		  ->setBody($body, 'text/html');
	
		// Send the message
		try {
			$result = $mailer->send($message);
			return $result;
		} catch (Exception $e) {
			print_r($e->getMessage());
		}
		
		
	}
?>