<?PHP 
class Estudiante{
	private $idEstudiante;
	private $usuarioIdUsuario;
	private $idCarrera;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idEstudiante;
	}	public function getNombreId(){
		return "idEstudiante";
	}
	public function getIdEstudiante(){
		return $this->idEstudiante;
	}
	public function getUsuarioIdUsuario(){
		return $this->usuarioIdUsuario;
	}
	public function getIdCarrera(){
		return $this->idCarrera;
	}
	public function getByUsuario($Usuario_idUsuario){
		return $this->listarObj(array("Usuario_idUsuario"=>$Usuario_idUsuario));
	}
	public function getUsuario(){
		$usuario = new Usuario($this->con);
		$usuario->cargarPorId($this->usuarioIdUsuario);
		return $usuario;
	}
	public function getByCarrera($id_carrera){
		return $this->listarObj(array("id_carrera"=>$id_carrera));
	}
	public function getCarrera(){
		$carrera = new Carrera($this->con);
		$carrera->cargarPorId($this->idCarrera);
		return $carrera;
	}

	//Setters

	public function setIdEstudiante($idEstudiante){
		$this->idEstudiante = $idEstudiante;
	}
	public function setUsuarioIdUsuario($usuarioIdUsuario){
		$this->usuarioIdUsuario = $usuarioIdUsuario;
	}
	public function setIdCarrera($idCarrera){
		$this->idCarrera = $idCarrera;
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
		if(empty($this->idEstudiante)){			
			$this->idEstudiante = $this->con->autoInsert(array(
			"Usuario_idUsuario" => $this->usuarioIdUsuario,
			"id_carrera" => $this->idCarrera,
			),"estudiante");
			return;
		}
		return $this->con->autoUpdate(array(
			"Usuario_idUsuario" => $this->usuarioIdUsuario,
			"id_carrera" => $this->idCarrera,
			),"estudiante","idEstudiante=".$this->getId());
	}
    
	public function cargarPorId($idEstudiante){
		if($idEstudiante>0){
			$result = $this->con->query("SELECT * FROM `estudiante`  WHERE idEstudiante=".$idEstudiante);
			$this->idEstudiante = $result[0]['idEstudiante'];
			$this->usuarioIdUsuario = $result[0]['Usuario_idUsuario'];
			$this->idCarrera = $result[0]['id_carrera'];
		return $result[0];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE estudiante");
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
		$rows =$this->con->query("SELECT $fields,idEstudiante FROM `estudiante`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idEstudiante"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, '*');
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idEstudiante"]);
			$rowsr[$row["idEstudiante"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `estudiante`  WHERE idEstudiante=".$this->getId());
	}
}
?>