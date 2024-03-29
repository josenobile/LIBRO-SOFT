<?PHP 
class TipoIdentificación{
	private $idTipoIdentificación;
	private $nombre;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idTipoIdentificación;
	}	public function getNombreId(){
		return "idTipo_Identificación";
	}
	public function getIdTipoIdentificación(){
		return $this->idTipoIdentificación;
	}
	public function getNombre(){
		return $this->nombre;
	}

	//Setters

	public function setIdTipoIdentificación($idTipoIdentificación){
		$this->idTipoIdentificación = $idTipoIdentificación;
	}
	public function setNombre($nombre){
		$this->nombre = $nombre;
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
		if(empty($this->idTipoIdentificación)){			
			$this->idTipoIdentificación = $this->con->autoInsert(array(
			"nombre" => $this->nombre,
			),"tipo_identificación");
			return;
		}
		return $this->con->autoUpdate(array(
			"nombre" => $this->nombre,
			),"tipo_identificación","idTipo_Identificación=".$this->getId());
	}
    
	public function cargarPorId($idTipo_Identificación){
		if($idTipo_Identificación>0){
			$result = $this->con->query("SELECT * FROM `tipo_identificación`  WHERE idTipo_Identificación=".$idTipo_Identificación);
			$this->idTipoIdentificación = $result[0]['idTipo_Identificación'];
			$this->nombre = $result[0]['nombre'];
		return $result[0];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE tipo_identificación");
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
		$rows =$this->con->query("SELECT $fields,idTipo_Identificación FROM `tipo_identificación`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idTipo_Identificación"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, '*');
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idTipo_Identificación"]);
			$rowsr[$row["idTipo_Identificación"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `tipo_identificación`  WHERE idTipo_Identificación=".$this->getId());
	}
}
?>