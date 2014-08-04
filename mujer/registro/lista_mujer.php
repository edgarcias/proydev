<?php
session_start();//Habilitamos uso de variables de sesi�n

 //set headers to NOT cache a page
header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
header("Pragma: no-cache"); //HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past


//Incluimos cabecera
include('../../inc/header.php'); 
//Incluimos modelo 'Acci�n'
include_once($_SESSION['model_path'].'mujeres_avanzando.php');

//Valores de la b�squeda
$id_caravana=$_GET['id_caravana'];
$tipo_filtro=$_GET['tipo_filtro'];
$busqueda=$_GET['busqueda'];
$respuesta=$_GET['r'];
$id_dif=$_GET['id_dif'];

//Obtenemos listado de acciones
list($lista,$p) = mujeresAvanzando::listaMujer($busqueda,
                                               $tipo_filtro,
                                               NULL,
                                               null,
                                               null,
                                               null,
                                               null,
                                               NULL,
                                               $id_caravana);

//imprimos respuesta en caso de enviarse
if($respuesta !=null){
    list($mensaje,$clase) = Permiso::mensajeRespuesta($respuesta);

    //Se guard� exitosamente, mostramos la id_dif/curp generada
    if($respuesta == 1 && $id_dif){
        //Vemos si fue generada la curp
        $es_generada = substr($id_dif, 0,2);
        //Obtenemos curp
        $curp = substr($id_dif, 2);

        $mensaje .= ($es_generada == "SI")? '. ID DIF: '.$curp : '. CURP: '.$curp;

    }

}

//si la lista nula enviamos mensaje de que no hay registro en la busqueda
if($lista == NULL && $respuesta == NULL){
    //No existen registros
    list($mensaje,$respuesta) = Permiso::mensajeRespuesta(8);
    $respuesta = 1;
}
$db->where ('activo', 1);
$caravanas = $db->get('caravana');

//Obtenemos acciones del men�
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

