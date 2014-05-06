<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Menus
 * **/ 

//Inclumos librer�a Paginador
include_once($_SESSION['inc_path'].'libs/Paginador.php');


class Menu extends MysqliDb{

    public function __construct(){}

    /**
     * Ejecutamos sentencia sql con par�metros
     * @param string $sql Sentencia SQL
     * @param array $params Cada uno de los par�metros de la sentencia
     * 
     * @return int Resultado
     * */

    private static function executar($sql,$params){
        //Ejecutamos
        $resultado = self::getInstance()->rawQuery($sql, $params);
        
        //Regresamos resultado
        return $resultado;        
    }

    /**
     * Cambiamos el estatus del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */
    public static function activaMenu($id_menu){
        
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `menu` where id = ?'; 
        
        //Par�metro(s) para la edici�n
        $params = array($id_menu);

        //Verificamos el estatus del usuario        
        $registro = self::executar($sql,$params);
        $registro = $registro[0];

        //Si el registro tiene estatus de 'Eliminado', se activar�
        if($registro['activo'] == 0){
            $estatus = 1;
        }else if($registro['activo'] == 1){
        //Si el registro tiene estatus de 'Activo', se eliminar�
            $estatus = 0;
        }

        //Preparamos update
        self::getInstance()->where('id',$id_menu);                                                
        //datos a actualizar
        $updateData = array('activo' => $estatus);

        //Iniciamos transacci�n
        self::getInstance()->begin_transaction();

        if(!self::getInstance()->update('menu',$updateData)){
            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3; 
            
            //Cancelamos los posibles campos afectados
            self::getInstance()->rollback(); 
            
        }else{
            //Campos guardados correctamente
            $msg_no = 1;                    
            
            //Guardamos campos afectados en la tabla
            self::getInstance()->commit(); 
        } 

        return $msg_no;
    } 

    /**
     * Obtenemos listado de los usuarios. Predeterminadamente mostramos
     * en ambos estatus (activos e inactivos)
     * @param string $busqueda 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaMenus($busqueda = NULL,$tipo_filtro=NULL,$activo = NULL){

        $sql = '
        SELECT
        ms.*,
        u.usuario,
        e.nombre as estatus
        FROM `menu` ms
        left join usuario u on u.id = ms.id_usuario_creador
        left join estatus e on e.valor = ms.activo
        WHERE ?
        ';

        //Par�metros de la sentencia
        $params = array(1);
         //Filtro de b�squeda
       if ($busqueda !==NULL && $tipo_filtro !==NULL){
           
           //switch($tipo_filtro){
               
               //case :
                $sql .= ' AND ms.'.$tipo_filtro.' LIKE ? ';
                $params[] = '%'.$busqueda.'%';        
                   //break;
           //}
                                               
       }

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND ms.activo = ?';
            $params[] = $activo;
        }

        //Regresamos resultado
         return Paginador::paginar($sql,$params);           
    }

    /**
     * Guardamos registro en la tabla Menus
     * @param array $menu Arreglo con los campos a guardar
     * @param int $id del menu a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveMenu($menu,$id = null)
    {
        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;
        
        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        /*Obtenemos cada una de las variables enviadas v�a POST 
        y las asignamos a su respectiva variable. Por ejemplo:
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

        foreach($menu as $key => $ms):
        ${$key} = self::getInstance()->real_escape_string($ms);
        endforeach;
        
        //Evitamos duplicidad de nombres en los registros        
        $sql='SELECT id FROM `menu` where nombre = ? ';
        $params = array($nombre);
        
        //S�lo si se edita el mismo registro puede 'repetir el nombre'
        if($id !=null){
            $sql.=' and id not in (?)';
            $params[] = $id;
            
        }
                                
        //Ejecutamos sentencia
        $duplicado = self::getInstance()->rawQuery($sql,$params);
        
        //Verificamos que no haya nombre duplicado
        if(count($duplicado)>0){
            $msg_no = 6;
            //Nombre duplicado
        }else{            
                //Obtenemos el id del usuario creador
                $id_usuario_creador = $_SESSION['usr_id'];
                
                /*Si no esta creada la variable activo 
                predeterminadamente la guardamos = 1*/        
                if(!isset($activo) ){
                    $activo = 1 ;            
                }        
                
                //Campos obligatorios
                if ($nombre){
                    $insertData = array(
                    'nombre' => mb_strtoupper($nombre),
                    'activo' => mb_strtoupper($activo),
                    'fecha_creado' => date('Y-m-d H:i:s'),
                    'id_usuario_creador' => $id_usuario_creador
                    );
                    
                    //Quitamos del arreglo los valores vac�os
                    $insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));
                    
                    //Si recibimos id para editar
                    if(intval($id)>0){
                        //Indicamos que haremos un update
                        $accion = 'update';
    
                        //Agregamos condici�n para indicar qu� id se actualiza
                        self::getInstance()->where('id',$id);                                        
                    }
                    
                    //Iniciamos transacci�n
                    self::getInstance()->begin_transaction();
                    
                    if(! self::getInstance()->{$accion}('menu', $insertData)){
                        //'Error al guardar, NO se guardo el registro'
                        $msg_no = 3; 
                        
                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();
                    }else{
                        //Campos guardados correctamente
                        $msg_no = 1;
                        
                        //Guardamos campos afectados en la tabla
                        self::getInstance()->commit(); 
                    } 
                                           
                }else{
                    //'Campos Incompletos'
                    $msg_no = 2;             
                }
                
            }                        
        //Regresamos mensaje
        return $msg_no;
    }
}