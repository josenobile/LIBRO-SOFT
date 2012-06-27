<?PHP 
class Libro{
	private $idLibro;
	private $idAreaConocimiento;
	private $iSBN;
	private $titulo;
	private $añoPublicación;
	private $idioma;
	private $palabrasClaves;
	private $idEditorial;
	private $caratula;
	private $archivo;
	private $fechaIngreso;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idLibro;
	}	public function getNombreId(){
		return "idLibro";
	}
	public function getIdLibro(){
		return $this->idLibro;
	}
	public function getIdAreaConocimiento(){
		return $this->idAreaConocimiento;
	}
	public function getISBN(){
		return $this->iSBN;
	}
	public function getTitulo(){
		return $this->titulo;
	}
	public function getAñoPublicación(){
		return $this->añoPublicación;
	}
	public function getIdioma(){
		return $this->idioma;
	}
	public function getPalabrasClaves(){
		return $this->palabrasClaves;
	}
	public function getIdEditorial(){
		return $this->idEditorial;
	}
	public function getCaratula(){
		return $this->caratula;
	}
	public function getArchivo(){
		return $this->archivo;
	}
	public function getFechaIngreso(){
		return $this->fechaIngreso;
	}
	public function getByEditorial($id_editorial){
		return $this->listarObj(array("id_editorial"=>$id_editorial));
	}
	public function getEditorial(){
		$editorial = new Editorial($this->con);
		$editorial->cargarPorId($this->idEditorial);
		return $editorial;
	}
	public function getByArea($id_area_conocimiento){
		return $this->listarObj(array("id_area_conocimiento"=>$id_area_conocimiento));
	}
	public function getArea(){
		$area = new Area($this->con);
		$area->cargarPorId($this->idAreaConocimiento);
		return $area;
	}

	//Setters

	public function setIdLibro($idLibro){
		$this->idLibro = $idLibro;
	}
	public function setIdAreaConocimiento($idAreaConocimiento){
		$this->idAreaConocimiento = $idAreaConocimiento;
	}
	public function setISBN($iSBN){
		$this->iSBN = $iSBN;
	}
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}
	public function setAñoPublicación($añoPublicación){
		$this->añoPublicación = $añoPublicación;
	}
	public function setIdioma($idioma){
		$this->idioma = $idioma;
	}
	public function setPalabrasClaves($palabrasClaves){
		$this->palabrasClaves = $palabrasClaves;
	}
	public function setIdEditorial($idEditorial){
		$this->idEditorial = $idEditorial;
	}
	public function setCaratula($caratula){
		$this->caratula = $caratula;
	}
	public function setArchivo($archivo){
		$this->archivo = $archivo;
	}
	public function setFechaIngreso($fechaIngreso){
		$this->fechaIngreso = $fechaIngreso;
	}
	//LLena todos los atributos de la clase sacando los valores de un array
	function setValues($array){
		foreach($array as $key => $val){
			$key = lcfirst(str_replace(" ","",ucwords(str_replace("_"," ",$key))));
			if(property_exists($this,$key))
				$this->$key = $val;
		}
	}
	
	//Guarda o actualiza el objeto en la base de datos, la accion se determina por la clave primaria
	public function save(){
		if(empty($this->idLibro)){			
			$this->idLibro = $this->con->autoInsert(array(
			"id_area_conocimiento" => $this->idAreaConocimiento,
			"ISBN" => $this->iSBN,
			"titulo" => $this->titulo,
			"año_publicación" => $this->añoPublicación,
			"idioma" => $this->idioma,
			"palabras_claves" => $this->palabrasClaves,
			"id_editorial" => $this->idEditorial,
			"caratula" => $this->caratula,
			"archivo" => $this->archivo,
			"fecha_ingreso" => $this->fechaIngreso,
			),"libro");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_area_conocimiento" => $this->idAreaConocimiento,
			"ISBN" => $this->iSBN,
			"titulo" => $this->titulo,
			"año_publicación" => $this->añoPublicación,
			"idioma" => $this->idioma,
			"palabras_claves" => $this->palabrasClaves,
			"id_editorial" => $this->idEditorial,
			"caratula" => $this->caratula,
			"archivo" => $this->archivo,
			"fecha_ingreso" => $this->fechaIngreso,
			),"libro","idLibro=".$this->getId());
	}
    
	public function cargarPorId($idLibro){
		if($idLibro>0){
			$result = $this->con->query("SELECT * FROM `libro`  WHERE idLibro=".$idLibro);
			$this->idLibro = $result[0]['idLibro'];
			$this->idAreaConocimiento = $result[0]['id_area_conocimiento'];
			$this->iSBN = $result[0]['ISBN'];
			$this->titulo = $result[0]['titulo'];
			$this->añoPublicación = $result[0]['año_publicación'];
			$this->idioma = $result[0]['idioma'];
			$this->palabrasClaves = $result[0]['palabras_claves'];
			$this->idEditorial = $result[0]['id_editorial'];
			$this->caratula = $result[0]['caratula'];
			$this->archivo = $result[0]['archivo'];
			$this->fechaIngreso = $result[0]['fecha_ingreso'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE libro");
			$listicos = array();
			foreach($campos as $campo){
				$tmp = explode("(",$campo["Type"]);
				$listicos[$campo["Field"]] = $tmp[0];
			}
			foreach($filtros as $filtro => $valor){
				if($listicos[$filtro] == "int")
					$whereA[] = $filtro." = ".floatval($valor);
				else
					$whereA[] = $filtro." LIKE '%".$this->con->escape($valor)."%'";			
			}

		}else{
			foreach($filtros as $filtro => $valor)
				$whereA[] = $filtro." = ".$this->con->quote($valor);
		}
		$where = implode(" AND ",$whereA);
		if($where == '')
			$where = 1;
		if ($orderBy != "")
			$orderBy = "ORDER BY $orderBy";
		$rows =$this->con->query("SELECT $fields,idLibro FROM `libro`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idLibro"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idLibro"]);
			$rowsr[$row["idLibro"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `libro`  WHERE idLibro=".$this->getId());
	}
}
?>