//Guardamos �ltima b�squeda
if(strlen($_SERVER['QUERY_STRING']) > 5){
    $_SESSION['last_search'] = $_SERVER['QUERY_STRING'];
}else{
    $_SESSION['last_search'] = '';
}
?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">
    <div id="contenido">
        <div class="centro">
            
            <div  align="center">
            <h2 class="centro">Listado de Mujeres</h2>
            
            <?php if ($lista !=null){ ?>
            <h3 class="centro"> Resultados Encontrados: <?php echo count($lista);?></h3> 
            <?php }?>

            <div style="float: right;">
                <form action="lista_mujer.php">
                    <input type="submit" value="REINICIAR"/>
                </form>
                <!-- 
                <input style="float: right;" type="button" onclick="javascript:history.back(-1)" value="REGRESAR"/>
                -->                
            </div>            
                        
            <form id='formbusqueda' method="get" action='lista_mujer.php'>
            <table>
            <tr>
              <td>
                <label for="tipo_filtro"> Buscar Por: </label>
              </td>
              <td>
                <select id="tipo_filtro" name="tipo_filtro">
                       <option value="nombre">Nombre</option> 
                       <option value="folio">Folio</option>
                       <option value="calle">Calle</option>                                        
                </select>
              </td>
              <td><label for="busqueda"> Palabra Clave</label></td>
              <td><input type = 'text' id = 'busqueda' name = 'busqueda'/><td>&nbsp;</td>
              <td><label for="busqueda">Caravana</label></td>
              <td>
                    <select id="id_caravana" name="id_caravana">
                    <option value="">Seleccione Caravana</option>
                    <?php foreach($caravanas as $c): 
                    $selected = ($c['id'] == $id_caravana )? 'selected' : ''; 
                    ?>
                        <option value='<?php echo $c['id'] ?>'  <?php echo $selected;?> > 
                            <?php echo $c['descripcion'];?>
                        </option>
                    <?php endforeach; ?>                       
                    </select>
                </td>
              <td><input type="submit" id="boton"  value="Buscar" /></td></td>
            </tr>
            
            </table>
            </form>
            </div>
        </div>
                                
    <?php if($respuesta > 0){ ?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
            
    <?php } ?>
     
    <div id="page_list" align="center">        
    <p>
        <?php if(array_key_exists('alta_mujer',$central)){ ?>
        <a  id = 'enviar' href="alta_mujer.php">Agregar</a>
        <?php } ?>
    </p> 
    <p>
    
    <?php
    //Si tenemos listado
    if($lista != NULL){                
        // Listado de p�ginas del paginador
        echo $p->display();
    ?>
    </p>
    <table class="tablesorter">
    <thead>
        <th>Nombres</th>
        <th>Estado</th>
        <th>Municipio</th>
        <th>Localidad</th>
        <!--  <th>Escolaridad</th> -->
        <!--<th>Ocupacion</th>-->
        <!-- <th>Estado Civil</th> -->
        <th>Fecha de Nacimiento</th>
        <th>Caravana</th>
        <!-- <th>G&eacute;nero</th> -->
        <!-- <th>Pasaporte</th> -->        
        <!--<th>Activo</th>-->
        <!-- <th>Indigena</th> -->
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['nombre_completo']; ?></td>
            <td><?php echo $l['estado']; ?></td>
            <td><?php echo $l['municipio'];?></td>
            <td><?php echo $l['localidad'];?></td>
            <!-- <td><?php //echo $l['n_escolaridad'];?></td> -->
            <!--<td><?php //echo $l['ocupacion'];?></td>-->
            <!-- <td><?php //echo $l['estado_civil'];?></td> -->
            <td><?php echo $l['fecha_nacimiento'];?></td>
            <td><?php echo $l['nom_caravana'];?></td>
            <!-- <td><?php //echo $l['genero'];?></td> -->
            <!--  <td><?php //echo $l['pasaporte'];?></td> -->
            <!-- <td><?php //echo $l['es_activo'];?></td> -->
            <!-- <td><?php //echo $l['indigena'];?></td> -->            
            <td>
             <?php if($l['activo']==1){ ?>
                <?php if(array_key_exists('edita_mujer',$central)){ ?>
            
                <div title="Editar" class="ui-state-default ui-corner-all lista">                
                    <a class="ui-icon ui-icon-note" href="edita_mujer.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>
                                            
                <!-- 
                <div>            
                    <a href="edita_mujer.php?id_edicion=<?php //echo $l['id']; ?>">Editar</a>
                </div>
                 -->
            
                <?php } ?>
             <?php }?> 

                <?php if(array_key_exists('activa_mujer',$central)){ ?>                
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">                
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> beneficiaria?" 
                       href="activa_mujer.php?id_activo=<?php echo $l['id']; ?>"></a>
                </div>
                <?php } ?>
                
                <?php if(array_key_exists('asigna_serv_mujer',$central)){ ?>                
                <div title="Agregar Servicio" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon ui-icon-circle-triangle-e" href="asigna_serv_mujer.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>                
                <?php } ?>
                
                <?php if(array_key_exists('seguimiento_mujer',$central)){ ?>
                <div title="Expediente de la Mujer" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon  ui-icon-document" href="seguimiento_mujer.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>                
                <?php } ?>
                 
                <?php if(array_key_exists('credencial',$central)){ ?>                
                <div id="<?php echo $l['folio']; ?>" title="Cartilla" class="ui-state-default carrito ui-corner-all lista">
                  <a class="ui-icon ui-icon-plus"></a>
                </div>                
                <?php } ?>
                                  
            </td>
        </tr>

        <?php endforeach; ?>      
       
    </tbody>
    
    </table>
    <div>
    </div>
    <div id="tbl_beneficiarias">
    <?php include_once($_SESSION['inc_path'].'/mujer/cartilla_mujer.php') ?>
    </div>
	<div id="photo" class="centro"></div>	
    <?php } ?>
        </div>
     </div>  
       </div>       
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>

