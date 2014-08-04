<?php session_start();     

        

    //Siempre obtendremos los m�dulos activos

    $db->where('activo',1);

    $modulos = $db->get('modulo');

    

    //Siempre obtendremos los subm�dulos activos

    $db->where('activo',1);

    $submodulos = $db->get('submodulo');

    

    //Si obtenemos los estatus

    $estatus = $db->get('estatus');

    

    //Si editamos el registro

    if(intval($id_edicion)>0){

        //Obtenemos el registro del m�dulo

        $db->where('id',$id_edicion);

        $accion = $db->getOne('accion');

        

        //Obtenemos el subm�dulo para editar el m�dulo ligado

        $id_submodulo = intval($accion['id_submodulo']);

        

        if($id_submodulo > 0){

        //Obtenemos el subm�dulo para editar el m�dulo y subm�dulo ligado

        $db->where('activo',1);

        $db->where('id',$id_submodulo);

        $mod = $db->getOne('submodulo');

        

        //Filtramos los subm�dulos

        $db->where('activo',1);

        $db->where('id_modulo',$mod['id_modulo']);

        $submodulos = $db->get('submodulo');

                    

        }

    }        

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/filtro.js"></script>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/<?php echo $_SESSION['module_name']?>/valida.js"></script>



	<form id='formAcc' method="post" action='save_accion.php'>

	<table>

    <tr>

        <td>

            <label for="id_modulo">M&oacute;dulo Padre</label>

        </td>

        <td>

            <select id="id_modulo" name="id_modulo">

                <option value='0'>Seleccione M&oacute;dulo</option>

                    <?php foreach($modulos as $m): 

                        if($m['id'] == $mod['id_modulo']){

                            $selected = "selected";

                        }else{

                            $selected = "";

                        }

                    ?>



                <option value='<?php echo $m['id'] ?>' <?php echo $selected;?> > 

                        <?php echo $m['descripcion'];?></option>

                    <?php endforeach; ?>

            </select>

        </td>

    </tr>



    <tr>

        <td>

            <label for="id_submodulo">Subm&oacute;dulo</label>

        </td>    

        <td id="submodulo">

                <select id="id_submodulo" name="id_submodulo">

                    <option value='0'>Seleccione Subm&oacute;dulo</option>

                    <?php foreach( $submodulos as $s): 

                        if($s['id'] == $accion['id_submodulo']){

                            $selected = "selected";

                        }else{

                            $selected = "";

                        }

                    ?>                



                    <option value='<?php echo $s['id'] ?>' <?php echo $selected;?> > 

                        <?php echo $s['descripcion'];?></option>

                    <?php endforeach; ?>

                </select>



            </td>

        </tr>



        <tr>

			<td>

                <label for="nombre">Nombre de la Acci&oacute;n</label>

            </td>

			<td>

                <input type="hidden" name="id_edicion" value="<?php echo $id_edicion; ?>" />

                <input type="hidden" name="id_accion" value="<?php echo $accion['id']; ?>" />

                <input type = 'text' id = 'nombre' name = 'nombre' value="<?php echo $accion['nombre']; ?>" />

            </td>

		</tr>

           <tr>

      <td>

        <label for="orden">Orden</label>

      </td>

      <td>

        <input type = 'text' class="digits" id = 'orden' name = 'ordent' class="nomnum"value="<?php echo $accion['orden']; ?>" />

      </td>

    </tr>

          <tr>

          <td>

             <label for="descripcion">Descripci&oacute;n </label>

          </td> 

          <td>

                 <textarea id='descripcion' name = 'descripcion'  class="nomnum" cols="50" rows="5" ><?php echo $accion['descripcion']; ?></textarea>

          </td>

      </tr>



        <tr>

      <?php if($id_edicion > 0) { ?>



       

            <td>

                <label for="activo">Estatus</label>

            </td>

            <td>

                <select id="activo" name="activo">

                    <option value="">Seleccione</option>

                    <?php foreach($estatus as $e){                         

                        if($e['valor'] == $accion['activo']){

                            $selected = "selected";

                        }else{

                            $selected = "";

                        }

                    ?>                

                    <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > <?php echo $e['nombre'];?></option>

                    <?php } ?>

                </select>

            </td>

              

       <?php } ?>

        

        </tr>

        <!-- 

        

        <tr>

            <td>Ver en men&uacute;</td>

            <td>

                <select id="mostrar_menu" name="mostrar_menu">

                    <option value="">Seleccione</option>

                    <?php /* foreach($estatus as $e){                         

                        if($e['valor'] == $accion['mostrar_menu']){

                            $selected = "selected";

                        }else{

                            $selected = "";

                        }

                    ?>                

                    <option value='<?php echo $e['valor'] ?>' <?php echo $selected;?> > <?php echo $e['nombre'];?></option>

                    <?php } */ ?>

                </select>

            </td>

        </tr>

        

         -->        



      	<tr>

			<td>&nbsp;</td>

			<td><input type = 'submit' id = 'enviar' value = 'Enviar' /></td>

		</tr>



	</table>

	</form>