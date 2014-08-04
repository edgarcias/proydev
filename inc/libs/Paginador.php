<?php
/**
 * Clase que nos permite administrar variables globales, restricciones y dem�s
 * **/

//Inclumos librer�a de paginaci�n

include('Pagination.php');

class Paginador extends MysqliDb
{

    public function __construct(){}
    public function __destruct(){}

     /**
      * N�mero de p�ginas desplegadas en la barra de navegaci�n.    
      * 
      * @var int  
      * */
     public static $pagesPerSection = 15;

     /**
      * Desplegado de opciones
      * array(5, 10, 25, 50, "All");
      * @var Array
      * */    
     private static $options = array(15,30);

     /**
      * Nombre del ID para el paginador
      * 
      * @var int
      * */
     public static $paginationID = "comments";	

     /**
      * Lo siguiente son nombres de las clases de estilo
      * 
      * @var string
      * */      
      public static $stylePageOff    = "pageOff";
      public static $stylePageOn	 = "pageOn";
      public static $styleErrors	 = "paginationErrors";
      public static $styleSelect	 = "paginationSelect";      

    public function setPagesPerSection($number = 15){
      self::$pagesPerSection = $number;
    }

    /**
    *Regresamos resultados desde una funci�n NO est�tica
    *@param string $sql_pag consulta a paginar
    *@param array $params par�metros de la consulta
    *
    *@return array Arreglo con resultados y paginador
    */
    public function objPaginar($sql_pag,$params){
       return self::paginar($sql_pag,$params);
    }

    public static function paginar($sql_pag,$params){

        //Obtenemos el total de registro del sql a paginar
        $sql='SELECT COUNT(*) FROM ('.$sql_pag.') pag';

        //Hacemos consulta
        $results = self::getInstance()->rawQuery($sql,$params);        

        //Obtenemos el total de registros
        $totalEntries = array_shift($results[0]);

        $Pagination = new Pagination(
        $totalEntries, 
        self::$pagesPerSection, 
        self::$options, 
        self::$paginationID, 
        self::$stylePageOff, 
        self::$stylePageOn, 
        self::$styleErrors, 
        self::$styleSelect);

        //Obtenemos el rango de registros  mostrar
        $start 		= $Pagination->getEntryStart();
        $end 		= $Pagination->getEntryEnd();

        //preparamos par�metros
        $params[] = $start;
        $params[] = $end;

        //Armamos la consulta final
        $sql = $sql_pag." LIMIT ?,?"; 
        $results = self::getInstance()->rawQuery($sql,$params);

        return array($results,$Pagination);
     }
}