<?PHP 
class Descargas{
	private $idDescargas;
	private $contador;
	private $usuarioIdUsuario;
	private $libroIdLibro;
	private $fecha;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idDescargas;
	}	public function getNombreId(){
		return "idDescargas";
	}
	public function getIdDescargas(){
		return $this->idDescargas;
	}
	public function getContador(){
		return $this->contador;
	}
	public function getUsuarioIdUsuario(){
		return $this->usuarioIdUsuario;
	}
	public function getLibroIdLibro(){
		return $this->libroIdLibro;
	}
	public function getFecha(){
		return $this->fecha;
	}

	//Setters

	public function setIdDescargas($idDescargas){
		$this->idDescargas = $idDescargas;
	}
	public function setContador($contador){
		$this->contador = $contador;
	}
	public function setUsuarioIdUsuario($usuarioIdUsuario){
		$this->usuarioIdUsuario = $usuarioIdUsuario;
	}
	public function setLibroIdLibro($libroIdLibro){
		$this->libroIdLibro = $libroIdLibro;
	}
	public function setFecha($fecha){
		$this->fecha = $fecha;
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
		if(empty($this->idDescargas)){			
			$this->idDescargas = $this->con->autoInsert(array(
			"contador" => $this->contador,
			"Usuario_idUsuario" => $this->usuarioIdUsuario,
			"Libro_idLibro" => $this->libroIdLibro,
			"fecha" => $this->fecha,
			),"descargas");
			return;
		}
		return $this->con->autoUpdate(array(
			"contador" => $this->contador,
			"Usuario_idUsuario" => $this->usuarioIdUsuario,
			"Libro_idLibro" => $this->libroIdLibro,
			"fecha" => $this->fecha,
			),"descargas","idDescargas=".$this->getId());
	}
    
	public function cargarPorId($idDescargas){
		if($idDescargas>0){
			$result = $this->con->query("SELECT * FROM `descargas`  WHERE idDescargas=".$idDescargas);
			$this->idDescargas = $result[0]['idDescargas'];
			$this->contador = $result[0]['contador'];
			$this->usuarioIdUsuario = $result[0]['Usuario_idUsuario'];
			$this->libroIdLibro = $result[0]['Libro_idLibro'];
			$this->fecha = $result[0]['fecha'];
		return $result[0];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE descargas");
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
		$rows =$this->con->query("SELECT $fields,idDescargas FROM `descargas`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idDescargas"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, '*');
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idDescargas"]);
			$rowsr[$row["idDescargas"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `descargas`  WHERE idDescargas=".$this->getId());
	}
}
?>