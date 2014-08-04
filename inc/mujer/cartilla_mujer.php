<?php
session_start();  

    if(!isset($db)){
        //Librer�a de conexi�n
        include($_SESSION['inc_path']."conecta.php");
    }    

    //Incluimos librer�a 'Art�culos' para obtener todo lo relacionado al servicio
    include_once($_SESSION['inc_path'].'libs/cartilla_carrito.php');

    //Incluimos librer�a de permiso
    include_once($_SESSION['inc_path'].'libs/Permiso.php');
    include_once($_SESSION['inc_path'].'libs/Fechas.php');
    include_once($_SESSION['model_path'].'mujeres_avanzando.php');

    //Variable de mensaje
    $msg = "";
    $mensaje = "";
    
    //ruta de imagen
    $ruta = $_SESSION['img_path']."mujeres/";   

    //ruta raiz
    $ruta_raiz = $_SESSION['app_path_r'].'img'.$_SESSION['DS'].'mujeres'.$_SESSION['DS']; 

    /*
    echo $_POST["accion"];
    echo $_POST["id"];
    */

    //Verificamos que se nos env�en las variables necesarias
    if($_POST["accion"]){

        //Recibimos variables
        $accion = $_POST["accion"]; 
        $id = $_POST["id"];
       
 
      
        //Dependiendo la acci�n, buscamos la funci�n
        switch($accion){

            case 'agregar':
                        $msg = agregarArticulo($id);
                        break;

            case 'eliminar':
                        $msg = eliminarArticulo($id);
                        break;

            case 'listado':
                        $msg = listadoArticulo($ID_C_SERVICIO);
                        break;
            case 'vaciar':
                        $msg = vaciarCarrito();
                        break;
        }

    }else{

        //$mensaje = "No se seleccion� ninguna acci�n";

    }    

    //Si obtenemos un c�digo de mensaje
    if($msg){
        //Obtenemos mensaje y clase
        list($mensaje,$clase) = Permiso::mensajeRespuesta($msg);
    }    

    //Agregamos art�culos al carrito de art�culos
    function agregarArticulo($id_articulo = 0){

    //Preparamos variables

        $A = "";

        if (!$_SESSION['arrayArt']){
            $A = new Cartilla();
            //echo ("Instancia");
        } else {
            $A = unserialize($_SESSION['arrayArt']);
            //echo ("Deserializar");
        }

        //Si recibimos un art�culo
        if($id_articulo){

            //Agregamos 1 art�culo
            $mensaje = $A->agregar($id_articulo);                

            //Si obtenemos mensaje de error en el carrito, lo mostramos  
            if($mensaje){
                return $mensaje;
            }else{
                //No hubo error, serializamos el objeto y mostramos mensaje de agregado
                $_SESSION['arrayArt'] = serialize($A);
                //$mensaje = 'Servicio agregado';   
            }

        }else{
            //No se agreg&oacute; a la beneficiar&iacute;a, seleccione uno
            $mensaje = 25;
        }                   

        return $mensaje;              

    }

    //Eliminamos art�culos del carrito    
    function eliminarArticulo($posicion){

        //Quitamos de cada arreglo el valor que corresponde con el $id, quitando 1 producto en total
        if ($A = unserialize($_SESSION['arrayArt'])) {

            $A->dilete($posicion);

            /*Si todav�a tenemos un art�culo, serializamos el objeto, 
            caso contrario, eliminamos la variable de sesi�n*/            
            if (count($A->articulo_id)) {
               $_SESSION['arrayArt'] = serialize($A);
            } else{
                unset($_SESSION['arrayArt']);
            }                
            
            //Beneficiaria descartada
            $mensaje = 26;

        } else {
            
            //Error con el arreglo
            $mensaje = 27;

        }

        return $mensaje;

    }

    //Vaciamos Carrito
    function vaciarCarrito(){
        unset($_SESSION['arrayArt']);
        return 28;
    }

