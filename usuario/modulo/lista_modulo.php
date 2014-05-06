<?php
session_start();//Habilitamos uso de variables de sesi�n

//Incluimos cabecera
include_once('../../inc/header.php');    
$tipo_filtro=$_GET['tipo_filtro'];
$busqueda=$_GET['busqueda'];
$respuesta=$_GET['r'];

//Incluimos modelo 'M�dulo'
include_once($_SESSION['model_path'].'modulo.php');

//Obtenemos listado de m�dulos
list($lista,$p) = Modulo::listaModulos($busqueda,$tipo_filtro);
if($respuesta !=null){
    $mensaje = Permiso::mensajeRespuesta($respuesta);
}
if($lista == NULL){
   //No existen registros
   $mensaje = Permiso::mensajeRespuesta(8);
}

//Obtenemos acciones del men�
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

?>

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
        <form id='formbusqueda' method="GET" action='lista_modulo.php'>        
        <table>
        <tr>
         <td>
            <label for="tipo_filtro"> Buscar Por:</label>
         </td>

            <td>

                <select id="tipo_filtro" name="tipo_filtro">
                    <option value="nombre">Nombre</option> 
                    <option value="descripcion">Descripcion</option> 
                    
                   

                </select>

            </td>
            <td><label for="busqueda"> Palabra Clave</label></td>
            <td><input type = 'text' id = 'busqueda' name = 'busqueda'/><td>&nbsp;</td>
            <td><input type="submit" id="boton"  value="Buscar" /></td></td>
        </tr>
        </table>
        </form>
        </div>    	
    </div>
  <h2 class="centro">Listado de M&oacute;dulos</h2>
  
     <?php if($respuesta > 0){?>
    
    <div class="mensaje"><?php echo $mensaje;?></div>
            
                            
     <?php } ?>
    
    <div id="page_list" align="center">
    <p>        
        <?php if(array_key_exists('alta_modulo',$central)){ ?>
        <p><a  href="alta_modulo.php">Agregar</a></p>
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
        <th>Nombre</th>
        <th>Descripci&oacute;n</th>
        <th>Estatus</th>
        <th>Creado por</th>
        <th>Fecha Creado</th>
        <th>Fecha Modif.</th>
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
    <?php foreach($lista as $l): ?>
    <tr>
        <td><?php echo $l['nombre']; ?></td>
        <td><?php echo $l['descripcion']; ?></td>
        <td><?php echo $l['estatus'];?></td>
        <td><?php echo $l['usuario'];?></td>
        <td><?php echo $l['fecha_creado'];?></td>
        <td><?php echo $l['fecha_mod'];?></td>
        <td>
            <?php if($l['activo']==1){ ?>
            <?php if(array_key_exists('edita_modulo',$central)){ ?>
            
            <div title="Editar" class="ui-state-default ui-corner-all lista">
               <a class="ui-icon ui-icon-note" href="edita_modulo.php?id_edicion=<?php echo $l['id']; ?>">Editar</a>
            </div>
            
            <?php } ?>
            <?php } ?>
            <?php if(array_key_exists('activa_modulo',$central)){ ?>
            <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">
                <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> acci&oacute;n?" 
                       href="activa_modulo.php?id_activo=<?php echo $l['id']; ?>"></a>
                  
            </div>
            <?php } ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>   
    <?php } ?>     
    </div>
  </div>  
</div>
<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>