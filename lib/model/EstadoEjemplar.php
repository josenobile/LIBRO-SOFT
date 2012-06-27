<?PHP 
class EstadoEjemplar{
	private $idEstadoEjemplar;
	private $estado;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idEstadoEjemplar;
	}	public function getNombreId(){
		return "idEstado_Ejemplar";
	}
	public function getIdEstadoEjemplar(){
		return $this->idEstadoEjemplar;
	}
	public function getEstado(){
		return $this->estado;
	}

	//Setters

	public function setIdEstadoEjemplar($idEstadoEjemplar){
		$this->idEstadoEjemplar = $idEstadoEjemplar;
	}
	public function setEstado($estado){
		$this->estado = $estado;
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
		if(empty($this->idEstadoEjemplar)){			
			$this->idEstadoEjemplar = $this->con->autoInsert(array(
			"estado" => $this->estado,
			),"estado_ejemplar");
			return;
		}
		return $this->con->autoUpdate(array(
			"estado" => $this->estado,
			),"estado_ejemplar","idEstado_Ejemplar=".$this->getId());
	}
    
	public function cargarPorId($idEstado_Ejemplar){
		if($idEstado_Ejemplar>0){
			$result = $this->con->query("SELECT * FROM `estado_ejemplar`  WHERE idEstado_Ejemplar=".$idEstado_Ejemplar);
			$this->idEstadoEjemplar = $result[0]['idEstado_Ejemplar'];
			$this->estado = $result[0]['estado'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE estado_ejemplar");
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
		$rows =$this->con->query("SELECT $fields,idEstado_Ejemplar FROM `estado_ejemplar`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEstado_Ejemplar"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idEstado_Ejemplar"]);
			$rowsr[$row["idEstado_Ejemplar"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `estado_ejemplar`  WHERE idEstado_Ejemplar=".$this->getId());
	}
}
?>