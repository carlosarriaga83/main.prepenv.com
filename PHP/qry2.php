<?php
//$ds          = DIRECTORY_SEPARATOR;  //1
session_start();
//header('Content-type: text/javascript');
 header('Content-type: text/javascript; charset=utf-8');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);



$BODY = $_REQUEST['datos'];
//$BODY = file_get_contents('php://input');
//$KEYS = array_keys(json_decode($BODY, true));
$JSON = json_decode($BODY,true);
//print_r($JSON);



// FECHA
date_default_timezone_set('America/Mexico_City');
$current_date = date('Y-m-d H:i:s');
$current_ts = date('Y-m-d H:i:s', strtotime($current_date . ' -1 hour')); 


//print_r($JSON); 
//echo($JSON['request']);

 $db_login = mysqli_connect('localhost', 'u124132715_paradise' ,'Paradise1!.', 'u124132715_paradise'); 
	 
if( $JSON['request'] == 0 ){
			
			if ($JSON['pwd'] == ''){
					echo 'Ingrese usuario y contrasena.';
					$db_login -> close(); die;
				}
			
			if ( isset($JSON['user']) && isset($JSON['pwd'])  ) {
				$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE user ='%s' AND pwd = '%s'", $JSON['user'], $JSON['pwd'] );
				
			}else{
	
				echo 'Ingrese usuario y contrasena.';
				$db_login -> close(); die;
			}
			//echo $q;
			$result = mysqli_query($db_login, $q);
			
			if (mysqli_num_rows($result)!=0) { 
			
				session_start();
				$row = mysqli_fetch_assoc($result);
				echo '1';
				$_SESSION["login"] = true;
				$_SESSION["nivel"] = $row["nivel"];
				$_SESSION["id"] = $row["id"];
				$_SESSION["user"] = $row["user"];
				
				$q = sprintf("UPDATE Usuarios SET TS = '%s' WHERE id = '%s'", $current_ts, $row["id"] );
				$result = mysqli_query($db_login, $q); 
				 
				$db_login -> close(); die;
				
				
			} else {
				
				echo 'Usuario y/o contrasena incorrectos.';
				$db_login -> close(); die;
				exit;
				//header("Location: index.php");
			} 
			  
			
	//$db_login -> close(); 	
	$db_login -> close(); die;
}

