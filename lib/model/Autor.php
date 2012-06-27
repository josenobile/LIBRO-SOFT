<?PHP 
class Autor{
	private $idAutor;
	private $primerNombre;
	private $segundoNombre;
	private $primerApellido;
	private $segundoApellido;
	private $paisNacionalidad;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idAutor;
	}	public function getNombreId(){
		return "idAutor";
	}
	public function getIdAutor(){
		return $this->idAutor;
	}
	public function getPrimerNombre(){
		return $this->primerNombre;
	}
	public function getSegundoNombre(){
		return $this->segundoNombre;
	}
	public function getPrimerApellido(){
		return $this->primerApellido;
	}
	public function getSegundoApellido(){
		return $this->segundoApellido;
	}
	public function getPaisNacionalidad(){
		return $this->paisNacionalidad;
	}

	//Setters

	public function setIdAutor($idAutor){
		$this->idAutor = $idAutor;
	}
	public function setPrimerNombre($primerNombre){
		$this->primerNombre = $primerNombre;
	}
	public function setSegundoNombre($segundoNombre){
		$this->segundoNombre = $segundoNombre;
	}
	public function setPrimerApellido($primerApellido){
		$this->primerApellido = $primerApellido;
	}
	public function setSegundoApellido($segundoApellido){
		$this->segundoApellido = $segundoApellido;
	}
	public function setPaisNacionalidad($paisNacionalidad){
		$this->paisNacionalidad = $paisNacionalidad;
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
		if(empty($this->idAutor)){			
			$this->idAutor = $this->con->autoInsert(array(
			"primer_nombre" => $this->primerNombre,
			"segundo_nombre" => $this->segundoNombre,
			"primer_apellido" => $this->primerApellido,
			"segundo_apellido" => $this->segundoApellido,
			"pais_nacionalidad" => $this->paisNacionalidad,
			),"autor");
			return;
		}
		return $this->con->autoUpdate(array(
			"primer_nombre" => $this->primerNombre,
			"segundo_nombre" => $this->segundoNombre,
			"primer_apellido" => $this->primerApellido,
			"segundo_apellido" => $this->segundoApellido,
			"pais_nacionalidad" => $this->paisNacionalidad,
			),"autor","idAutor=".$this->getId());
	}
    
	public function cargarPorId($idAutor){
		if($idAutor>0){
			$result = $this->con->query("SELECT * FROM `autor`  WHERE idAutor=".$idAutor);
			$this->idAutor = $result[0]['idAutor'];
			$this->primerNombre = $result[0]['primer_nombre'];
			$this->segundoNombre = $result[0]['segundo_nombre'];
			$this->primerApellido = $result[0]['primer_apellido'];
			$this->segundoApellido = $result[0]['segundo_apellido'];
			$this->paisNacionalidad = $result[0]['pais_nacionalidad'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE autor");
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
		$rows =$this->con->query("SELECT $fields,idAutor FROM `autor`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idAutor"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idAutor"]);
			$rowsr[$row["idAutor"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `autor`  WHERE idAutor=".$this->getId());
	}
}
?>