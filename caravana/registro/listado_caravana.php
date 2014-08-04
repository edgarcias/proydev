<?php
session_start();//Habilitamos uso de variables de sesi�n

//Incluimos cabecera
include('../../inc/header.php');

//Incluimos modelos a usar
include_once($_SESSION['model_path'].'caravana.php');


//municipios
/*$sql='SELECT CVE_MUN, NOM_MUN FROM cat_municipio WHERE CVE_ENT = 14';
$municipio = $db->query($sql);

//localidad
$sql = 'SELECT CVE_LOC, NOM_LOC FROM `cat_localidad` where CVE_ENT = 14';    
$localidad = $db->query($sql);     */

//Variable de respuesta
$respuesta = intval($_GET['r']);

//Mensaje respuesta
list($mensaje,$class) = Permiso::mensajeRespuesta($respuesta);

//Valores de la b�squeda
$descripcion=$_GET['descripcion'];
$fecha_instalacion=$_GET['fecha_instalacion'];
$status=$_GET['status'];


//print_r($_GET);
//Listamos los programas del beneficiario
list($lista,$p) = caravana::listaCaravana($descripcion,
                                          $fecha_instalacion,
                                          $status
                                          );      

//Obtenemos acciones del men�
$central = Permiso::arregloMenu(substr(basename(__file__),0,-4),'center');

?>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/filtro.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/valida.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>combobox.js"></script>

<script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(function() {
    $("table").tablesorter({widgets: ['zebra']});
});
</script>

<div id="principal">

   <div id="contenido">
    <h2 class="centro">Caravanas</h2>

    <?php if($respuesta > 0){?>
    
    <div class="mensaje <?php echo $clase; ?>"><?php echo $mensaje;?></div>
    
    <?php } ?>

    <div class="centro">       
        <div  align="center">
      <?php 
        
        //Verificamos si tiene permiso de alta de caravana
        if(Permiso::accesoAccion('alta_caravana', 'registro', 'caravana')){ ?>
        <table>
            <tr>
              <td colspan="6" style="text-align:center;">
              <form action="../../caravana/registro/alta_caravana.php">
              <input type="submit" id="agregar"  value="Agregar Nueva Caravana" />
              </form>
              </td>
            </tr>
        </table>

        <?php } ?>   

            <form id='formbusqueda' method="get" action='listado_caravana.php'>
            <fieldset>
            <table>
            <legend>
                   <label>
                     Busqueda  
                   </label>  
            </legend>

            <tr>
                <td class="centro" colspan="6">
                <label>Formulario de B&uacute;squeda</label>
                </td>                
            </tr>            

            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>

            <tr>
              <td colspan="2">
                <label for="descripcion">Descripci&oacute;n</label>
              </td>
              <td colspan="2">
                <label for="fecha_instalacion">Fecha de Instalaci&oacute;n</label>
              </td>
               <td colspan="2">
                <label for="status">Estatus</label>
              </td>              
            </tr>

            <tr>          
              <td colspan="2">
                <input type = 'text' id = 'descripcion' name = 'descripcion' class="direccion"/>
              </td>
              <td colspan="2">
                <input type = 'text' id = 'fecha_instalacion' name = 'fecha_instalacion' class="fecha date"/>
              </td>              
              <td>
                 <input type = 'text' id='status' name='status' class="nom_num"/>
              </td>
              <td colspan="6">
                <input type="submit" id="boton"  value="Buscar" />
              </td>
            </tr>              
          </table> 
          </fieldset>           

          </form>                              
        </div>
    <div>

    <?php if($lista == NULL && count($_GET)){?>

    <div class="mensaje">
        No se encontraron resultados con el criterio de b&uacute;squeda seleccionado
    </div>
    
    <?php } elseif($lista !=null){

    echo $p->display();
    ?>
    </p>

    <table class="tablesorter">
    <thead>
        <th>Descripci&oacute;n</th>
        <th>Fecha de Instalaci&oacute;n</th>
        <th>Estatus</th>
        <th>Acci&oacute;n</th>
    </thead>

    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['descripcion']; ?></td>
            <td><?php echo $l['fecha_instalacion']; ?></td>
            <td><?php echo $l['estatus'];?></td>
            <td>

                <?php if($l['activo']==1){ ?>
                
                <?php if(array_key_exists('edita_caravana',$central)){ ?>
                <div title="Edita Caravana" class="ui-state-default ui-corner-all lista">
                  <a class="ui-icon ui-icon-circle-triangle-e" href="edita_caravana.php?id_edicion=<?php echo $l['id']; ?>"></a>
                </div>
                <?php } ?>
                
                <?php }?>

                <?php if(array_key_exists('activa_caravana',$central)){ ?>
                <div title="<?php echo ($l['activo'] == 1)? 'Eliminar' : 'Activar' ?>" class="ui-state-default ui-corner-all lista">
                    <a class="confirmation ui-icon ui-icon-<?php echo ($l['activo'] == 1)? 'closethick' : 'check'  ?>"
                       title="&iquest;Seguro de <?php echo ($l['activo'] == 1)? 'eliminar' : 'activar' ?> caravana?" 
                       href="activa_caravana.php?id_activo=<?php echo $l['id']; ?>"></a>
                </div>                
                <?php } ?>

                <div title="Edita Caravana" class="ui-state-default ui-corner-all lista">
                  
                </div>
                <!--
                <a class="confirmation"  href="activa_beneficiario.php?id_activo=<?php //echo $l['id']; ?>">
                 -->

                  <?php /* if($l['activo'] == 1){

                    echo 'Eliminar';

                }else if($l['activo'] == 0){

                    echo 'Activar';

                }*/?>

                <!--
                </a>
                 -->

            </td>
        </tr>

        <?php endforeach; ?>

    </tbody>
    </table>
    <?php } ?>
    </div>

  </div>
 </div>
</div>

<?php 
//Incluimos pie
include($_SESSION['inc_path'].'/footer.php');
?>