?>



<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>
<style type="text/css">
    .foto_cred{
        /*position: absolute;*/
        display: block;
        top: 1.6cm;
        left: 0.74cm;
        width: 2.37cm;
        height: 2.75cm;                
    }
</style>

<?php if($mensaje){ ?>
<div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
<?php } ?>

<?php
    
    if($_SESSION['arrayArt']){

        //Obtenemos el carrito
        $articulos = unserialize($_SESSION['arrayArt']);

        /*Si el objeto tiene elementos, mostramos la tabla, caso contrario
        notificamos que no hay art�culos por mostrar*/
        if(isset($articulos)&& isset($articulos->articulo_id)){
    ?>
 <h2 class="centro">Impresi&oacute;n De Fichas Para Cartilla</h2>
 <h2 class="centro">
    <button id="borra_cartilla">Vaciar Listado</button>
    </h2>
<table style="font-size:14pt;" class="tablesorter">             
    <thead> 
        <tr>
            <th>Foto</th>
            <th style="width: 50px;">&nbsp;</th>
            <th>Beneficiaria</th>
            <th>Acci&oacute;n</th> 
        </tr> 
    </thead>
    <tbody>
    <?php foreach($articulos->articulo_id as $key => $value):
       
       $mujer = mujeresAvanzando::get_by_id(null,$value);
    
        //Verificamos si la imagen existe, de no estarlo ponemos imagen default
       $ruta_imagen = (file_exists($ruta_raiz.$mujer['folio'].".png"))? $ruta.$mujer['folio'].".png?i=". filemtime($ruta_raiz.$mujer['folio'].'.png') : $ruta."default.png";              

    ?>
        <tr class="zebra"> 
            <td width="95">
                <div class="foto_cred" 
                style="background: url(<?php echo $ruta_imagen; ?>) center center ; background-size: auto 100%;">
                </div>
            </td>
            <td>
              <button id="<?php echo $value; ?>" class="foto">Tomar Foto</button>
            </td>
            <td>
                <?php $calle = $mujer['calle'].' No. '.$mujer['num_ext']; ?>
                <?php echo '<B>NOMBRE:</B> '.$mujer['nombres'];?>
                <?php echo '<br><B>FECHA DE NACIMIENTO:</B> '.Fechas::fechacorta('/',Fechas::fechaymdAdmy($mujer['fecha_nacimiento'])).' ('. $mujer['edad'].' a&ntilde;os)';?>
                <?php //echo '<B>DOMICILIO:</B> '.$mujer['calle'].' '.$mujer['num_ext'].' '.'INTERIOR '.$mujer['num_int'].'<br>';?>
                <?php echo '<br><B>DOMICILIO:</B> '.$calle .= (isset($mujer['num_int']) && (strtoupper($mujer['num_int']) != 'S/N') )? ' INTERIOR '.$mujer['num_int'] : '';?>
                <?php echo '<br><B>COLONIA:</B> '.$mujer['colonia'];?>
                <?php echo '<br><B>MUNICIPIO:</B> '.$mujer['NOM_MUN'];?>
                <?php echo '<br><B>C&Oacute;DIGO POSTAL:</B> '.$mujer['CODIGO'];?>
                <?php echo '<br><B>TELEFONO:</B> '.$mujer['telefono'].'<BR>';?>
                <a  href="edita_mujer.php?id_edicion=<?php echo $mujer['id']; ?>"><b>[Modificar Datos]</b></a>
                
            </td>
            <td>
              <input id="elimina_art" type="button" name="<?php echo $key;?>" value="Quitar Del Listado" /><br />
                             
                    
              
                </div>
            </td>
            <td>
              
            </td>
        </tr>
        
           
        
      <?php endforeach;?>
    </tbody>
</table>
<button id="vista">Imprimir fichas seleccionadas</button>   

<?php }else{

    echo "No existen servicios guardados";

} ?>

<?php } ?>