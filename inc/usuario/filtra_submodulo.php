<?php
session_start();//Habilitamos uso de variables de sesi�n

if(isset($_POST['id_modulo'])){
    
    //Obtenemos conexi�n
    include ($_SESSION['inc_path'] . "conecta.php");
    
    //Obtenemos el m�dulo
    $id_modulo = $_POST["id_modulo"];
    
    //Siempre obtendremos los m�dulos del id_submodulo ligado
    $db->where('activo',1);
    $db->where('id_modulo',$id_modulo);
    $submodulos = $db->get('submodulo');
       
}else{
    exit;
}
?>

<select id="id_submodulo" name="id_submodulo">
    <option value='0'>Seleccione Subm&oacute;dulo</option>
    <?php foreach($submodulos as $s): 
            if($s['id'] == $accion['id_submodulo']){
                $selected = "selected";
            }else{
                $selected = "";
            }?>                
    <option value='<?php echo $s['id'] ?>' <?php echo $selected;?> > 
        <?php echo $s['descripcion'];?></option>
    <?php endforeach; ?>
</select>