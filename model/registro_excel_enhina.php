<?php
/**
 * Clase que nos permite administrar lo relacionado con el registro de excel de enhina * **/ 

//Inclumos librer�a de Paginador

include_once('../../inc/libs/Paginador.php');
include_once($_SESSION['model_path'].'servicio_caravana.php');
class Registro_excel extends MysqliDb{

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
     public static function listaRegistroexcel(){
        
        $sql='
                SELECT
                nombre, 
                total_encuestados,
                total_enc_completo,
                total_enc_inc,
                total_familiares,
                total_prog_mac,
                total_prog_map,
                total_registrados,
                total_duplicados,
                total_no_coinciden,
                fecha_subido,
                u.usuario
                FROM `registro_excel_enhina`
                LEFT JOIN usuario u on u.id = registro_excel_enhina.id_usuario_creador
                where ?        
        
        ';
        
        //Par�metros de la sentencia
        $params = array(1);
        
        //Regresamos resultado
        return Paginador::paginar($sql,$params);
     }

     public static function saveRegistroexcel($totales){

        //Variable que nos indica el mensaje generado al guardar el registro
        $msg_no = 0;

        //Arreglo donde contendremos si hay un registro duplicado
        $duplicado=array();

        //Indicamos predeterminadamente que insertaremos un registro
        $db_accion = 'insert';

        //Variable para verificar si es hom�nimo un mujeres_avanzando
        $esHomonimo = NULL;
        
        //Obtenemos el id del usuario creador
        $id_usuario_creador = $_SESSION['usr_id'];

        /*Obtenemos cada una de las variables enviadas v�a POST y las asignamos
        a su respectiva variable. Por ejemplo 
        $id = $_POST['id'], $nombre = $_POST['nombre']*/

            //Creamos variable CURP        $curp = "";

        foreach($totales as $key => $value):

        ${$key} = (is_array($value))? $value : self::getInstance()->real_escape_string($value);

        endforeach;

        //echo $indigena;
        //exit;

         //Comprobamos duplicidad y validez de CURP
                                

            //Campos obligatorios
            if($total_encuestados)
            {                       
            
            
                $insertData = array(
                             'total_encuestados' => $total_encuestados,
							 'total_enc_completo' => $total_enc_completo,
							 'total_enc_inc' => $total_enc_inc,
							 'total_familiares' => $total_familiares,
							 'total_prog_mac' => $total_prog_mac,
							 'total_prog_map' => $total_prog_map,
							 'total_registrados' => $total_registrados,
							 'total_duplicados' => $total_duplicados,
							 'total_no_coinciden' => $total_no_coinciden,
                             'id_usuario_creador' => $id_usuario_creador,
                             'nombre' => $nombre
                 );                            

                //Quitamos del arreglo los valores vac�os
                $insertData = array_filter($insertData, create_function('$a','return preg_match("#\S#", $a);'));

               

                    //Si recibimos id para editar
                    if(intval($id)>0){

                        //Indicamos que haremos un update
                        $db_accion = 'update';
                        
                        //Al editarse no se guardar� el usuario creador                    
                        unset($insertData['id_usuario_creador']);
                    
                        //Agregamos condici�n para indicar qu� id se actualiza
                        self::getInstance()->where('id',$id);                                        

                    }

                    //print_r($insertData);
                    //exit;

                    //Iniciamos transacci�n
                    self::getInstance()->startTransaction();                  

                    if(! self::getInstance()->{$db_accion}('registro_excel_enhina', $insertData)){
                        
                        /*Si se hace un update, no se guardaron campos nuevos, caso contrario
                        NO se est� guardando el registro por tener campos incompletos o incorrectos*/
                        $msg_no = ($db_accion == 'update')?  14 : 3;                    
                        
                        //Cancelamos los posibles campos afectados
                        self::getInstance()->rollback();                        

                        }else{

                        //Campos guardados correctamente
						$msg_no = 1;
                        
                       
                     
                         if($msg_no == 1){
                        self::getInstance()->commit(); 
                        }else{
                            self::getInstance()->rollback();     
                        }              

                        }     

                }

            else{

            //'Campos Incompletos'
            $msg_no = 2;             

            }
              
             

        //Regresamos el mensaje, CURP y el id generado/modificado
        return $msg_no;

    } 
    
}
?>