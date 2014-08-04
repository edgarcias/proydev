<?php

session_start();//Habilitamos uso de variables de sesi�n



//Si enviamos el pa�s

if(isset($_POST['id_pais'])){

    

    //Obtenemos conexi�n

    include ($_SESSION['inc_path'] . "conecta.php");

    

    //Guardamos variable de pa�s

    $pais = $_POST['id_pais'];

    

    $sql = 'SELECT CVE_ENT, NOM_ENT from cat_estado WHERE ? ';

    $params = array(1);

    

    //Si el pa�s no es M�xico, s�lo ponemos la opci�n de OTRO

    if($pais != 90){

        $sql .= ' AND CVE_ENT = ?';

        $params[] = 33;

    }

    

    //Ejecutamos sentencia

    $estados = $db->rawQuery($sql,$params);

       

}else{

    exit;

}

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?><?php echo $_SESSION['module_name']?>/combobox.js"></script>

<select class="combobox" id="id_cat_estado" name="id_cat_estado">

    <option value=''>Seleccione Estado</option>

    <?php foreach($estados as $l):?>                

    <option value='<?php echo $l['CVE_ENT'] ?>' <?php echo $selected;?> > <?php echo $l['NOM_ENT'];?></option>

    <?php endforeach; ?>

</select>                