// REGRESA OBJETO CON EMPRESAS
//	id
if( $JSON['request'] == 1 ){
			
			session_start();
			
			
				
				//SELECCIONA SOLO LAS EMPRESAS RELACIONADAS CON ESE USUARIO
				
				$q = sprintf("SELECT DISTINCT Empresas.id, Empresas.empresa , Empresas.direccion, Empresas.contacto, Empresas.correo
								FROM u124132715_SYP.Empresas 
								INNER JOIN Asignaciones ON  Asignaciones.Empresa = Empresas.id
								WHERE Asignaciones.Usuario = '%s'  ", $_SESSION["id"]);
				
				$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'  ", $JSON['id']);
			
			
			/*
			if ( !isset($JSON['id']) ) {
				$q = sprintf("SELECT * FROM u124132715_SYP.Empresas ORDER BY empresa ASC" );
				
			}else{
	
				$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'  ", $JSON['id']);
			}
			*/
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			if (mysqli_num_rows($result)==0) {echo '{"data": [], "total": 0, "recordsTotal": 0, "recordsFiltered": 0}'; $db_login -> close(); die;	} 
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo '{ "data": ' . $myJSON . '}';
			
					
	$db_login -> close(); die;
}

// REGRESA OBJETO DE UNA EMPRESAS
//	id
if( $JSON['request'] == 1.01 ){
			
			$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'  ", $JSON['id']);
			
			
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo  $myJSON ;
			
					
	$db_login -> close(); die;
}

// REGRESA OBJETO CON EMPRESAS
//	id
if( $JSON['request'] == 1.1 ){
	
			if ( !isset($JSON['id']) ) {
				$q = sprintf("SELECT * FROM u124132715_SYP.Empresas ORDER BY empresa ASC" );
				
			}else{
	
				$q = sprintf("SELECT DISTINCT Empresas.* 
								FROM u124132715_SYP.Empresas 
								INNER JOIN Asignaciones ON  Empresas.id = Asignaciones.Empresa
								WHERE Asignaciones.Usuario = '%s'  ", $JSON['id']);
			}
			
			if ( $_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3 ) {
				$q = sprintf("SELECT * FROM u124132715_SYP.Empresas ORDER BY empresa ASC" ); 
				 
			}
			
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo  $myJSON;
			
					
	$db_login -> close(); die;
}

// REGRESA OBJETO CON EMPRESAS
//	id
if( $JSON['request'] == 1.12 ){
	
			if ( !isset($JSON['id']) ) {
				$q = sprintf("SELECT * FROM u124132715_SYP.Empresas ORDER BY empresa ASC" );
				
			}else{
	
				$q = sprintf("SELECT DISTINCT Empresas.id, Empresas.empresa 
								FROM u124132715_SYP.Empresas 
								INNER JOIN Asignaciones ON  Empresas.id = Asignaciones.Empresa
								WHERE Asignaciones.Usuario = '%s'  ", $JSON['id']);
			}
			
			if ( $_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3) {
				//$q = sprintf("SELECT * FROM u124132715_SYP.Empresas ORDER BY empresa ASC" ); 
				 
			}
 
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo  $myJSON;
			
					
	$db_login -> close(); die;
}

// REGRESA OBJETO CON USUARIOS
//	id
if( $JSON['request'] == 1.2 ){
	
			if ( !isset($JSON['id']) ) {
				$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios ORDER BY user ASC" );
				
			}else{
	
				$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE id = '%s'  ", $JSON['id']);
			}
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo  $myJSON;
			
					
	$db_login -> close(); die;
}

// ACTUALIZA ASIGNACIONES
//	id
if( $JSON['request'] == 1.3 ){
			
			//echo $JSON['empresas'];
			
			$data = str_getcsv($JSON['empresas']);
			//var_dump($data);
			
			$q = sprintf("DELETE FROM u124132715_SYP.Asignaciones WHERE Usuario = '%s'", $JSON['usuario'] );
			$result = mysqli_query($db_login, $q);
			
			foreach ($data as &$value) {
				
				$q = sprintf("INSERT INTO u124132715_SYP.Asignaciones (Usuario,Empresa) VALUES ('%s','%s')", $JSON['usuario'], $value );
				$result = mysqli_query($db_login, $q);
				//echo $q;
			}
			

			
					
	$db_login -> close(); die;
}


// ACTUALIZA NIVEL DE USUARIO
//	id
if( $JSON['request'] == 1.4 ){
			
			//echo $JSON['empresas'];
			
			$data = str_getcsv($JSON['empresas']);
			//var_dump($data);
			
			$q = sprintf("UPDATE u124132715_SYP.Usuarios SET nivel = '%s' WHERE id = '%s'", $JSON['nivel'], $JSON['usuario'] );
			$result = mysqli_query($db_login, $q);
			
			echo $q;
			
					
	$db_login -> close(); die;
}

// CREA NUEVO USUARIO
//	id
if( $JSON['request'] == 1.5 ){
			
			$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE user = '%s'", $JSON['user'] );
			$result = mysqli_query($db_login, $q);
			
			if (mysqli_num_rows($result)!=0) { 
			
				echo "Usuario existente, seleccione otro nombre";
				
				$db_login -> close(); die;
			} 
			
			
			$q = sprintf("INSERT INTO u124132715_SYP.Usuarios (nivel, user, pwd) VALUES ('%s','%s','%s') ", $JSON['nivel'], $JSON['user'], $JSON['pwd']  );
			$result = mysqli_query($db_login, $q);
			
			echo "Usuario creado exitosamente.";
			
					
	$db_login -> close(); die;
}

if( $JSON['request'] == 1.501 ){
			
			$q = sprintf("UPDATE u124132715_SYP.Usuarios SET pwd = '%s' WHERE user = '%s' ",  $JSON['pwd'], $JSON['user']  );
			$result = mysqli_query($db_login, $q);
			//echo $q;
			if (mysqli_num_rows($result)!=0) { }  
			
			
			//$q = sprintf("INSERT INTO u124132715_SYP.Usuarios (nivel, user, pwd) VALUES ('%s','%s','%s') ", '1', $JSON['user'], $JSON['pwd']  );
			//$result = mysqli_query($db_login, $q);
			
			echo "Usuario creado exitosamente.";
			
					
	$db_login -> close(); die;
}

// EDITA USUARIO
//	id
if( $JSON['request'] == 1.51 ){
			

			
			
			$q = sprintf("UPDATE u124132715_SYP.Usuarios SET user='%s', pwd='%s', nivel='%s', telefono = '%s' WHERE id = '%s' ",  $JSON['user'], $JSON['pwd'], $JSON['nivel'], $JSON['telefono'], $JSON['id']  );
			$result = mysqli_query($db_login, $q); 
			
			echo "Usuario actualizado exitosamente.";
			
					
	$db_login -> close(); die;
}


// ELIMINA USUARIO
//	id
if( $JSON['request'] == 1.6 ){
			
			$q = sprintf("DELETE FROM u124132715_SYP.Usuarios WHERE id = '%s'", $JSON['user'] );
			$result = mysqli_query($db_login, $q);
			
			 echo "Usuario eliminado."; 
					
	$db_login -> close(); die;
}

// OBTIENE DAOTS DE USUARIO
//	id
if( $JSON['request'] == 1.7 ){
			
			$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE id = '%s'", $JSON['id'] );

			
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo  $myJSON;
					
	$db_login -> close(); die;
}


// REGRESA OBJETO CON DOCUMENTOS DE CIERTA EMPRESA
//	Empresa_id
if( $JSON['request'] == 2 ){
	
			//$q = sprintf("SELECT * FROM u124132715_SYP.Documentos WHERE Empresa_id = '%s'  ", $JSON['Empresa_id']);
			//if ( !isset($JSON['Empresa_id']) || $JSON['Empresa_id'] == 'undefined' ) {$JSON['Empresa_id'] = 0;}
			
			$q = sprintf("SELECT Documentos.id, Empresas.empresa as Empresa_id, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, Documentos.path
					FROM Documentos
					INNER JOIN Empresas ON Documentos.Empresa_id = Empresas.id 
					WHERE Empresas.id = '%s' ", $JSON['Empresa_id']);
			 
			//echo $q;
			
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			if (mysqli_num_rows($result)==0) {echo '{"data": [], "total": 0, "recordsTotal": 0, "recordsFiltered": 0}'; $db_login -> close(); die;	} 
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo '{ "data": ' . $myJSON . '}';
			
					
	$db_login -> close(); die;
}

// ELIMINA EMPRESA
//	Empresa_id
if( $JSON['request'] == 2.1 ){
	
			//$q = sprintf("SELECT * FROM u124132715_SYP.Documentos WHERE Empresa_id = '%s'  ", $JSON['Empresa_id']);
			$q = sprintf("DELETE FROM u124132715_SYP.Empresas WHERE Empresas.id = '%s' ", $JSON['Empresa_id']);
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			echo "Empresa borrada";
			
					
	$db_login -> close(); die;
}

// ELIMINA DOCUMENTO
//	id
if( $JSON['request'] == 2.2 ){
	
	
	
			$q = sprintf("SELECT * FROM u124132715_SYP.Documentos WHERE id = '%s'  ", $JSON['id']);
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['path'] = $row['path'];
				$i++;
			}
			
			  if(isset($myArr['path'])) {
				$file = $myArr['path'];

				if(file_exists($file)) {
				  unlink($file);
				  //echo "File deleted successfully";
				} else {
				  //echo "File does not exist: " . $file;
				  //$db_login -> close(); die;
				}
			  }else {
				  
				  echo "Path desconocido";
				  $db_login -> close(); die;	
			  }
			
			$q = sprintf("DELETE FROM u124132715_SYP.Documentos WHERE id = '%s' ", $JSON['id']);
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			echo "Documento eliminado";
			
			

			
					
	$db_login -> close(); die;
}

// GUARDA ID DEL ARHCIVO SUBIDO
if( $JSON['request'] == 3 ){
	
			$q = sprintf("UPDATE u124132715_SYP.Documentos SET path='%s' WHERE id = '%s' ", $JSON['path'], $JSON['id']);
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			echo "1";
						
					
	$db_login -> close(); die;
}

// ACTUALIZA REGISTRO DE DOCUMENTO
if( $JSON['request'] == 4 ){
	
			
			$q = sprintf("UPDATE u124132715_SYP.Documentos SET Inicia = '%s', Vence = '%s' WHERE id = '%s'", $JSON['inicia'],	$JSON['expira'], $JSON['id']);
			
			if ( $JSON['DOC_ID'] != '' ) {
				
					$directory = '../_BIBLIOTECA';
					$file_name = sprintf('*ID%s*',$JSON['DOC_ID']);
					//echo $file_name;
					$RESP['DATOS'] = FIND_FILE($directory , $file_name);
					
					if ($RESP['DATOS'][1] == ''){						
						echo "Error, no se encontró el archivo con ese ID.";
						$db_login -> close(); die;
					}
					
					$destination_folder = sprintf("../_REPO/%s/%s",  $JSON['id_E'], $JSON['id']);
					
					if(!file_exists($destination_folder)) {
						//echo 'Creando: ' . $upload_folder . "\r\n"; 
						mkdir($destination_folder);
					}
					
					$destinationFile = $destination_folder . '/' . $RESP['DATOS'][1];
					
					if (copy($RESP['DATOS'][0] ,  $destinationFile)) {
						//echo "File copied successfully." . "\nFROM: " . $RESP['DATOS'][0] . "\nTO: " . $destinationFile . "\n" ;
					} else {
						//echo "Failed to copy the file." . "\nFROM: " . $RESP['DATOS'][0] . "\nTO: " . $destinationFile . "\n" ;
						echo "Error, no se encontró el archivo con ese ID.";
						$db_login -> close(); die;
					}

				$q = sprintf("UPDATE u124132715_SYP.Documentos SET Inicia = '%s', Vence = '%s', path = '%s' WHERE id = '%s'", $JSON['inicia'],	$JSON['expira'], $destinationFile, $JSON['id']);
				
			} 
			
			//echo $q;
		$fetch = mysqli_query($db_login, $q);
		echo sprintf("Registro actualizado exisotamente.");
		//echo sprintf("Registro actualizado exisotamente E: %s D: %s ", $JSON['id_E'], $JSON['id']);
	$db_login -> close(); die;
}

// CREA DOCUMENTO
if( $JSON['request'] == 4.1 ){
	
			$q = sprintf("INSERT into u124132715_SYP.Documentos ( Empresa_id, Titulo, Inicia, Vence, path ) values ('%s','%s','%s','%s','%s')", $JSON['Empresa_id'], $JSON['titulo'],$JSON['inicia'],$JSON['vence'],$JSON['path']);
			//echo $q;
		$fetch = mysqli_query($db_login, $q);
		echo sprintf("Documento creado exisotamente.");
		//echo sprintf("Registro actualizado exisotamente E: %s D: %s ", $JSON['id_E'], $JSON['id']);
	$db_login -> close(); die;
}

// REGRESA DATOS DE  DOCUMENTO ID
if( $JSON['request'] == 4.2 ){
	
			$q = sprintf("SELECT * FROM u124132715_SYP.Documentos WHERE id = '%s'", $JSON['id']);
			//echo $q;
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo $myJSON;
			
			$db_login -> close(); die;
}


// INSERTA NUEVA EMPRESA
if( $JSON['request'] == 5 ){
	

		$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE Empresa = '%s' ",  $JSON['empresa']);

		$result = mysqli_query($db_login, $q);
		
			if (mysqli_num_rows($result)!=0) { 
				echo "Empresa existente, elija un nuevo nombre.";
				$db_login -> close(); die;
			}

			$q = sprintf("INSERT into u124132715_SYP.Empresas ( empresa, direccion, contacto, correo, sucursal, telefono) values ('%s','%s','%s','%s','%s','%s')", $JSON['empresa'], $JSON['direccion'],	$JSON['contacto'],	$JSON['correo'], $JSON['sucursal'], $JSON['telefono']);

			//echo $q;

			$result = mysqli_query($db_login, $q);
			
		
			$INSERTED_ID = mysqli_insert_id($db_login);
		
		if ($_SESSION['nivel'] == 1){
			
				$q = sprintf("INSERT INTO u124132715_SYP.Asignaciones (Usuario,Empresa) VALUES ('%s','%s')", $_SESSION['id'], $INSERTED_ID );
				$result = mysqli_query($db_login, $q);
				//echo $q;

		}
		

		echo "Empresa creada exisotamente";

	$db_login -> close(); die;
}

// ACTUALIZA DATOS DE EMPRESA
//	id
if( $JSON['request'] == 5.1 ){
			


			$q = sprintf("UPDATE u124132715_SYP.Empresas SET  empresa = '%s', direccion = '%s', contacto = '%s', correo = '%s', sucursal = '%s', telefono = '%s' WHERE id = '%s'", $JSON['empresa'], $JSON['direccion'],	$JSON['contacto'], $JSON['correo'], $JSON['sucursal'], $JSON['telefono'], $JSON['id']);

			//echo $q;

		$fetch = mysqli_query($db_login, $q);

		echo "Empresa actualizada exisotamente";

	$db_login -> close(); die;
}

// SEMAFORO
if( $JSON['request'] == 0.1 ){
	
		// MES / CANTIDAD
		$q = sprintf("SELECT EXTRACT(MONTH FROM Vence) as mes,COUNT(id) as cantidad FROM u124132715_SYP.Documentos GROUP BY YEAR(Vence), MONTH(Vence) ");
		//echo $q;
		$result = mysqli_query($db_login, $q);
		
		$myArr['1'] = (object)(['mes' => '1 - Enero', 'cantidad' => '0']);
		$myArr['2'] = (object)(['mes' => '2 - Febrero', 'cantidad' => '0']);
		$myArr['3'] = (object)(['mes' => '3 - Marzo', 'cantidad' => '0']);
		$myArr['4'] = (object)(['mes' => '4 - Abril', 'cantidad' => '0']);
		$myArr['5'] = (object)(['mes' => '5 - Mayo', 'cantidad' => '0']);
		$myArr['6'] = (object)(['mes' => '6 - Junio', 'cantidad' => '0']);
		$myArr['7'] = (object)(['mes' => '7 - Julio', 'cantidad' => '0']);
		$myArr['8'] = (object)(['mes' => '8 - Agosto', 'cantidad' => '0']);
		$myArr['9'] = (object)(['mes' => '9 - Septiembre', 'cantidad' => '0']);
		$myArr['10'] = (object)(['mes' => '10 - Octubre', 'cantidad' => '0']);
		$myArr['11'] = (object)(['mes' => '11 - Noviembre', 'cantidad' => '0']);
		$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
		
		//echo json_encode($myArr);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				//echo $row;
				$myArr[$row['mes']] = $row;
				$i++;
			}

			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo $myJSON;

	$db_login -> close(); die;
}

// INDICADORES DE HOME PARA ADMIN
if( $JSON['request'] == 0.2 && ($_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3)){
	
	
	 
		// TOTAL
		$q = sprintf("SELECT COUNT(id) as cantidad FROM u124132715_SYP.Documentos");
			
		
						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['TOTAL'] = $row;
				$i++;
			}
			
		// VENCIDOS
		$q = sprintf("SELECT COUNT(id) as cantidad FROM u124132715_SYP.Documentos 
						WHERE Vence <= NOW()");
						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['VENCIDOS'] = $row;
				$i++;
			}
		// 30 DIAS
		$q = sprintf("SELECT COUNT(id) as cantidad FROM u124132715_SYP.Documentos 
						WHERE Vence 
						BETWEEN CURRENT_DATE()
						AND DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)");
						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['XDIAS'] = $row;
				$i++;
			}
		// OK
		$q = sprintf("SELECT COUNT(id) as cantidad FROM u124132715_SYP.Documentos 
						WHERE Vence > DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)");
						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['OK'] = $row;
				$i++;
			}
			

			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo $myJSON;

	$db_login -> close(); die;
}

