<?php
session_start();//Habilitamos uso de variables de sesi�n
//Incluimos cabecera
include('../../inc/header.php');
//Variable de respuesta
$respuesta = intval($_GET['r']);
//Mensaje respuesta
$mensaje = Permiso::mensajeRespuesta($respuesta);
; 
?>
<div id="principal">
   <div id="contenido">
    <h2 class="centro">Alta de Acciones</h2>
    
    <?php if($respuesta > 0){?>
    
    <div class="mensaje"><?php echo $mensaje;?></div>
    
    <?php } ?>
    
	<div  align="center">                
        <?php
            //Si el registro no es exitoso mostramos el formulario de usuario 
            if($respuesta != 1){        
                include_once("form_accion.php");    
            }
        ?>
    </div>
    </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>