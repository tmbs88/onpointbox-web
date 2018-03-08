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
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 */
class DbHandler {
 
    private $conn;
 
    function __construct() {
        //require_once dirname(__FILE__) . './DbConnect.php';
        
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
		$this->conn->set_charset("utf8");
		
    }
 
    /* ------------- `users` table method ------------------ */
 
    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function insertUser($nome, $email, $password, $tel, $tipo, $dtnasc, $sexo, $foto=null) {
 
        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
 
            // insert query
            $stmt = $this->conn->prepare("INSERT INTO utilizador(nome, email, password, tel, tipo, dtnasc, sexo, foto) values(?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssiisss", $nome, $email, $password, $tel, $tipo, $dtnasc, $sexo, $foto);
 
            if ($stmt->execute()) {
	        	$response['error']=FALSE;
	        	$stmt->close();
	            return $response;
	        } else {
	        	$response['error']=TRUE;
				$response['error_message']=$stmt->error;
	        	$stmt->close();
	            return $response;
	        }
        } else {
            // User with same email already existed in the db
            $response['error']=TRUE;
			$response['error_message']="User already exists.";
            return $response;
        }
 
    }
	
	public function updateUser($id, $nome, $email, $tel, $dtnasc, $sexo, $foto=null) {
 		// insert query
        $stmt = $this->conn->prepare("UPDATE utilizador SET nome=?, email=?, tel=?, dtnasc=?, sexo=?, foto=? WHERE id=?");
        $stmt->bind_param("ssisssi", $nome, $email, $tel, $dtnasc, $sexo, $foto, $id);

            // Check for successful insertion
        if ($stmt->execute()) {
        	$response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
        	$response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
	
	public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM utilizador WHERE id=?");
        $stmt->bind_param("i", $id);
 
        // Check for successful insertion
        if ($stmt->execute()) {
            $response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
	}

    public function updateUserState($id) {
    	$stmt = $this->conn->prepare("SELECT estado FROM utilizador WHERE id=?");
        $stmt->bind_param("i", $id);
 
        if($stmt->execute()){
        	$stmt->bind_result($estado);
        	$stmt->fetch();
        	$estado ? $r=FALSE : $r=TRUE;
        	$stmt->close();

        	$stmt1 = $this->conn->prepare("UPDATE utilizador SET estado=? WHERE id=?");
        	$stmt1->bind_param("ii", $r, $id);

        	if($stmt1->execute()){
        		$response['error']=FALSE;
	        	$stmt1->close();
	            return $response;
        	}else {
	            $response['error']=TRUE;
				$response['error_message']=$stmt1->error;
	        	$stmt1->close();
	            return $response;
       		}
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
 
 	public function updateUserPass($id, $password) {
 
        // insert query
        $stmt = $this->conn->prepare("UPDATE utilizador SET password=? WHERE id=?");
        $stmt->bind_param("si", $password, $id);
 
        if ($stmt->execute()) {
            $response['error']=FALSE;
			$response['error_message']="Password changed.";
        	$stmt->close();
            return $response;
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
	
    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password, $tiporeq) {
    	$response = array('error' => TRUE);
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password, tipo, estado FROM utilizador WHERE email = ?");
        $stmt->bind_param("s", $email);
 
        if($stmt->execute()) {
        	$stmt->bind_result($password_hash, $tipo, $estado);
         	$stmt->store_result();
			
			if($stmt->num_rows > 0) {
	            // Found user with the email
	            $stmt->fetch();
	            $stmt->close();
	
				//verific tipo
				if($tipo != $tiporeq){
					$response['error'] = TRUE;
					$response['errno'] = 2;
					$response['error_message'] = "You do not have permissions.";
					return $response;
				} else if ($estado == 0) {
					$response['error'] = TRUE;					
	                $response['errno'] = 3;
					$response['error_message'] = "Account Disabled.";
	                return $response;
				} else if ($password_hash != $password) {
	                $response['error'] = TRUE;					
	                $response['errno'] = 4;
					$response['error_message'] = "Wrong password.";
	                return $response;
	            } else {
	            	$response['error'] = FALSE;
	            	$response['level'] = $tipo;
	                return $response;
	            }
	        } else {
	        	$response['error'] = TRUE;
				$response['errno'] = 1;
				$response['error_message'] = "Email not found.";
				return $response;
	        }
			
        } else {
            $response['error'] = TRUE;
			$response['errno'] = 0;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }

	public function checkUserPass($email, $password) {
    	$response = array('error' => TRUE);
        // fetching user by email
        $stmt = $this->conn->prepare("SELECT password FROM utilizador WHERE email = ?");
        $stmt->bind_param("s", $email);
 
        if($stmt->execute()) {
        	$stmt->bind_result($password_hash);
         	$stmt->store_result();
			
			if($stmt->num_rows > 0) {
	            // Found user with the email
	            $stmt->fetch();
	            $stmt->close();
	
				//verific tipo
				if ($password_hash != $password) {
	                $response['error'] = TRUE;					
	                $response['errno'] = 2;
					$response['error_message'] = "Wrong password.";
	                return $response;
	            } else {
	            	$response['error'] = FALSE;
	                return $response;
	            }
	        } else {
	        	$response['error'] = TRUE;
				$response['errno'] = 1;
				$response['error_message'] = "Email not found.";
				return $response;
	        }
			
        } else {
            $response['error'] = TRUE;
			$response['errno'] = 0;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
 
    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $stmt = $this->conn->prepare("SELECT id from utilizador WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
 
    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT nome, id, foto FROM utilizador WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
        	/* bind variables to prepared statement */
		    $stmt->bind_result($col1, $col2, $col3);
		
		    /* fetch values */
		    $stmt->fetch();
			$user = array('nome' => $col1, 'id' => $col2, 'foto' => $col3);
		
		    /* close statement */
            $stmt->close();
            return $user;
        } else {
        	$stmt->close();
            return NULL;
        }
    }
 
 	public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, tel, nome, email, foto, estado, dtnasc, sexo FROM utilizador WHERE id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
        	/* bind variables to prepared statement */
		    $stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8);
		
		
			
		    /* fetch values */
		    $stmt->fetch();
			if($col2 == NULL) $col2="";
			if($col7 == NULL) $col7="";
			if($col8 == NULL) $col8="";
			$user = array('id' => $col1, 
						  'phone' => $col2,
						  'nome' => $col3,
						  'dtnasc' => $col7,
						  'gender' => $col8,
						  'email' => $col4,
						  'img' => $col5,
						  'estado' => $col6);
		
		    /* close statement */
            $stmt->close();
            return $user;
        } else {
        	$stmt->close();
            return NULL;
        }
    }
	
 	public function getUserList($type, $selector){
 		
		
		$result = $this->conn->query("SELECT id, $selector FROM utilizador WHERE tipo=$type ORDER BY $selector");
	
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'title' => $row["$selector"]
							);
			}
		} 
	
		//free result set 
		$result->free();
		// close connection 
		$this->conn->close();
		
		
		//envia-los para a pagina	
		return $rows;
 	}
	
	public function insertClass($nome, $dtini, $dtfim, $max, $desc, $period) {
 		// insert query
 		$this->conn->autocommit(FALSE);
 		
        $stmt1 = $this->conn->prepare("INSERT INTO tipoaula(nome, datainicio, datafim, maxalunos, descricao)".
        							" values(?, ?, ?, ?, ?)");
									
        $stmt1->bind_param("sssis", $nome, $dtini, $dtfim, $max, $desc);
 
        if ($stmt1->execute()) {
        	
			$lastid = $stmt1->insert_id;
			
			foreach ($period as $key => $value) {
				$stmt2 = $this->conn->prepare("INSERT INTO periodaula(id_dia, hora, duracao, id_prof, id_tipoaula)".
        							" values(?, ?, ?, ?, ?)");
									
        		$stmt2->bind_param("issii", 
        							$value[0],  
        							$value[1], 
        							$value[2],
        							$value[3], 
        							$lastid);
				
				if(!$stmt2->execute()) {
					$this->conn->rollback();
					$response['error']=TRUE;
					$response['error_message']=$stmt2->error;
		        	$stmt1->close();
					$stmt2->close();
		            return $response;
				}
			}
			
        	$this->conn->commit();
			$response['error']=FALSE;
			$response['id']= $lastid;
        	$stmt1->close();
            return $response;
        } else {
        	$this->conn->rollback();
        	$response['error']=TRUE;
			$response['error_message']=$stmt1->error;
        	$stmt1->close();
            return $response;
        }
    }
	
	public function updateClass($id, $nome, $dtini, $dtfim, $max, $desc, $period) {
 		// insert query
 		$this->conn->autocommit(FALSE);
		
        $stmt1 = $this->conn->prepare("UPDATE tipoaula SET nome=?, datainicio=?, datafim=?, maxalunos=?, descricao=? WHERE id=?");
        $stmt1->bind_param("sssisi", $nome, $dtini, $dtfim, $max, $desc, $id);

            // Check for successful insertion
        if ($stmt1->execute()) {
			
			if($period!=null){
				foreach ($period as $key => $value) {
					$stmt2 = $this->conn->prepare("INSERT INTO periodaula(id_dia, hora, duracao, id_prof, id_tipoaula)".
	        							" values(?, ?, ?, ?, ?)");
										
	        		$stmt2->bind_param("issii", 
	        							$value[0],  
	        							$value[1], 
	        							$value[2], 
	        							$value[3],
	        							$id);
					
					if(!$stmt2->execute()) {
						$this->conn->rollback();
						$response['error']=TRUE;
						$response['error_message']=$stmt2->error;
			        	$stmt1->close();
						$stmt2->close();
			            return $response;
					}
				}
			}
        	$this->conn->commit();
			$response['error']=FALSE;
        	$stmt1->close();
            return $response;
        } else {
        	$response['error']=TRUE;
			$response['error_message']=$stmt1->error;
        	$stmt1->close();
            return $response;
        }
    }
	
	public function getClassList(){
 		$result = $this->conn->query("SELECT id, nome FROM tipoaula ORDER BY nome");
	
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'title' => $row["nome"]
							);
			}
		} 
	
		//free result set 
		$result->free();
		// close connection 
		$this->conn->close();
		
		
		//send them to the page
		return $rows;
 	}
 	
 	private function getClassPeriod($id) {
 		$rows = null;
        $result = $this->conn->query("SELECT periodaula.id AS id, dia.nome AS nome, dia.num AS num, periodaula.hora AS hora, periodaula.duracao AS duracao, utilizador.nome AS prof FROM periodaula, dia, utilizador WHERE periodaula.id_tipoaula=$id AND dia.num=periodaula.id_dia AND periodaula.id_prof=utilizador.id ORDER BY dia.num");

        if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'day' => $row["nome"],
							'dayn' => $row["num"],
							'hour' => $row["hora"],
							'duration' => $row["duracao"],
							'instructor' => $row["prof"]
							);
			}
		} 
	
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $rows;
 	}
	
	public function getClassById($id) {
		
		$period = $this->getClassPeriod($id);
		
        $stmt = $this->conn->prepare("SELECT tipoaula.id, tipoaula.nome, tipoaula.datainicio, tipoaula.datafim, tipoaula.maxalunos, tipoaula.descricao, tipoaula.estado ".
        								"FROM tipoaula WHERE tipoaula.id = ?");
        $stmt->bind_param("s", $id);
        if ($stmt->execute()) {
        	/* bind variables to prepared statement */
		    $stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7);
		
		    /* fetch values */
		    $stmt->fetch();
			for ($i=1; $i < 7; $i++) {
				$col= "col$i" ;
				if($$col == NULL) $$col="";
			}
			
			//print_r($period);
			
			$user = array('id' => $col1, 
						  'nome' => $col2, 
						  'dtini' => $col3, 
						  'dtfim' => $col4,
						  'max' => $col5,
						  'desc' => $col6,
						  'estado' => $col7,
						  'period' => $period);
		
		    /* close statement */
            $stmt->close();
            return $user;
        } else {
        	$stmt->close();
            return NULL;
        }
    }
    
    public function getClassListId($estado) {
		$rows = null;
		/*
		SELECT tipoaula.*, periodaula.* 
		FROM tipoaula LEFT JOIN periodaula ON periodaula.id_tipoaula=tipoaula.id 
		WHERE estado=1 AND periodaula.id IS NOT NULL
		GROUP BY nome
		*/
		$result = $this->conn->query("SELECT tipoaula.id ".
										"FROM tipoaula LEFT JOIN periodaula ON periodaula.id_tipoaula=tipoaula.id ".
										"WHERE estado=$estado AND periodaula.id IS NOT NULL ".
										"GROUP BY nome");
		
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = $row["id"];
							
			}
		} 
	
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $rows;
 	}
    
    public function updateClassState($id) {
    	$stmt = $this->conn->prepare("SELECT estado FROM tipoaula WHERE id=?");
        $stmt->bind_param("i", $id);
 
        if($stmt->execute()){
        	$stmt->bind_result($estado);
        	$stmt->fetch();
        	$estado ? $r=FALSE : $r=TRUE;
        	$stmt->close();

        	$stmt1 = $this->conn->prepare("UPDATE tipoaula SET estado=? WHERE id=?");
        	$stmt1->bind_param("ii", $r, $id);

        	if($stmt1->execute()){
        		$response['error']=FALSE;
	        	$stmt1->close();
	            return $response;
        	}else {
	            $response['error']=TRUE;
				$response['error_message']=$stmt1->error;
	        	$stmt1->close();
	            return $response;
       		}
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
	
	public function deleteClass($id) {
        $stmt = $this->conn->prepare("DELETE FROM tipoaula WHERE id=?");
        $stmt->bind_param("i", $id);
 
        // Check for successful insertion
        if ($stmt->execute()) {
            $response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
	}
	public function deletePeriod($id) {
        $stmt = $this->conn->prepare("DELETE FROM periodaula WHERE id=?");
        $stmt->bind_param("i", $id);
 
        // Check for successful insertion
        if ($stmt->execute()) {
            $response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
	}
	
	public function insertEvent($std, $ed, $tp, $pd) {
 		// insert query
        $stmt = $this->conn->prepare("INSERT INTO aula(event_start, event_end, id_tipoaula, id_periodaula) values(?, ?, ?, ?)");
        $stmt->bind_param("ssii", $std, $ed, $tp, $pd);
 
            if ($stmt->execute()) {
	        	$response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
        	$response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
	
	public function selectEvent($ini, $end, $id) {
		$rows = null;
		$result = $this->conn->query("SELECT * FROM aula WHERE event_start>='$ini' AND event_start<='$end' AND id_tipoaula=$id");
		
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'event_start' => $row["event_start"],
							'event_end' => $row["event_end"],
							'id_tipoaula' => $row["id_tipoaula"],
							'id_periodaula' => $row["id_periodaula"]
							);
			}
		} 
	
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $rows;
 	}
 	
 	public function selectRegEventsUser($intmin, $uid, $ismobile=false) {
		$rows = null;
		$response = array('error' => TRUE);
		/*
		SELECT 
			aula.id, aula.event_start, aula.event_end, 
		    tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, 
		 	periodaula.duracao as duration, 
		    utilizador.nome as prof, 
		    alunoaula.id as regid,
		 	(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc
		FROM aula 
			INNER JOIN tipoaula 
		    	ON tipoaula.id=aula.id_tipoaula
		    INNER JOIN periodaula
		    	on periodaula.id=aula.id_periodaula
		    INNER JOIN utilizador
		    	ON utilizador.id=periodaula.id_prof
		    LEFT JOIN alunoaula 
		    	ON aula.id=alunoaula.id_aula 
		        AND 19=alunoaula.id_aluno
		WHERE TIMEDIFF(DATE_SUB(event_start, INTERVAL 6000 MINUTE), NOW()) <='00:00:00' 
			AND TIMEDIFF(event_end, NOW()) >='00:00:00'
		ORDER BY event_start, nome
		*/
		$result = $this->conn->query("SELECT ".
										"aula.id as ida, aula.event_start, aula.event_end, ".
										"tipoaula.id as id, tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, ".
										"periodaula.duracao as duration, ".
										"utilizador.nome as prof, ".
										"alunoaula.id as regid, ".
										"(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc ".
									 "FROM aula ".
									 	"INNER JOIN tipoaula ON tipoaula.id=aula.id_tipoaula ".
									 	"INNER JOIN periodaula ON periodaula.id=aula.id_periodaula ".
									 	"INNER JOIN utilizador ON utilizador.id=periodaula.id_prof ".
									 	"LEFT JOIN alunoaula ON aula.id=alunoaula.id_aula AND $uid=alunoaula.id_aluno ".
										"WHERE TIMEDIFF(DATE_SUB(event_start, INTERVAL $intmin MINUTE), NOW()) <='00:00:00' ".
										"AND TIMEDIFF(event_end, NOW()) >='00:00:00' ".
										"ORDER BY aula.event_start, tipoaula.nome");
		
		if ($result->num_rows >= 1) {
			if($ismobile){
				while($row = $result->fetch_array(MYSQLI_ASSOC))
				{
					$rows[] = array( 
								'id' => $row["id"],
								'ida' => $row["ida"],
								'start' => $row["event_start"],
								'end' => $row["event_end"],
								'duration' => $row['duration'],
								'title' => $row["nome"],
								'max' => $row['maxalunos'],
								'insc' => $row['insc'],
								'instructor' => $row["prof"],
								'regid' => $row["regid"]
								);
				}
			}else{
				while($row = $result->fetch_array(MYSQLI_ASSOC))
				{
					$rows[] = array( 
								'id' => $row["id"],
								'ida' => $row["ida"],
								'start' => $row["event_start"],
								'end' => $row["event_end"],
								'duration' => $row['duration'],
								'title' => $row["nome"],
								'description' => $row["descricao"],
								'max' => $row['maxalunos'],
								'insc' => $row['insc'],
								'instructor' => $row["prof"],
								'regid' => $row["regid"]
								);
				}
			}
			
		}else {
			$response['error'] = TRUE;
			$response['errno'] = 1;
			$response['error_message'] = "No classes available at the moment.";
			$result->free();
			return $response;
		}
		$response['error'] = FALSE;
		$response['rows'] = $rows;
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $response;
 	}

	public function getUserByEmailMobile($email) {
        $stmt = $this->conn->prepare("SELECT nome, id, tel, dtnasc, sexo, foto FROM utilizador WHERE email = ?");
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
        	/* bind variables to prepared statement */
		    $stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6);
		
		    /* fetch values */
		    $stmt->fetch();
			$user = array('nome' => $col1, 'id' => $col2, 'tel' => $col3, 'dtnasc' => $col4, 'sexo' => $col5, 'image' => $col6, 'email' => $email);
		
		    /* close statement */
            $stmt->close();
            return $user;
        } else {
        	$stmt->close();
            return NULL;
        }
    }
	
	public function isRegExistsMobile($uid, $cid) {
        $stmt = $this->conn->prepare("SELECT id from alunoaula WHERE id_aluno = ? AND id_aula = ?");
        $stmt->bind_param("ss", $uid, $cid);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }
	
	public function isFull($cid) {
		$stmt1 = $this->conn->prepare("SELECT maxalunos FROM tipoaula INNER JOIN aula ON aula.id_tipoaula=tipoaula.id WHERE aula.id = ?");
        $stmt1->bind_param("s", $cid);
        
		if($stmt1->execute()){
			$stmt1->bind_result($max);
        	$stmt1->fetch();
			
			if($max != NULL){
				$stmt1->close();
				$stmt2 = $this->conn->prepare("SELECT COUNT(*) FROM alunoaula WHERE id_aula = ?");
        		$stmt2->bind_param("s", $cid);
				
				if($stmt2->execute()){
					$stmt2->bind_result($actual);
					$stmt2->fetch();
					
					if($max === $actual){
						return TRUE;
					}
					$stmt2->close();
				}
			}
			
        	//$stmt1->close();
			return FALSE;
		}
	    return FALSE;  
    }
    
    public function insertRegMobile($uid, $cid) {
 		// insert query
        $stmt = $this->conn->prepare("INSERT INTO alunoaula(id_aluno, id_aula) values(?, ?)");
        $stmt->bind_param("ii", $uid, $cid);
 
            if ($stmt->execute()) {
	        	$response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
        	$response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
    }
	
	public function deleteRegMobile($uid, $cid) {
        $stmt = $this->conn->prepare("DELETE FROM alunoaula WHERE id_aluno = ? AND id_aula = ?");
        $stmt->bind_param("ii", $uid, $cid);
 
        // Check for successful insertion
        if ($stmt->execute()) {
            $response['error']=FALSE;
        	$stmt->close();
            return $response;
        } else {
            $response['error']=TRUE;
			$response['error_message']=$stmt->error;
        	$stmt->close();
            return $response;
        }
	}
	
	public function selectAllSchedule() {
		$rows = null;
		
		$result = $this->conn->query("SELECT ".
										"aula.id as ida, aula.event_start, aula.event_end, ".
										"tipoaula.id as id, tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, ".
										"periodaula.duracao as duration, ".
										"utilizador.nome as prof, ".
										"(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc ".
									 "FROM aula ".
									 	"INNER JOIN tipoaula ON tipoaula.id=aula.id_tipoaula ".
									 	"INNER JOIN periodaula ON periodaula.id=aula.id_periodaula ".
									 	"INNER JOIN utilizador ON utilizador.id=periodaula.id_prof ".
									 "WHERE tipoaula.estado = 1 ".
										"ORDER BY aula.event_start, tipoaula.nome");
		
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'start' => $row["event_start"],
							'end' => $row["event_end"],
							'duration' => $row['duration'],
							'title' => $row["nome"],
							'description' => $row["descricao"],
							'max' => $row['maxalunos'],
							'insc' => $row['insc'],
							'instructor' => $row["prod"],
							'color' => 'green'
							);
			}
		}
		$response['rows'] = $rows;
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $response;
 	}
 	
 	public function selectNextEventsProf($uid) {
		$rows = null;
		$response = array('error' => TRUE);
		/*
		SELECT 
			aula.id, aula.event_start, aula.event_end, 
		    tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, 
		 	periodaula.duracao as duration, 
		    utilizador.nome as prof, 
		 	(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc
		FROM aula 
			INNER JOIN tipoaula 
		    	ON tipoaula.id=aula.id_tipoaula
		    INNER JOIN periodaula
		    	on periodaula.id=aula.id_periodaula
		    INNER JOIN utilizador
		    	ON utilizador.id=periodaula.id_prof
                AND utilizador.id=8
		WHERE  TIMEDIFF(event_end, NOW()) >='00:00:00'
		ORDER BY event_start, nome
		*/
		$result = $this->conn->query("SELECT ".
										"aula.id as ida, aula.event_start, aula.event_end, ".
										"tipoaula.id as id, tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, ".
										"periodaula.duracao as duration, ".
										"utilizador.nome as prof, ".
										"(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc ".
									 "FROM aula ".
									 	"INNER JOIN tipoaula ON tipoaula.id=aula.id_tipoaula ".
									 	"INNER JOIN periodaula ON periodaula.id=aula.id_periodaula ".
									 	"INNER JOIN utilizador ON utilizador.id=periodaula.id_prof AND utilizador.id=$uid ".
									 "WHERE TIMEDIFF(event_end, NOW()) >='00:00:00' ".
									 	"AND tipoaula.estado = 1 ".
										"ORDER BY aula.event_start, tipoaula.nome");
		
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'ida' => $row["ida"],
							'start' => $row["event_start"],
							'end' => $row["event_end"],
							'duration' => $row['duration'],
							'title' => $row["nome"],
							'description' => $row["descricao"],
							'max' => $row['maxalunos'],
							'insc' => $row['insc'],
							'instructor' => $row["prod"]
							);
			}
		}else {
			$response['error'] = TRUE;
			$response['errno'] = 1;
			$response['error_message'] = "Não tem aulas disponiveis de momento.";
			$result->free();
			return $response;
		}
		$response['error'] = FALSE;
		$response['rows'] = $rows;
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $response;
 	}
 	
 	public function selectNextEventsAdmin() {
		$rows = null;
		$response = array('error' => TRUE);
		/*
		SELECT 
			aula.id, aula.event_start, aula.event_end, 
		    tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, 
		 	periodaula.duracao as duration, 
		    utilizador.nome as prof, 
		 	(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc
		FROM aula 
			INNER JOIN tipoaula 
		    	ON tipoaula.id=aula.id_tipoaula
		    INNER JOIN periodaula
		    	on periodaula.id=aula.id_periodaula
		    INNER JOIN utilizador
		    	ON utilizador.id=periodaula.id_prof
		WHERE  TIMEDIFF(event_end, NOW()) >='00:00:00'
		ORDER BY event_start, nome
		*/
		$result = $this->conn->query("SELECT ".
										"aula.id as ida, aula.event_start, aula.event_end, ".
										"tipoaula.id as id, tipoaula.nome, tipoaula.descricao, tipoaula.maxalunos, ".
										"periodaula.duracao as duration, ".
										"utilizador.nome as prof, ".
										"(SELECT COUNT(*) FROM alunoaula WHERE id_aula = aula.id) as insc ".
									 "FROM aula ".
									 	"INNER JOIN tipoaula ON tipoaula.id=aula.id_tipoaula ".
									 	"INNER JOIN periodaula ON periodaula.id=aula.id_periodaula ".
									 	"INNER JOIN utilizador ON utilizador.id=periodaula.id_prof ".
									 "WHERE TIMEDIFF(event_end, NOW()) >='00:00:00' ".
									 	"AND tipoaula.estado = 1 ".
										"ORDER BY aula.event_start, tipoaula.nome");
		
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'id' => $row["id"],
							'ida' => $row["ida"],
							'start' => $row["event_start"],
							'end' => $row["event_end"],
							'duration' => $row['duration'],
							'title' => $row["nome"],
							'description' => $row["descricao"],
							'max' => $row['maxalunos'],
							'insc' => $row['insc'],
							'instructor' => $row["prod"]
							);
			}
		}else {
			$response['error'] = TRUE;
			$response['errno'] = 1;
			$response['error_message'] = "Não tem aulas disponiveis de momento.";
			$result->free();
			return $response;
		}
		$response['error'] = FALSE;
		$response['rows'] = $rows;
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $response;
 	}
 	
 	public function getUserList2($type, $cid){
 		$rows = null;
		$response = array('error' => TRUE);
		/*
		SELECT nome, foto 
		FROM utilizador 
			INNER JOIN alunoaula ON utilizador.id=alunoaula.id_aluno AND alunoaula.id_aula=51
		WHERE tipo=3 
		ORDER BY nome
		 */
		$result = $this->conn->query("SELECT nome, foto FROM utilizador ".
									"INNER JOIN alunoaula ON utilizador.id=alunoaula.id_aluno AND alunoaula.id_aula=$cid ".
									"WHERE tipo=$type ORDER BY nome");
		
		if ($result->num_rows >= 1) {
			while($row = $result->fetch_array(MYSQLI_ASSOC))
			{
				$rows[] = array( 
							'name' => $row["nome"],
							'img' => $row["foto"]
							);
			}
		}else {
			$response['error'] = TRUE;
			$response['errno'] = 1;
			$response['error_message'] = "There are no trainees enrolled at the moment.";
			$result->free();
			return $response;
		}
		$response['error'] = FALSE;
		$response['rows'] = $rows;
		//free result set 
		$result->free();
		// close connection 
		//$this->conn->close();
 		return $response;
 	}
}
 
?>