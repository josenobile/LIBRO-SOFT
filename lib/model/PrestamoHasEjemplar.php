<?PHP 
class PrestamoHasEjemplar{
	private $idPrestamoEjemplar;
	private $prestamoIdPrestamo;
	private $ejemplarIdEjemplar;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idPrestamoEjemplar;
	}	public function getNombreId(){
		return "idPrestamo_Ejemplar";
	}
	public function getIdPrestamoEjemplar(){
		return $this->idPrestamoEjemplar;
	}
	public function getPrestamoIdPrestamo(){
		return $this->prestamoIdPrestamo;
	}
	public function getEjemplarIdEjemplar(){
		return $this->ejemplarIdEjemplar;
	}
	public function getByPrestamo($Prestamo_idPrestamo){
		return $this->listarObj(array("Prestamo_idPrestamo"=>$Prestamo_idPrestamo));
	}
	public function getPrestamo(){
		$prestamo = new Prestamo($this->con);
		$prestamo->cargarPorId($this->prestamoIdPrestamo);
		return $prestamo;
	}
	public function getByEjemplar($Ejemplar_idEjemplar){
		return $this->listarObj(array("Ejemplar_idEjemplar"=>$Ejemplar_idEjemplar));
	}
	public function getEjemplar(){
		$ejemplar = new Ejemplar($this->con);
		$ejemplar->cargarPorId($this->ejemplarIdEjemplar);
		return $ejemplar;
	}

	//Setters

	public function setIdPrestamoEjemplar($idPrestamoEjemplar){
		$this->idPrestamoEjemplar = $idPrestamoEjemplar;
	}
	public function setPrestamoIdPrestamo($prestamoIdPrestamo){
		$this->prestamoIdPrestamo = $prestamoIdPrestamo;
	}
	public function setEjemplarIdEjemplar($ejemplarIdEjemplar){
		$this->ejemplarIdEjemplar = $ejemplarIdEjemplar;
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
		if(empty($this->idPrestamoEjemplar)){			
			$this->idPrestamoEjemplar = $this->con->autoInsert(array(
			"Prestamo_idPrestamo" => $this->prestamoIdPrestamo,
			"Ejemplar_idEjemplar" => $this->ejemplarIdEjemplar,
			),"prestamo_has_ejemplar");
			return;
		}
		return $this->con->autoUpdate(array(
			"Prestamo_idPrestamo" => $this->prestamoIdPrestamo,
			"Ejemplar_idEjemplar" => $this->ejemplarIdEjemplar,
			),"prestamo_has_ejemplar","idPrestamo_Ejemplar=".$this->getId());
	}
    
	public function cargarPorId($idPrestamo_Ejemplar){
		if($idPrestamo_Ejemplar>0){
			$result = $this->con->query("SELECT * FROM `prestamo_has_ejemplar`  WHERE idPrestamo_Ejemplar=".$idPrestamo_Ejemplar);
			$this->idPrestamoEjemplar = $result[0]['idPrestamo_Ejemplar'];
			$this->prestamoIdPrestamo = $result[0]['Prestamo_idPrestamo'];
			$this->ejemplarIdEjemplar = $result[0]['Ejemplar_idEjemplar'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE prestamo_has_ejemplar");
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
		$rows =$this->con->query("SELECT $fields,idPrestamo_Ejemplar FROM `prestamo_has_ejemplar`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idPrestamo_Ejemplar"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idPrestamo_Ejemplar"]);
			$rowsr[$row["idPrestamo_Ejemplar"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `prestamo_has_ejemplar`  WHERE idPrestamo_Ejemplar=".$this->getId());
	}
}
?>