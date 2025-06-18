<?php
    
include_once($_SERVER['DOCUMENT_ROOT'] . '/PHP/MYF1.php');
    


setlocale(LC_ALL, 'en_US.UTF-8');
header('Content-type: text/javascript; charset=utf-8');
    

require (dirname(__DIR__).'/vendor/autoload.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHP_PLUGINS/PHPMailer/Exception.php';
require '../PHP_PLUGINS/PHPMailer/PHPMailer.php';
require '../PHP_PLUGINS/PHPMailer/SMTP.php';



$entityBody = file_get_contents('php://input');
$BODY_OB = json_decode($entityBody, true);        //$BODY_OB = json_decode($BODY_EN, true);

$DATA = $BODY_OB;   
$KEYS = array_keys(json_decode($DATA , true)); 
$DATA_STR = json_encode($DATA , true);



$ROWS = '';

foreach($DATA as $SELECCION){
    
    $DATOS = json_decode($SELECCION['Datos'],true);
    
    $DATOS['T1'] = DISH_ID_2_NAME($DATOS['T1']);
    $DATOS['T2'] = DISH_ID_2_NAME($DATOS['T2']);
    $DATOS['T3'] = DISH_ID_2_NAME($DATOS['T3']);
    $DATOS['T4'] = DISH_ID_2_NAME($DATOS['T4']);
    
    //$BODY .= $SELECCION['NOMBRE'] . '<br>' . ;
    $ROWS .= <<<BX
            <tr>
                <td>{$SELECCION['NOMBRE']}</td>
                <td>{$DATOS['T1']}</td>
                <td>{$DATOS['T2']}</td>
                <td>{$DATOS['T3']}</td>
                <td>{$DATOS['T4']}</td>
                <td>{$DATOS['ALERGIAS']}</td>
                <td>
                    <a href="https://salypimientabanquetes.com/{$SELECCION['E_ID']}">Editar
                    <img src="https://salypimientabanquetes.com/IO/edit.png" class="icon">
                    </a>
                </td>
                
            </tr>
        BX;
    
}

$BODY = <<<TEXT
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    
    <style>
    
        html{
            font-size: 10px;
        }

            
        body {
                font-family: 'Google Sans', Arial, sans-serif;
                font-family: "Darker Grotesque",'Google Sans', Arial, sans-serif;
                display: inline;
                justify-content: center;
                align-items: center;
                
               
                margin: 0;
                
        }
        span{
            font-family: "Darker Grotesque",'Google Sans', Arial, sans-serif;
            
            
        }
        .CONTENEDOR{

            justify-content: center;
            align-items: center;
            width: 70%;
            height: 100%;
            margin-top: 1px;
            margin-left: 3px;
        
        }
        table {

        }
        td {
            padding: 6px;
            text-align: center;
            
            color: #3c4043;
            border-bottom: 1px solid #dadce0;
            font-family: "Darker Grotesque",'Google Sans', Arial, sans-serif;
            background-color: white;
            color: #5e74b2;
        }
        thead {

            font-weight: bold;
            font-family: "Darker Grotesque",'Google Sans', Arial, sans-serif;
        }
        tbody tr:hover {
            
        }
        th {
            
            
            text-align: center;

            color: #ffffff;
            min-width: 100px;
        }

        table tr :first-child {
          width: 150px;
         
        }
        
        .TH_TEXT{

            text-wrap-mode: nowrap;
            text-align: center;

            color: #5e74b2;
            min-width: 100px;
            background-color: #5e74b238;
            
            border-radius: 16px;
            text-align: center;

            justify-content: center;
            
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .TH_ICON{
            display: flex;
            justify-content: center;
            align-items: center;
        
        }
        
        
        .icon {
            width: 16px !important;
            height: auto; 
            vertical-align: middle; 
            margin-right: 5px;
            color: #5e74b2;
        }     
        
        .card{

        
        }
        
        
    </style>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
</head>
<body>

    <span>
        Estimado comensal,
        <br>
        <br>
        <br>
        Hemos recibido tus selecciones de platillos.
        <br>
        <br>
        A continuación, te presentamos un resumen de los platillos que elegiste:
    </span>
    <br>
    <br>
    <br>
    <br>
    <div class="CONTENEDOR">
    
        <div class="card" style="">

            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/user.png" class="icon"></div><div class="TH_TEXT">Nombre</div></th>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/1.png" class="icon"></div><div class="TH_TEXT">Tiempo 1</div></th>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/2.png" class="icon"></div><div class="TH_TEXT">Tiempo 2</div></th>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/3.png" class="icon"></div><div class="TH_TEXT">Tiempo 3</div></th>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/4.png" class="icon"></div><div class="TH_TEXT">Tiempo 4</div></th>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/allergy.png" class="icon"></div><div class="TH_TEXT">Alergias</div></th>
                            <th><div class="TH_ICON"><img src="https://salypimientabanquetes.com/IO/edit.png" class="icon"></div><div class="TH_TEXT">Editar</div></th>
                        </tr>
                    </thead>
                    <tbody>
                      {$ROWS}
                    </tbody>
                </table>   
            </div>
        </div>
    </div>
    
    <br>
    <br>
    <br>
    <span>Villas del Lago <br> 
                    San Agustin 220 <br>
                    CP 62374 <br>
                    Cuenavaca Morelos <br>
                    México<br>
                    Tel. 777 494 0810
                    </span>
</body>
</html>
TEXT;



//return json_encode($DATA); 

//echo $BODY;

//SEND_EMAIL( 'cear83@gmail.com', $BODY);
SEND_EMAIL( $SELECCION['EMAIL'], $BODY);
    
//$RESP['TXT_LOGIN_USER'] = $DATA['TXT_LOGIN_USER'];
//$RESP['SESSION']         = $_SESSION;

//echo json_encode($RESP); 

//die;

return;
    




function SEND_EMAIL( $TO, $BODY){
    
    //$TO = 'sosmex@hotmail.com';
    //$TO = 'cear83@gmail.com';
    
    //global $Empresa_correo;
    //$TO = $Empresa_correo;
    
    //if ( $Empresa_id == null || $CARTA_PATH == null || $TO == null ){log_error('SEND_EMAIL()', 'FALTA DATO', '1'); echo "ERROR"; return false;} 
    
    try {    
        echo 'Enviando' . "\n";
    
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8'; 
            echo '.' . "\n";
        //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.hostinger.com';                   //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'selecciones@salypimientabanquetes.com';     //SMTP username
            $mail->Password   = 'Salypimienta1!';                            //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            echo '.' . "\n";
            
        //Recipients
            $mail->setFrom('selecciones@salypimientabanquetes.com', "Seleccion de Platillos" );
            $mail->addAddress($TO);     //Add a recipient
            $mail->addReplyTo('selecciones@salypimientabanquetes.com');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');
            echo '.' . "\n";
        //Attachments
            //$mail->addAttachment($CARTA_PATH);         //Add attachments
            //$mail->addAttachment($CARTA_PATH, $name);    //Optional name
            echo '.' . "\n";
        //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Seleccion de Platillos';
            
            $DIRECCION = sprintf('');
            
            $BODY = sprintf('%s <br><br><br> %s', $BODY, $DIRECCION);
            
            $mail->Body    = $BODY ; 
            
            echo '.' . "\n";

            $mail->send(); 
            echo "" . 'Message has been sent to: ' . $TO . "\n";
            

        
        //echo 'EMAIL OK';
        return true;
        
    } catch (Exception $e) {
        
        echo "Error al enviar correo a: {$TO} \n" . '{$mail->ErrorInfo}';
        
        //log_error('EMAIL', "Error al enviar correo a: {$TO} \n" . '{$mail->ErrorInfo}', '1'); 
        return false; 
    }

    
}
    
    
?>