<?PHP 
class Ejemplar{
	private $idEjemplar;
	private $libroIdLibro;
	private $idEstado;
	private $nombreSala;
	private $numeroPasillo;
	private $estante;
	private $numeroCajon;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idEjemplar;
	}	public function getNombreId(){
		return "idEjemplar";
	}
	public function getIdEjemplar(){
		return $this->idEjemplar;
	}
	public function getLibroIdLibro(){
		return $this->libroIdLibro;
	}
	public function getIdEstado(){
		return $this->idEstado;
	}
	public function getNombreSala(){
		return $this->nombreSala;
	}
	public function getNumeroPasillo(){
		return $this->numeroPasillo;
	}
	public function getEstante(){
		return $this->estante;
	}
	public function getNumeroCajon(){
		return $this->numeroCajon;
	}
	public function getByLibro($Libro_idLibro){
		return $this->listarObj(array("Libro_idLibro"=>$Libro_idLibro));
	}
	public function getLibro(){
		$libro = new Libro($this->con);
		$libro->cargarPorId($this->libroIdLibro);
		return $libro;
	}
	public function getByEstadoEjemplar($id_estado){
		return $this->listarObj(array("id_estado"=>$id_estado));
	}
	public function getEstadoEjemplar(){
		$estado_ejemplar = new EstadoEjemplar($this->con);
		$estado_ejemplar->cargarPorId($this->idEstado);
		return $estado_ejemplar;
	}

	//Setters

	public function setIdEjemplar($idEjemplar){
		$this->idEjemplar = $idEjemplar;
	}
	public function setLibroIdLibro($libroIdLibro){
		$this->libroIdLibro = $libroIdLibro;
	}
	public function setIdEstado($idEstado){
		$this->idEstado = $idEstado;
	}
	public function setNombreSala($nombreSala){
		$this->nombreSala = $nombreSala;
	}
	public function setNumeroPasillo($numeroPasillo){
		$this->numeroPasillo = $numeroPasillo;
	}
	public function setEstante($estante){
		$this->estante = $estante;
	}
	public function setNumeroCajon($numeroCajon){
		$this->numeroCajon = $numeroCajon;
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
		if(empty($this->idEjemplar)){			
			$this->idEjemplar = $this->con->autoInsert(array(
			"Libro_idLibro" => $this->libroIdLibro,
			"id_estado" => $this->idEstado,
			"nombre_sala" => $this->nombreSala,
			"numero_pasillo" => $this->numeroPasillo,
			"estante" => $this->estante,
			"numero_cajon" => $this->numeroCajon,
			),"ejemplar");
			return;
		}
		return $this->con->autoUpdate(array(
			"Libro_idLibro" => $this->libroIdLibro,
			"id_estado" => $this->idEstado,
			"nombre_sala" => $this->nombreSala,
			"numero_pasillo" => $this->numeroPasillo,
			"estante" => $this->estante,
			"numero_cajon" => $this->numeroCajon,
			),"ejemplar","idEjemplar=".$this->getId());
	}
    
	public function cargarPorId($idEjemplar){
		if($idEjemplar>0){
			$result = $this->con->query("SELECT * FROM `ejemplar`  WHERE idEjemplar=".$idEjemplar);
			$this->idEjemplar = $result[0]['idEjemplar'];
			$this->libroIdLibro = $result[0]['Libro_idLibro'];
			$this->idEstado = $result[0]['id_estado'];
			$this->nombreSala = $result[0]['nombre_sala'];
			$this->numeroPasillo = $result[0]['numero_pasillo'];
			$this->estante = $result[0]['estante'];
			$this->numeroCajon = $result[0]['numero_cajon'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE ejemplar");
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
		$rows =$this->con->query("SELECT $fields,idEjemplar FROM `ejemplar`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEjemplar"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idEjemplar"]);
			$rowsr[$row["idEjemplar"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `ejemplar`  WHERE idEjemplar=".$this->getId());
	}
}
?>