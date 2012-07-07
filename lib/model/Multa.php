<?PHP 
class Multa{
	private $idMulta;
	private $idEjemplar;
	private $idPrestamo;
	private $idEstado;
	private $multa;
	private $fechaPago;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idMulta;
	}	public function getNombreId(){
		return "idMulta";
	}
	public function getIdMulta(){
		return $this->idMulta;
	}
	public function getIdEjemplar(){
		return $this->idEjemplar;
	}
	public function getIdPrestamo(){
		return $this->idPrestamo;
	}
	public function getIdEstado(){
		return $this->idEstado;
	}
	public function getMulta(){
		return $this->multa;
	}
	public function getFechaPago(){
		return $this->fechaPago;
	}

	//Setters

	public function setIdMulta($idMulta){
		$this->idMulta = $idMulta;
	}
	public function setIdEjemplar($idEjemplar){
		$this->idEjemplar = $idEjemplar;
	}
	public function setIdPrestamo($idPrestamo){
		$this->idPrestamo = $idPrestamo;
	}
	public function setIdEstado($idEstado){
		$this->idEstado = $idEstado;
	}
	public function setMulta($multa){
		$this->multa = $multa;
	}
	public function setFechaPago($fechaPago){
		$this->fechaPago = $fechaPago;
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
		if(empty($this->idMulta)){			
			$this->idMulta = $this->con->autoInsert(array(
			"id_ejemplar" => $this->idEjemplar,
			"id_prestamo" => $this->idPrestamo,
			"id_estado" => $this->idEstado,
			"multa" => $this->multa,
			"fecha_pago" => $this->fechaPago,
			),"multa");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_ejemplar" => $this->idEjemplar,
			"id_prestamo" => $this->idPrestamo,
			"id_estado" => $this->idEstado,
			"multa" => $this->multa,
			"fecha_pago" => $this->fechaPago,
			),"multa","idMulta=".$this->getId());
	}
    
	public function cargarPorId($idMulta){
		if($idMulta>0){
			$result = $this->con->query("SELECT * FROM `multa`  WHERE idMulta=".$idMulta);
			$this->idMulta = $result[0]['idMulta'];
			$this->idEjemplar = $result[0]['id_ejemplar'];
			$this->idPrestamo = $result[0]['id_prestamo'];
			$this->idEstado = $result[0]['id_estado'];
			$this->multa = $result[0]['multa'];
			$this->fechaPago = $result[0]['fecha_pago'];
		return $result[0];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE multa");
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
		$rows =$this->con->query("SELECT $fields,idMulta FROM `multa`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idMulta"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, '*');
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idMulta"]);
			$rowsr[$row["idMulta"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `multa`  WHERE idMulta=".$this->getId());
	}
}
?>