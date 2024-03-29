<?PHP 
class Carrera{
	private $idCarrera;
	private $carrera;
	private $codigo;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idCarrera;
	}	public function getNombreId(){
		return "idCarrera";
	}
	public function getIdCarrera(){
		return $this->idCarrera;
	}
	public function getCarrera(){
		return $this->carrera;
	}
	public function getCodigo(){
		return $this->codigo;
	}

	//Setters

	public function setIdCarrera($idCarrera){
		$this->idCarrera = $idCarrera;
	}
	public function setCarrera($carrera){
		$this->carrera = $carrera;
	}
	public function setCodigo($codigo){
		$this->codigo = $codigo;
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
		if(empty($this->idCarrera)){			
			$this->idCarrera = $this->con->autoInsert(array(
			"carrera" => $this->carrera,
			"codigo" => $this->codigo,
			),"carrera");
			return;
		}
		return $this->con->autoUpdate(array(
			"carrera" => $this->carrera,
			"codigo" => $this->codigo,
			),"carrera","idCarrera=".$this->getId());
	}
    
	public function cargarPorId($idCarrera){
		if($idCarrera>0){
			$result = $this->con->query("SELECT * FROM `carrera`  WHERE idCarrera=".$idCarrera);
			$this->idCarrera = $result[0]['idCarrera'];
			$this->carrera = $result[0]['carrera'];
			$this->codigo = $result[0]['codigo'];
		return $result[0];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE carrera");
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
		$rows =$this->con->query("SELECT $fields,idCarrera FROM `carrera`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idCarrera"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, '*');
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idCarrera"]);
			$rowsr[$row["idCarrera"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `carrera`  WHERE idCarrera=".$this->getId());
	}
}
?>