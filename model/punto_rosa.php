<?php
/**
 * Clase que nos permite administrar lo relacionado a la tabla Punto Rosa
 * **/ 

//Inclumos librería de Paginador

include_once($_SESSION['inc_path'].'libs/Paginador.php');

class PuntoRosa extends MysqliDb{

    public function __construct(){}
    
    /**
     * Ejecutamos sentencia sql con parámetros
     * @param string $sql Sentencia SQL
     * @param array $params Cada uno de los parámetros de la sentencia
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
     * Obtenemos listado de las caravanas
     * @param $activo Determinamos si queremos los activos, inactivos o ambos
     * predeterminadamente mostramos los activos
     * @return array Resultado de la consulta
     * */
    public static function listaPuntos($activo = 1){

        $sql = 
        'SELECT
        p.*,
        e.nombre as estatus
        FROM `punto_rosa` p
        left join estatus e on e.valor = p.activo
        WHERE ?
        ';

        //Parámetros de la sentencia
        $params = array(1);

        //Filtro de búsqueda        
        //Verificamos si se quieren filtrar los activos/inactivos
        if($activo !== NULL){
            $sql .= ' AND p.activo = ?';
            $params[] = $activo;
        }

        //echo $sql;

        //Regresamos resultado
        return  self::executar($sql,$params);

    }
}
?>