<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Usuario
 * **/ 
//Inclumos librer�a MysqliDb
include_once($_SESSION['inc_path'].'libs/Paginador.php');

class UsuarioGrupo extends MysqliDb
{

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
     * Cambiamos el estatus del grupo del usuario 
     * 1 = Activo, 0 = Inactivo
     * @param int $id_usuario_grupo a actualizar
     * 
     * @return string $msg_no No. de Mensaje a regresar
     * */ 
    public static function activaUsuarioGrupo($id_usuario_grupo){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Variable donde guardamos el estatus
        $estatus = 0;

        //Sentencia para obtener el campo activo de la tabla usuario
        $sql = 'SELECT activo from `usuario_grupo` where id = ?'; 

        $params = array($id_usuario_grupo);

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
        self::getInstance()->where('id',$id_usuario_grupo);
                                                        
        //datos a actualizar
        $updateData = array('activo' => $estatus);
        
        //Iniciamos transacci�n
        self::getInstance()->startTransaction();

        if(!self::getInstance()->update('usuario_grupo',$updateData)){
            //'Error al guardar, NO se guardo el registro'
            $msg_no = 3; 
            
            //Cancelamos los posibles campos afectados
            self::getInstance()->rollback(); 
            
        }else{
            //Campos guardados correctamente
            $msg_no = 1;    
            
            //Guardamos los campos afectados en la tabla
            self::getInstance()->commit();                
        } 
        return $msg_no;
    } 


    /**
     * Obtenemos listado de los grupos que NO tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaFiltraGrupo($id_usuario,$activo = NULL){

        $sql = '        
        SELECT id, nombre FROM `grupo` where id not in (        
            SELECT
            g.id 
            FROM `usuario_grupo` ug
            inner join usuario u on u.id = ug.id_usuario and u.activo = 1
            inner join  grupo g on g.id= ug.id_grupo and g.activo = 1
            where ug.activo= 1 and ug.id_usuario= ? group by 1
        )';        

        //Par�metros de la sentencia
        $params = array($id_usuario);

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND activo = ?';
            $params[] = $activo;
        }

        //Regresamos resultado
        return self::executar($sql,$params);           
    }

    /**
     * Creamos consulta y par�metros delos grupos que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Consulta y par�metros de la consulta
     * */
    private static function listadoGruposUsuario($id_usuario,$activo = NULL){
        
        $sql = 
        'SELECT
         g.nombre,
         g.id
         FROM `usuario_grupo` ug
         inner join usuario u on u.id = ug.id_usuario and u.activo = 1
         inner join  grupo g on g.id= ug.id_grupo and g.activo = 1
         where ug.id_usuario = ? ';

        //Par�metros de la sentencia
        $params = array($id_usuario);

        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND ug.activo = ?';
            $params[] = $activo;
        }

        return array($sql,$params);

    }

     /**
     * Obtenemos listado (arreglo) de los grupos que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Listado de grupos de un usuario en un arreglo
     * */
    public static function listUsuarioGrupo($id_usuario,$activo = NULL){

        //Obtenemos consulta y par�metros para realizar la consulta
        list($sql,$params) = self::listadoGruposUsuario($id_usuario,$activo);

        //Regresamos resultado
        return self::executar($sql,$params);              
    }

    /**
     * Obtenemos listado PAGINADO de los grupos que tiene el usuario. 
     * Predeterminadamente mostramos en ambos estatus (activos e inactivos)
     * @param int $id_usuario Usuario del que obtenemos su lista 
     * @param int $activo Determinamos si queremos los activos, inactivos o ambos
     * 
     * @return array Resultado de la consulta
     * */
    public static function listaUsuarioGrupo($id_usuario,$activo = NULL){

        //Obtenemos consulta y par�metros para realizar la consulta
        list($sql,$params) = self::listadoGruposUsuario($id_usuario,$activo);

        //Regresamos resultado
        return Paginador::paginar($sql,$params);           
    }


    /**
     * Guardamos registro en la tabla usuario_grupo
     * @param array $usuario Arreglo con los campos a guardar
     * @param int $id del usuario_grupo a editar (opcional)
     * 
     * @return int No. de mensaje
     * */
    public static function saveUsuarioGrupo($id_grupo,$id_usuario,$id = null){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Indicamos predeterminadamente que insertaremos un registro
        $accion = 'insert';

        //Falta validar SQL injection 
        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];              

        //Campos obligatorios
        if ($id_grupo && $id_usuario ) 
        {
                $insertData = array(
                'id_grupo' => $id_grupo,
                'id_usuario' => $id_usuario,
                'activo' => 1,
                'id_usuario_creador' => $id_usuario_creador,
                'fecha_creado' => date('Y-m-d H:i:s')
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
                self::getInstance()->startTransaction();
                
                if(! self::getInstance()->{$accion}('usuario_grupo', $insertData)){
                    //'Error al guardar, NO se guardo el registro'
                    $msg_no = 3;
                    
                    //Cancelamos los posibles campos afectados
                    self::getInstance()->rollback(); 
                     
                }else{
                    //Campos guardados correctamente
                    $msg_no = 1;     
                    
                    //Guardamos los campos afectados en la tabla
                    self::getInstance()->commit();                 
                }             
        }else{
            //'Campos Incompletos'
            $msg_no = 2;             
        }
        return $msg_no;
    }
}