// INDICADORES DE HOME PARA CLIENTE
if( $JSON['request'] == 0.2 ){
	
		// TOTAL
			$q = sprintf("SELECT  count(Asignaciones.id) as cantidad
							FROM u124132715_SYP.Documentos
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario
							WHERE Usuarios.id = %s 
							AND Documentos.Vence != '000-00-00' ;", $_SESSION['id']);
			
		
		
		//echo $q;
		
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['TOTAL'] = $row;
				$i++;
			}
			
		// VENCIDOS
			$q = sprintf("SELECT  count(Asignaciones.id) as cantidad
							FROM u124132715_SYP.Documentos
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario
							WHERE Usuarios.id = %s 
							AND Documentos.Vence != '000-00-00' 
							AND Documentos.Vence <= NOW();", $_SESSION['id']);
						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['VENCIDOS'] = $row;
				$i++;
			}
		// 30 DIAS
			$q = sprintf("SELECT  count(Asignaciones.id) as cantidad
							FROM u124132715_SYP.Documentos
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario
							WHERE Usuarios.id = %s 
							AND Documentos.Vence != '000-00-00' 
							AND Documentos.Vence
							BETWEEN CURRENT_DATE()
							AND DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)", $_SESSION['id']);
							
							
						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['XDIAS'] = $row;
				$i++;
			}
		// OK
		
			$q = sprintf("SELECT  count(Asignaciones.id) as cantidad
							FROM u124132715_SYP.Documentos
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario
							WHERE Usuarios.id = %s 
							AND Documentos.Vence != '000-00-00' 
							AND Documentos.Vence > DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)", $_SESSION['id']);
							

						//echo $q;
		$result = mysqli_query($db_login, $q);
		
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr['OK'] = $row;
				$i++;
			}
			

			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo $myJSON;

	$db_login -> close(); die;
}

