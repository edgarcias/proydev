<?php
session_start();//Habilitamos uso de variables de sesi�n

//Obtenemos el tipo de edici�n
if(isset($_GET["id_edicion"])){
    $id_edicion = intval($_GET["id_edicion"]);
}

//Obtenemos id de edici�n del expediente
if(isset($_GET["id_edicion_exp"])){
    $id_edicion_exp = intval($_GET["id_edicion_exp"]);
}

//Obtenemos el tipo de edici�n
if(isset($_GET["id_aspirante"])){
    $id_aspirante = intval($_GET["id_aspirante"]);
}

//Incluimos cabecera
include($_SESSION['inc_path']. 'header.php');  
//Variable de respuesta
$respuesta = intval($_GET['r']);

//Mensaje respuesta
$mensaje = Permiso::mensajeRespuesta($respuesta);
  
?>
<div id="principal">
   <div id="contenido">
    <h2 class="centro">Edici&oacute;n de Beneficiarios</h2>
    <?php if($respuesta > 0){?>
    
    <div class="mensaje"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
	<div  align="center">                
        <?php
            //Si el registro no es exitoso mostramos el formulario de usuario 
            if($respuesta != 1){        
                include_once("form_beneficiario.php");    
            }
        ?>
    </div>
        </div>
    </div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>