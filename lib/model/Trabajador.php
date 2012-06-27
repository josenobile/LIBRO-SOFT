<?PHP 
class Trabajador{
	private $idTrabajador;
	private $perfilIdPerfil;
	private $usuarioIdUsuario;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idTrabajador;
	}	public function getNombreId(){
		return "idTrabajador";
	}
	public function getIdTrabajador(){
		return $this->idTrabajador;
	}
	public function getPerfilIdPerfil(){
		return $this->perfilIdPerfil;
	}
	public function getUsuarioIdUsuario(){
		return $this->usuarioIdUsuario;
	}
	public function getByPerfil($Perfil_idPerfil){
		return $this->listarObj(array("Perfil_idPerfil"=>$Perfil_idPerfil));
	}
	public function getPerfil(){
		$perfil = new Perfil($this->con);
		$perfil->cargarPorId($this->perfilIdPerfil);
		return $perfil;
	}
	public function getByUsuario($Usuario_idUsuario){
		return $this->listarObj(array("Usuario_idUsuario"=>$Usuario_idUsuario));
	}
	public function getUsuario(){
		$usuario = new Usuario($this->con);
		$usuario->cargarPorId($this->usuarioIdUsuario);
		return $usuario;
	}

	//Setters

	public function setIdTrabajador($idTrabajador){
		$this->idTrabajador = $idTrabajador;
	}
	public function setPerfilIdPerfil($perfilIdPerfil){
		$this->perfilIdPerfil = $perfilIdPerfil;
	}
	public function setUsuarioIdUsuario($usuarioIdUsuario){
		$this->usuarioIdUsuario = $usuarioIdUsuario;
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
		if(empty($this->idTrabajador)){			
			$this->idTrabajador = $this->con->autoInsert(array(
			"Perfil_idPerfil" => $this->perfilIdPerfil,
			"Usuario_idUsuario" => $this->usuarioIdUsuario,
			),"trabajador");
			return;
		}
		return $this->con->autoUpdate(array(
			"Perfil_idPerfil" => $this->perfilIdPerfil,
			"Usuario_idUsuario" => $this->usuarioIdUsuario,
			),"trabajador","idTrabajador=".$this->getId());
	}
    
	public function cargarPorId($idTrabajador){
		if($idTrabajador>0){
			$result = $this->con->query("SELECT * FROM `trabajador`  WHERE idTrabajador=".$idTrabajador);
			$this->idTrabajador = $result[0]['idTrabajador'];
			$this->perfilIdPerfil = $result[0]['Perfil_idPerfil'];
			$this->usuarioIdUsuario = $result[0]['Usuario_idUsuario'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE trabajador");
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
		$rows =$this->con->query("SELECT $fields,idTrabajador FROM `trabajador`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idTrabajador"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idTrabajador"]);
			$rowsr[$row["idTrabajador"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `trabajador`  WHERE idTrabajador=".$this->getId());
	}
}
?>