if( $JSON['request'] == 0.3 ){  
		
		// DOCUMENTOS DEL USUARIO ID
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Documentos.Titulo, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, ''  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id = %s 
							ORDER BY Empresas.empresa, Caducidad ASC ;", $_SESSION['id']);
							

		
		
		if ( $_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3){
			
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Documentos.Titulo, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, ''  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id != ''
							ORDER BY Empresas.empresa, Caducidad ASC ;" ); 
			
		}
	
		// DOCUMENTOS DE LA EMPRESA ID					
		if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] != 'null' || $JSON['Empresa_id'] != 'undefined') ){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Documentos.Titulo, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, ''  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Empresa_id = %s 
							ORDER BY Empresas.empresa, Caducidad ASC;", $JSON['Empresa_id']);

		}
		
		// ADMIN NO EMPRESA SELECCIONADA
		if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined') && $_SESSION['nivel'] == 0){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Documentos.Titulo, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, ''  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id != ''
							ORDER BY Empresas.empresa, Caducidad ASC ;" ); 

		} 
		
		// USUARIO
		
		if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined') && $_SESSION['nivel'] == 1){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Documentos.Titulo, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, ''  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id = %s 
							ORDER BY Empresas.empresa, Caducidad ASC ;", $_SESSION['id']);

		}	
			
		 //echo $q; 
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}

		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo $myJSON;
		
		$db_login -> close(); die;
}
 
if( $JSON['request'] == 0.33 ){  
		
		// DOCUMENTOS DEL USUARIO ID
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '', Documentos.Carta  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id = %s 
							ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;", $_SESSION['id']);
							

		
		
		if ( $_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3){
			
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, ''  , Documentos.Carta 	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id != ''
							ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;" ); 
			
		}
	
		// DOCUMENTOS DE LA EMPRESA ID					
		if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] != 'null' || $JSON['Empresa_id'] != 'undefined') ){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '' , Documentos.Carta  	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 

							WHERE Empresa_id = %s 
							ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;", $JSON['Empresa_id']);

		}
		
		// ADMIN NO EMPRESA SELECCIONADA
		if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined') && ( $_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3 )){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '', Documentos.Carta   	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id != ''
							ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;" ); 

		}		 
		 
		// USUARIO TODAS SUS EMPRESAS
		
		if (  ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined') && $_SESSION['nivel'] == 1){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '', Documentos.Carta   	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id = %s 
							AND Documentos.Vence != '000-00-00' 
							ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;", $_SESSION['id']);

		}
		
		// USUARIO SOLO UNA DE SUS EMPRESAS

		if (  ($JSON['Empresa_id'] != 'null' || $JSON['Empresa_id'] != 'undefined') && $_SESSION['nivel'] == 1){
			$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '', Documentos.Carta   	
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE Usuarios.id = %s 
							AND Documentos.Vence != '000-00-00' 
							AND Empresa_id = %s 
							ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;", $_SESSION['id'], $JSON['Empresa_id']);

		}		

			
		 //echo $q;  
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}
		
		if (mysqli_num_rows($result)==0) {echo '{"data": [], "total": 0, "recordsTotal": 0, "recordsFiltered": 0}'; $db_login -> close(); die;	} 
		
		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo '{ "data": ' . $myJSON . '}';
		
		$db_login -> close(); die;
}

 

if( $JSON['request'] == 0.4 ){   

			$q = sprintf("SELECT *,'' FROM Empresas;" ); 


		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}

		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo $myJSON;
		
		$db_login -> close(); die;
}

if( $JSON['request'] == 0.5 ){    

			$q = sprintf("SELECT * FROM Indice ORDER by Titulo ASC;" ); 


		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}

		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo $myJSON;
		
		$db_login -> close(); die;
}

