<?php

    //Obtenemos acciones del men�
    $central = Permiso::arregloMenu('lista_beneficiario_pys','center');

    //Si tenemos listado
    if($lista){?>

    <p>   
    <?php // Listado de p�ginas del paginador
        echo $p->display();?>
    </p>

    <script lang="javascript" type="text/javascript" src="<?php echo $_SESSION['js_path'];?>jquery.tablesorter.min.js"></script>
    <script type="text/javascript">
    $(function() {
    $("table").tablesorter({widgets: ['zebra']});
    });
    </script>

    <table class="tablesorter">
    <thead>
        <th>CURP</th>
        <th>Nombre</th>
        <th>Fecha de Nacimiento</th>
        <th>Municipio</th>
        <th>Localidad</th>
        <th>Programas y Servicios</th>
        <th>Acci&oacute;n</th>
    </thead>
    <tbody>
        <?php foreach($lista as $l): ?>
        <tr>
            <td><?php echo $l['curp']; ?></td>
            <td><?php echo $l['nombre_completo'];?></td>
            <td><?php echo $l['fecha_nacimiento']; ?></td>
            <td><?php echo $l['municipio']; ?></td>
            <td><?php echo $l['localidad']; ?></td>
            <td>
                <?php 
                //Obtenemos listado de servicios
                $listaPys = Beneficiario_pys::listaPysBeneficiario($l['id']);  
                if($listaPys != NULL){ ?>

                <table class="tablesorter">
                <thead>
                    <th>Programa</th>
                    <th>Servicio</th>
                </thead>
                <tbody>
                <?php foreach($listaPys as $li): ?>
                <tr>
                    <td><?php echo $li['nombre_componente']; ?></td>
                    <td><?php echo $li['nombre_pys']; ?></td>                                    
                </tr>

                <?php endforeach; ?>      
                </tbody>
                </table> 
                <?php }else{ ?>
                No cuenta con programas asignados
                <?php } ?>
            </td>
            <td>
                <div title="Agregar/Quitar Servicio" class="ui-state-default ui-corner-all lista">
                    <?php if(array_key_exists('pys_beneficiario',$central)){ ?>
                    <a class="ui-icon ui-icon-circle-triangle-e" href="pys_beneficiario.php?id_edicion=<?php echo $l['id']; ?>"></a>
                    <?php } ?>                  
                </div>
                <!-- 
                <a class="confirmation"  href="activa_beneficiario.php?id_activo=<?php //echo $l['id']; ?>">
                 -->
            </td>
        </tr>
        <?php endforeach; ?>      
    </tbody>
    </table>        
    <?php }else{
        echo 'Beneficiario sin programas';
        } ?>