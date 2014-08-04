<?php 
    /*
    //Listado con CSS 
    //Obtenemos el nombre del m�dulo actual y el id del usuario logueado
    $nombre_modulo = $_SESSION['module_name'];
    $id_usuario = $_SESSION['usr_id'];

    //Obtenemos listado de subm�dulos que tiene el usuario
    $submodulos = Permiso::getSubmodulos($nombre_modulo,$id_usuario);        
    
    if($submodulos){ ?>
    <!--
    <div id="menu">
        <?php /*
        //Mostramos el listado de subm�dulos que tiene acceso
        foreach($submodulos as $s): ?>
        <ul>
           <li class="nivel1 ">
              <a class="nivel1" href="#"><?php echo $s['descripcion_submodulo']; ?></a>
            <ul class="nivel2">
                <?php                
                //Obtenemos listado de acciones que tiene el usuario
                //$acciones = Permiso::getAcciones($s['id_submodulo'],$id_usuario,false);
                $acciones = Permiso::listaMenuAcciones('HEADER',null,$s['id_submodulo']);
                
                if($acciones){ 
                            //Mostramos listado de acciones
                            foreach($acciones as $a):?>
                            <li>
                                <a href="<?php echo $_SESSION['module_path'].$s['nombre_submodulo'].

                                '/'.$a['nombre_accion'].'.php' ?>" > 

                                    <?php echo $a['descripcion_accion'] ?>
                                </a>               
                            </li>
                            <?php endforeach; ?>
                <?php } ?>
            </ul>
        </li>
        </ul>
        <?php endforeach; *//*?>
        </div>-->
    <!-- 
    <table style="margin: 0 auto;">
    <tr>
    <td>
    
    </td>    
    
    </tr>    
    </table>
     -->
        
<?php } */?>

<?php 
    //Listado con jquery ui
    //Obtenemos el nombre del m�dulo actual y el id del usuario logueado
    $nombre_modulo = $_SESSION['module_name'];
    $id_usuario = $_SESSION['usr_id'];

    //Obtenemos listado de m�dulos que tiene el usuario
    $modulos = Permiso::getModulos($id_usuario);

?>

<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/jmenu.jquery.min.js"></script>
<link href="<?php echo $_SESSION['css_path']?>jmenu/jmenu.css" rel="stylesheet" type="text/css"/>


<!--
<script lang="JavaScript" type="text/javascript" src="<?php /* echo $_SESSION['js_path']?>/hoverIntent.js"></script>
<script lang="JavaScript" type="text/javascript" src="<?php echo $_SESSION['js_path']?>/superfish.js"></script>
<link href="<?php echo $_SESSION['css_path']*/?>superfish/superfish.css" rel="stylesheet" type="text/css"/>
-->

<style>
.ui-menu { width: 150px; }
/*
#menu2{
overflow: hidden;
}
#menu2 ul{
overflow: visible !important;
}
#menu2 > li {
float: left;
display: block;
width: 140px; !important;
}
#menu2 ul li {
display:block;
float:none;
width: 140px; !important;
}
#menu2 ul li ul {
left:140px !important;
width:140px;
}
#menu2 ul li ul li {
width:140px;
}
#menu2 > li {
margin: 5px 5px !important;
padding: 0 0 !important;
}
#menu2 > li > a {
float: left;
display: block;
clear: both;
overflow: hidden;
}
#menu2 ul .ui-menu-icon {
margin-top: 0.3em !important;
}
#menu2 ul, #menu2 li {
float: left;
display: block;
}
*/
</style>    

<div class="bloque_menu">
<ul id="menu2" class="sf-menu">    
<?php 
    //Recorremos m�dulos
    foreach ($modulos as $m):?>
    <li><a href="<?php echo $_SESSION['app_path_p'].$m['nombre_modulo'].'/ini/index.php'?>
                    "><?php echo $m['descripcion_modulo'];?></a>
    <ul>
    <?php 
        //Obtenemos listado de subm�dulos que tiene el usuario
        $submodulos = Permiso::getSubmodulos($m['nombre_modulo'],$id_usuario);        
        $tot_submodulos = count($submodulos); 

        //Mostramos el listado de subm�dulos que tiene acceso
        foreach($submodulos as $s): 

            //Obtenemos listado de acciones que tiene el usuario
            //$acciones = Permiso::getAcciones($s['id_submodulo'],$id_usuario,false);
            $acciones = Permiso::listaMenuAcciones('HEADER',null,$s['id_submodulo']);

            //Armamos el enlace del subm�dulo
            $enlace = $_SESSION['app_path_p'].$m['nombre_modulo'].
                                    '/'.$s['nombre_submodulo'].
                                    '/';

            //Ponemos el listado como la acci�n predeterminada, en caso de no existir
            //listados la primer acci�n del subm�dulo como enlace inmediato
            if($acciones){  
                
                $accion = NULL;
                $needle='lista';

                foreach ($acciones as $key => $value):                
                    $haystack = $value['nombre_accion'];
                    $r = strpos($haystack,$needle);

                    if($r !==false){
                       $accion = $value['nombre_accion'].'.php';
                        break;                    
                    }

                endforeach;                        

                $enlace .= ($accion != NULL)? $accion : $acciones[0]['nombre_accion'].'.php';
            } 

            ?>
            
            <?php 
            //Imprimimos los m�dulos, si son m�s de 1
            if($tot_submodulos > 1){ ?>
                <li>
                    <a href="<?php echo $enlace ?>"><?php echo $s['descripcion_submodulo']; ?></a>
                    <ul>

                    <?php } ?>    

                    <?php                                    
                    
                    if($acciones){ 
                                //print_r($acciones);
                                //Mostramos listado de acciones
                                foreach($acciones as $a):?>
                                
                                <li>
                                    <a href="
                                    <?php echo $_SESSION['app_path_p'].$m['nombre_modulo'].
                                    '/'.$s['nombre_submodulo'].
                                    '/'.$a['nombre_accion'].'.php' ?>" > 

                                        <?php echo $a['descripcion_accion'] ?>
                                    </a>               
                                </li>                        
                                <?php endforeach; ?>
                    <?php } ?>

                <?php 
                //Imprimimos los m�dulos, si son m�s de 1
                if($tot_submodulos > 1){ ?>

                    </ul>
                </li>

                <?php } ?>

    <?php endforeach; ?>

        </ul>        
    </li>
<?php endforeach;?>
</div>

<script>
    $(function() {
        //$( "#menu2" ).menu({position: {at: "top"}});
        //$( "#menu2" ).superfish();
        
        $("#menu2").jMenu({
                    openClick : false,
                    ulWidth :'auto',
                     TimeBeforeOpening : 100,
                    TimeBeforeClosing : 11,
                    animatedText : false,
                    paddingLeft: 1,
                    effects : {
                        effectSpeedOpen : 150,
                        effectSpeedClose : 150,
                        effectTypeOpen : 'slide',
                        effectTypeClose : 'slide',
                        effectOpen : 'swing',
                        effectClose : 'swing'
                    }

                });        
                
    });
    </script>