if( $JSON['request'] == 6.0 ){    

			

			include 'autocarta.php'; 
			
			if (isset($JSON['Empresa_id'])) {$EQ1 = sprintf( " AND Empresas.id = '%s' ", $JSON['Empresa_id']); } else { $EQ1 = ''; return 'Seleccione empresa';}
			
			//DETECTA EMPRESAS QUE TENGAN DOCUMENTOS VENCIDOS
			$q = sprintf("SELECT DISTINCT Empresas.id, Empresas.empresa, Empresas.direccion, Documentos.Titulo
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
							INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
							WHERE DATEDIFF( Documentos.Vence, NOW()) <= 30  
							AND Documentos.Vence != '0000-00-00'   
							%s
							ORDER BY Empresas.empresa, Documentos.Titulo ASC ;", $EQ1 ); 
							
			$q = sprintf("SELECT DISTINCT Empresas.id, Empresas.empresa, Empresas.direccion, Empresas.correo
							FROM u124132715_SYP.Documentos 
							INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
							WHERE DATEDIFF( Documentos.Vence, NOW()) <= 30  
							AND Documentos.Vence != '0000-00-00'   
							%s
							ORDER BY Empresas.empresa, Documentos.Titulo ASC ;", $EQ1 ); 

		//echo $q . "\n";
		
		//die; 
		
		$result = mysqli_query($db_login, $q);
		
		if (mysqli_num_rows($result)==0) { echo 'nunguna carta hoy'; die; };  
		
		//die;
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$CARTAS_HOY[] = $row;
		}

		$myJSON = json_encode($CARTAS_HOY, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo $myJSON . "\n"; 
		
		$db_login -> close();
		//die;
		//echo "\n"; 
		//echo "\n"; 

			foreach ($CARTAS_HOY as &$value) { 
				
				date_default_timezone_set('America/Mexico_City');
				$current_date = date('Y-m-d H:i:s');
				//echo $current_date; 
				//echo "imprimiendo carta\n" ;
				$Empresa_id = $value['id']; 
				$Empresa = sanitize_filename_c($value['empresa']); 
				$Empresa_direccion = $value['direccion']; 
				$Empresa_correo = $value['correo']; 
				 
				try{
					echo $t= sprintf("GENERANDO CARTA:\t%s-\t%s-\t\t\t%s-\t\t%s\n", $Empresa_id, $Empresa_correo, $Empresa, $Empresa_direccion);
					$CARTA_PATH = GENERAR_CARTA($Empresa_id);
					echo "\tCARTA EN:\t" . $CARTA_PATH . "\n";  
					echo "\tEMAIL:\t" . SEND_EMAIL($Empresa_id, $CARTA_PATH , $Empresa_correo) . "\n"; 
					  
				}catch (Exception $e){ 
					echo "\t" . 'ERR' . "\n";
					log_error('QRY 6.0', 'Error intentando generar y enviar cartas para: ' . $Empresa_id, '1');
				}
				
				// OJO!!!!  $CARTA_PATH viene de autocarta.php 
 
				
				//$result = mysqli_query($db_login, $q);
				//echo $q . "\n";
			}
			
		//CHECK_BAD_EMAILS(); 
		
		$db_login -> close(); die;
}


// REGRESA OBJETO DE UNA EMPRESAS
//	id
if( $JSON['request'] == 7.0 ){
			
			
			if ( isset($JSON['id']) ) { 
				$q = sprintf("SELECT * FROM u124132715_SYP.Pagos WHERE id = '%s' ORDER BY Fecha ASC ", $JSON['id']);
			} else {
				$q = sprintf("SELECT * FROM u124132715_SYP.Pagos ORDER BY Fecha ASC");
			}
			
			//echo $q;
			   
			  
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$myArr[] = $row;
				$i++;
			}
			
			$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			echo  $myJSON ;
			
					
	$db_login -> close(); die;
}

if( $JSON['request'] == 7.1 ){  
		
							

		
		// ADMIN
		if ( $_SESSION['nivel'] == 0  || $_SESSION['nivel'] == 3){
			
			// ADMIN NO EMPRESA SELECCIONADA
			if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined') ){
				$q = sprintf("SELECT *,(SELECT empresa FROM Empresas WHERE id = Cuentas.Empresa_id) AS Empresa FROM Cuentas ORDER BY Empresa_id;" );   

			}else{
			// ADMIN SOLO UNA DE SUS EMPRESAS
				$q = sprintf("SELECT *,(SELECT empresa FROM Empresas WHERE id = Cuentas.Empresa_id) AS Empresa FROM Cuentas WHERE Empresa_id = '%s';", $JSON['Empresa_id'] ); 
			}
			
		}
	
  
		
		  
		
		// USUARIO 
		if ( $_SESSION['nivel'] == 1 ){
			
			// NO HAY EMPRESA SELECCIONADA
			if (  ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined')){
				$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '', Documentos.Carta   	
								FROM u124132715_SYP.Documentos 
								INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
								INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
								INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
								WHERE Usuarios.id = %s 
								AND Documentos.Vence != '000-00-00' 
								ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;", $_SESSION['id']);

			}else{
				// USUARIO SOLO UNA DE SUS EMPRESAS
				$q = sprintf("SELECT DISTINCT Documentos.id, Empresas.empresa, Empresas.id AS Empresa_id, YEAR(Documentos.Vence) AS Ano, Documentos.Titulo, Documentos.Inicia, Documentos.Vence, DATEDIFF( Documentos.Vence, NOW()) AS Caducidad, Documentos.path, '', Documentos.Carta   	
								FROM u124132715_SYP.Documentos 
								INNER JOIN Empresas ON Empresas.id = Documentos.Empresa_id 
								INNER JOIN Asignaciones ON Asignaciones.Empresa = Empresas.id 
								INNER JOIN Usuarios ON Usuarios.id = Asignaciones.Usuario 
								WHERE Usuarios.id = %s 
								AND Documentos.Vence != '000-00-00' 
								ORDER BY Empresas.empresa, Caducidad ASC, Documentos.Titulo ASC ;", $_SESSION['id']);
			}
		}
		
		



			
		 //echo $q;   
		
		$result = mysqli_query($db_login, $q);
		
		if (mysqli_num_rows($result)==0) {echo '{"data": [], "total": 0, "recordsTotal": 0, "recordsFiltered": 0}'; $db_login -> close(); die;	} 		 
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}
		
		


		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

		echo '{ "data": ' . $myJSON . '}';


		
		$db_login -> close(); die;
}


if( $JSON['request'] == 8.0 ){  

	$q = sprintf("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'u124132715_SYP'  AND TABLE_NAME = '%s';", $JSON['tabla'] ); 
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}

		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo $myJSON;
		
		$db_login -> close(); die;
}


// GENERA DATOS DE TABLA 
if( $JSON['request'] == 8.1 ){  
		
							

		
		// ADMIN
		if ( $_SESSION['nivel'] == 0 || $_SESSION['nivel'] == 3){
			
			// ADMIN NO EMPRESA SELECCIONADA
			if ( isset($JSON['Empresa_id']) && ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined') ){
				$q = sprintf("SELECT *,(SELECT empresa FROM Empresas WHERE id = %s.Empresa_id) AS Empresa  FROM %s;", $JSON['tabla'],$JSON['tabla'] ); 

			}else{
			// ADMIN SOLO UNA DE SUS EMPRESAS
				$q = sprintf("SELECT *,(SELECT empresa FROM Empresas WHERE id = %s.Empresa_id) AS Empresa FROM %s WHERE Empresa_id = '%s';", $JSON['tabla'],$JSON['tabla'], $JSON['Empresa_id'] ); 
			}
			
		}
	
 
		// USUARIO 
		if ( $_SESSION['nivel'] == 1 ){
			
			// NO HAY EMPRESA SELECCIONADA
			if (  ($JSON['Empresa_id'] == 'null' || $JSON['Empresa_id'] == 'undefined')){


			}else{
				// USUARIO SOLO UNA DE SUS EMPRESAS

			}
		}
		
		 //echo $q . "\n";   
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//echo $row;
			//$myArr[$row['mes']] = $row; 
			//$myArr['12'] = (object)(['mes' => '12 - Diciembre', 'cantidad' => '0']);
			$myArr[] = $row;
		}
		
		if (mysqli_num_rows($result)==0) {echo '{"data": [], "total": 0, "recordsTotal": 0, "recordsFiltered": 0}'; $db_login -> close(); die;	} 

		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo '{ "data": ' . $myJSON . '}';
		
		$db_login -> close(); die;
}

// ASOCIA PAGO
if( $JSON['request'] == 8.2 ){  

		$q = sprintf("DELETE FROM u124132715_SYP.Cuentas WHERE Pago_id = '%s' ", $JSON['id'] );
		echo $q . "\n";
		$result = mysqli_query($db_login, $q);
		
		

		$q = sprintf("UPDATE u124132715_SYP.Pagos SET Empresa_id = '%s', Asignado = '1' WHERE id = '%s' ", $JSON['Empresa_id'],$JSON['id']);
		echo $q . "\n";
		$result = mysqli_query($db_login, $q);
		
		$q = sprintf("SELECT * FROM u124132715_SYP.Pagos WHERE id = '%s' ", $JSON['id'] );
		echo $q . "\n";
		$result = mysqli_query($db_login, $q); 
				
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$PAGO[] = $row;
		}
		//print_r($PAGO);
		
		
		$q = sprintf("INSERT INTO u124132715_SYP.Cuentas (Empresa_id, Fecha, Operacion, Monto, Pago_id) VALUES ('%s','%s','%s','%s','%s')", $PAGO[0]['Empresa_id'], $PAGO[0]['Fecha'], 'Abono', $PAGO[0]['Monto']*1,$PAGO[0]['id'] ); 
		echo $q . "\n";
		$result = mysqli_query($db_login, $q);



		$db_login -> close(); die;
}


// DES-ASOCIA PAGO
//	id
if( $JSON['request'] == 8.3 ){


			$q = sprintf("SELECT * FROM u124132715_SYP.Cuentas WHERE id = '%s' ", $JSON['id']);
			echo $q . "\n";	  
			$result = mysqli_query($db_login, $q);
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					$CUENTA[] = $row;
				}
				
			IF ( $CUENTA[0]['Pago_id'] != '' ) { // SE ASEGURA DE BORRAR UNICAMENTE SI ES ABONO
			
				$q = sprintf("UPDATE u124132715_SYP.Pagos SET Empresa_id = '', Asignado = '0' WHERE id = '%s' ", $CUENTA[0]['Pago_id']); 
				echo $q . "\n";
				$result = mysqli_query($db_login, $q);
				
				
				$q = sprintf("DELETE FROM u124132715_SYP.Cuentas WHERE id = '%s' ", $JSON['id']);
				echo $q . "\n";
				$result = mysqli_query($db_login, $q);
				
				echo "Abono des-asociado";
			
				$db_login -> close(); die;
			
			}else{
			
		
				$q = sprintf("DELETE FROM u124132715_SYP.Cuentas WHERE id = '%s' ", $JSON['id']);
				//echo $q . "\n";
				$result = mysqli_query($db_login, $q);
				
				echo "Cargo eliminado";
			
				$db_login -> close(); die;
				
			}
				

}

  

// 
//	id
if( $JSON['request'] == 8.4 ){
		
	if ($JSON['id'] != '') {
				
		// BORRA LOS REGISTRO ANTERIORES DE ESE CARGO
		$q = sprintf("DELETE  FROM u124132715_SYP.Cuentas WHERE Cargo_id = '%s'", $JSON['id'] );
		//echo $q . "\n";
		$result = mysqli_query($db_login, $q); 
		
	}
		
		// FECHA
		date_default_timezone_set('America/Mexico_City');
		$current_date = date('Y-m-d H:i:s');
		$current_ts = date('Y-m-d', strtotime($current_date . ' -1 hour')); 
		//$current_ts = date('Y-m-01', strtotime($current_date . ' -0 hour')); 
		
		//print_r($JSON);
		
		// CARGO INICIAL CON TODOS LOS DATOS
		$q = sprintf("INSERT INTO u124132715_SYP.Cuentas (Empresa_id, Fecha, Operacion, Precio, Concepto, Anticipo, Mensualidades, Inicia) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s')", $JSON['Empresa_id'], $JSON['Fecha'], 'Precio', $JSON['Monto']*1, $JSON['Concepto'],$JSON['Anticipo'],$JSON['Mensualidades'],$JSON['Inicia'] );
		//echo $q . "\n";
		$result = mysqli_query($db_login, $q); 
		
		$INSERTED_ID = mysqli_insert_id($db_login);
		//echo 'ID: ' . $INSERTED_ID . "\n";
		
		// ACTUALIZA CARGO INICIAL CON SU ID 
		$q = sprintf("UPDATE u124132715_SYP.Cuentas SET Cargo_id = '%s', Concepto = '%s' WHERE id = '%s'",$INSERTED_ID, $JSON['Concepto'], $INSERTED_ID);
		$result = mysqli_query($db_login, $q); 
		
		// ANTICIPO
		$q = sprintf("INSERT INTO u124132715_SYP.Cuentas (Empresa_id, Fecha, Operacion, Monto, Concepto, Cargo_id) VALUES ('%s','%s','%s','%s','    %s','%s')", 
		$JSON['Empresa_id'], $JSON['Fecha'], 'Abono', $JSON['Anticipo'],'Anticipo', $INSERTED_ID );
		//echo $q . "\n"; 
		$result = mysqli_query($db_login, $q);
		
		// MENSUALIDADES 
				
		for ($x = 0; $x < $JSON['Mensualidades']; $x++) {
			 
			$FECHA_MENSUALIDAD = date('Y-m-05', strtotime(sprintf($JSON['inicia'] . ' +%s month',$x)));  
			
			$q = sprintf("INSERT INTO u124132715_SYP.Cuentas (Empresa_id, Fecha, Operacion, Parcialidad, Concepto, Cargo_id) VALUES ('%s','%s','%s','%s','    %s','%s')", 
			$JSON['Empresa_id'], $FECHA_MENSUALIDAD, 'Parcialidad' , ($JSON['Monto']-$JSON['Anticipo'])/$JSON['Mensualidades']*1 ,sprintf('Parcialidad %s/%s',$x+1,$JSON['Mensualidades']), $INSERTED_ID );
			//echo $q . "\n";
			$result = mysqli_query($db_login, $q);
			
		}
		
		echo 'Cargo agregado.'; 
 
		$db_login -> close(); die;
		
		
		
}


//	id
if( $JSON['request'] == 8.41 ){ 
		
		
		// FECHA
		date_default_timezone_set('America/Mexico_City');
		$current_date = date('Y-m-d H:i:s');
		$current_ts = date('Y-m-d', strtotime($current_date . ' -1 hour')); 
		//$current_ts = date('Y-m-01', strtotime($current_date . ' -0 hour')); 
		
		//print_r($JSON);
		
		$q = sprintf("INSERT INTO u124132715_SYP.Pagos (Fecha, Monto, Referencia) VALUES ( '%s','%s','%s') ", $JSON['Fecha'], $JSON['Monto'], 'Manual');
		
		//echo $q . "\n";
		$result = mysqli_query($db_login, $q); 
		
		$INSERTED_ID = mysqli_insert_id($db_login);
		//echo 'ID: ' . $INSERTED_ID . "\n";
		
		
		echo 'Abono agregado.'; 
 
		$db_login -> close(); die;
		
 
		
}


// ACTUALIZA CARGO MODAL
if( $JSON['request'] == 8.5 ){ 
	
		$q = sprintf("SELECT * FROM u124132715_SYP.Cuentas WHERE id = '%s'", $JSON['id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$myArr[] = $row;
		}
		
		$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		echo $myJSON; 
		
		$db_login -> close(); die;
}

// CREA LIGA DE PAGO
if( $JSON['request'] == 9 ){ 


		
		$q = sprintf("SELECT * FROM u124132715_SYP.Cuentas WHERE id = '%s'", $JSON['id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$myArr[] = $row;
		}
		
		
		$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'", $myArr[0]['Empresa_id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$EMPRESA[] = $row;
		}
		
		//$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		//echo $myJSON; 
		
		//print_r($myJSON);
		
		$db_login -> close();
		
		//require ('CREAR_LIGA.php');
		require (dirname(__DIR__).'/PHP/CREAR_LIGA.php');
		
		$jsonArray['url'] = trim(preg_replace('/\s\s+/', ' ', $jsonArray['url']));
		
		echo $jsonArray['url'];
		
		$db_login -> close(); die;
}

// CREA ESTADO DE CUENTA
if( $JSON['request'] == 10 ){ 


		
		$q = sprintf("SELECT * FROM u124132715_SYP.Cuentas WHERE id = '%s'", $JSON['id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$myArr[] = $row;
		}
		
		
		$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'", $myArr[0]['Empresa_id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$EMPRESA[] = $row;
		}
		
		//$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		//echo $myJSON; 
		
		//print_r($myJSON);
		
		$db_login -> close();
		
		$JSON['Empresa_id'] = $JSON['id'];
		//require ('CREAR_LIGA.php');
		require (dirname(__DIR__).'/PHP/WP_EDO_CTA.php');
		
		echo $CARTA_PATH;
		
		//echo 'Recibo creado.' 
		
		$db_login -> close(); die;
}

// CREA RECIBO
if( $JSON['request'] == 11 ){ 


		
		$q = sprintf("SELECT * FROM u124132715_SYP.Cuentas WHERE id = '%s'", $JSON['id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$myArr[] = $row;
		}
		
		
		$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'", $myArr[0]['Empresa_id'] );
		//echo $q . "\n";
		
		$result = mysqli_query($db_login, $q);
		
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$EMPRESA[] = $row;
		}
		
		//$myJSON = json_encode($myArr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		//echo $myJSON; 
		
		//print_r($myJSON);
		
		$db_login -> close();
		
		$JSON['Empresa_id'] = $JSON['id'];
		//require ('CREAR_LIGA.php');
		require (dirname(__DIR__).'/PHP/WP_RECIBO.php');
		
		
		
		
		echo $CARTA_PATH;
		
		if ($JSON['WA_ENVIAR'] == '1' ) {
			
			
		
		
			$JSON['CHAT_ID'] = sprintf('521%s@c.us',$EMPRESA[0]['telefono']) ;   //'14167682436@c.us';
			$JSON['WA_TXT'] = 'MENSAJE';
			$JSON['WA_FILE_URL'] = $CARTA_PATH;
			$JSON['WA_FILE_NAME'] = str_replace('https://semaforo.sosmexicoriesgo.com/RECIBOS/','',$CARTA_PATH);
			
			include 'WP_WA.php';
			$RESP = WA_SEND_FILE($JSON['CHAT_ID'], $CARTA_PATH, $JSON['WA_FILE_NAME']);
			
			//$JSON['WA_request'] = 2; 
			//require (dirname(__DIR__).'/PHP/WP_WA.php');
			
			//echo 'Recibo creado.' 
		}
		
		$db_login -> close(); die;
}

if( $JSON['request'] == 12 ){ 

			include 'WP_WA.php';
			
			
	 
			$JSON['CHAT_ID'] = sprintf('%s@c.us',$JSON['TELEFONO']) ;   //'14167682436@c.us';  
			
			$JSON['CODIGO'] = GEN_CODE();
			
			$JSON['WA_TXT'] = $JSON['CODIGO'];
			
			
			$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE user ='%s'", $JSON['TELEFONO'] );
			$result = mysqli_query($db_login, $q);
			
			if (mysqli_num_rows($result) == 0) {
				$q = sprintf("INSERT INTO u124132715_SYP.Usuarios (user , codigo, nivel, telefono) VALUES ('%s', '%s', '%s', '%s')", $JSON['TELEFONO'], $JSON['CODIGO'], '1' , $JSON['TELEFONO']);
				$result = mysqli_query($db_login, $q); 
				//echo $q;
			}else{
				$q = sprintf("UPDATE u124132715_SYP.Usuarios SET codigo = '%s'  WHERE user  = '%s' ", $JSON['CODIGO'], $JSON['TELEFONO']);
				$result = mysqli_query($db_login, $q);
				//echo $q;
			}
			
			$db_login -> close(); 
			
			//while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			//	$EMPRESA[] = $row;
			//}
			

			
			$RESP = WA_SEND_TXT($JSON['CHAT_ID'], $JSON['CODIGO']);
			
			//print_r ( $RESP); 
			echo json_encode($RESP,true); 
	
			die;
}



if( $JSON['request'] == 12.11 ){ 

			$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE user ='%s' AND pwd <> ''", $JSON['TELEFONO'] );
			$result = mysqli_query($db_login, $q); 
			
			if (mysqli_num_rows($result) != 0) { 
				 
				echo '<label> Usuario existente </label>' . ' ';    

			}
			
	$db_login -> close(); die; 
}

if( $JSON['request'] == 12.12 ){ 

			$q = sprintf("SELECT * FROM u124132715_SYP.Usuarios WHERE user ='%s' ", $JSON['TELEFONO'] );
			$result = mysqli_query($db_login, $q);
			
			//echo $q;
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$USUARIO[] = $row;
			}
			//print_r($USUARIO);
			//print $JSON['CODE'];
			if ( $USUARIO[0]['codigo'] ==  $JSON['CODE'] ) {
				
				echo '1';
			}
			
	$db_login -> close(); die; 
}

if( $JSON['request'] == 12.13 ){ 

			
			$q = sprintf("UPDATE u124132715_SYP.Usuarios SET pwd = '%s' WHERE user = '%s'", '', $JSON['TELEFONO'] );
			$result = mysqli_query($db_login, $q); 
			//echo $q; 
			if (mysqli_num_rows($result) != 0) { }
				  
				echo 'Contrasena borrada. ' . "\n".'Complete nuevamente los pasos 1, 2 y 3 ';      

			
			
	$db_login -> close(); die; 
}


if( $JSON['request'] == 13 ){ 

			
		$E_ID = $JSON['Empresa_id'];


			$q = sprintf("SELECT * FROM u124132715_SYP.Empresas WHERE id = '%s'", $E_ID);
			$result = mysqli_query($db_login, $q);

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$EMPRESA[] = $row;
				
			} 

			$q = sprintf("SELECT DISTINCT(Carta) FROM u124132715_SYP.Cartas WHERE Empresa = '%s' ORDER BY id ASC", $E_ID); 
			$result = mysqli_query($db_login, $q);

			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$CARTAS[] = $row;
				 
			} 

			$db_login -> close(); 


			$zip = new ZipArchive();

			$EMPRESA[0]['empresa'] = preg_replace( '/[^A-Za-z0-9-_\. ]/', '', $EMPRESA[0]['empresa'] );
			$FILE_NAME= sprintf("%s - %s.zip", $E_ID, $EMPRESA[0]['empresa']); 

			if(file_exists($_SERVER['DOCUMENT_ROOT']."/tmp/".$FILE_NAME)) {

					unlink ($_SERVER['DOCUMENT_ROOT']."/tmp/".$FILE_NAME); 

			}
			if ($zip->open($_SERVER['DOCUMENT_ROOT']."/tmp/".$FILE_NAME, ZIPARCHIVE::CREATE) != TRUE) {
					die ("Could not open archive");
			}
				

			 

			 
			foreach ($CARTAS as &$value) {
				
				

				$CARTA_NAME = substr($value['Carta'], strpos($value['Carta'], "CARTAS/")+7);
				$CARTA_PATH_OK = '../CARTAS/' . $CARTA_NAME;
				$zip->addFile($CARTA_PATH_OK,$CARTA_NAME);
				  
				 

				//echo $CARTA_NAME . "\t"  . file_exists($CARTA_PATH_OK) . "\n";   

			}
				
			$zip->close(); 
			
			echo 'https://semaforo.sosmexicoriesgo.com/' . "tmp/". $FILE_NAME; 
			
			die;
}








echo "request invalido \n";
print_r($JSON);
print_r($DATOS);
exit;



 

function CHECK__RETURN_EMPTY_TABLE($result){
		
		if (mysqli_num_rows($result)==0) {				
				echo '{"data": [], "total": 0, "recordsTotal": 0, "recordsFiltered": 0}'; 
							
				$db_login -> close(); die;
		} 
}

function GEN_CODE($length = 4) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

$RETURN = '?';
// Function to recursively add prefix with index to files
function FIND_FILE($dir, $name) {
	
    // Open the directory
    if ($handle = opendir($dir)) {
        // Iterate through items in the directory
        while (false !== ($entry = readdir($handle))) {
            // Skip the special entries "." and ".."
            if ($entry != "." && $entry != "..") {
                $fullPath = $dir . DIRECTORY_SEPARATOR . $entry;

                // If it's a directory, recursively call the function
                if (is_dir($fullPath)) { 

                     FIND_FILE($fullPath, $name);

					//flush();
                } else {
                    // Otherwise, it's a file, so add the prefix and index, then rename
					

					//echo $fullPath . "\n";
					if ( match_wildcard($name, $entry) ) {  $GLOBALS['RETURN'] = [$fullPath, $entry]; return $GLOBALS['RETURN'] . "\n"; }
					flush();

                }
            } 
        }
        closedir($handle);
		//return $RETURN . '';
    }
	if ($GLOBALS['RETURN'] == null){ return '' ;}
	return $GLOBALS['RETURN'];
}


function match_wildcard( $wildcard_pattern, $haystack ) {
   $regex = str_replace(
     array("\*", "\?"), // wildcard chars
     array('.*','.'),   // regexp chars
     preg_quote($wildcard_pattern, '/')
   );

   return preg_match('/^'.$regex.'$/is', $haystack);
}
	
/*
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$RESULTADOS['phone'] = $row['phone'];
				$RESULTADOS['modulo'] = $row['modulo'];

			}
			
				$q = sprintf("INSERT INTO u124132715_SYP.Asignaciones (Usuario,Empresa) VALUES ('%s','%s')", $JSON['usuario'], $value );
				$q = sprintf("UPDATE u124132715_curso.resultados SET resultado = '%s', calificacion = '%s', status = '%s'  WHERE id = '%s' ", $JSON['resultado'],$JSON['calificacion'],$JSON['status'],$JSON['id']);
				$q = sprintf("SELECT * FROM u124132715_curso.users WHERE phone = '%s'  ", $RESULTADOS['phone']);
				$result = mysqli_query($db_login, $q);
				
				
			
			echo $RESULTADOS['phone'] . "\r\n";
			
			$q = sprintf("SELECT * FROM u124132715_curso.users WHERE phone = '%s'  ", $RESULTADOS['phone']);
			   //echo $q;
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$PERFIL['user'] = $row['user'];
				$PERFIL['email'] = $row['email'];

			}

			$q = sprintf("UPDATE u124132715_curso.resultados SET resultado = '%s', calificacion = '%s', status = '%s'  WHERE id = '%s' ", $JSON['resultado'],$JSON['calificacion'],$JSON['status'],$JSON['id']);

			$fetch = mysqli_query($db_login, $q);

			$q = sprintf("SELECT titulo FROM modulos WHERE modulo_ID = '%s'  ", $RESULTADOS['modulo']);
			   //echo $q;
			$result = mysqli_query($db_login, $q);
			
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$modulos_data[] = $row;
			}
				
*/





?>   