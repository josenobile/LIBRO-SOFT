<?PHP 
class Prestamo{
	private $idPrestamo;
	private $idPrestamista;
	private $idEstado;
	private $fechaCreacion;
	private $fechaDevolucion;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idPrestamo;
	}	public function getNombreId(){
		return "idPrestamo";
	}
	public function getIdPrestamo(){
		return $this->idPrestamo;
	}
	public function getIdPrestamista(){
		return $this->idPrestamista;
	}
	public function getIdEstado(){
		return $this->idEstado;
	}
	public function getFechaCreacion(){
		return $this->fechaCreacion;
	}
	public function getFechaDevolucion(){
		return $this->fechaDevolucion;
	}
	public function getByUsuario($id_prestamista){
		return $this->listarObj(array("id_prestamista"=>$id_prestamista));
	}
	public function getUsuario(){
		$usuario = new Usuario($this->con);
		$usuario->cargarPorId($this->idPrestamista);
		return $usuario;
	}
	public function getByEstadoPrestamos($id_estado){
		return $this->listarObj(array("id_estado"=>$id_estado));
	}
	public function getEstadoPrestamos(){
		$estado_prestamos = new EstadoPrestamos($this->con);
		$estado_prestamos->cargarPorId($this->idEstado);
		return $estado_prestamos;
	}

	//Setters

	public function setIdPrestamo($idPrestamo){
		$this->idPrestamo = $idPrestamo;
	}
	public function setIdPrestamista($idPrestamista){
		$this->idPrestamista = $idPrestamista;
	}
	public function setIdEstado($idEstado){
		$this->idEstado = $idEstado;
	}
	public function setFechaCreacion($fechaCreacion){
		$this->fechaCreacion = $fechaCreacion;
	}
	public function setFechaDevolucion($fechaDevolucion){
		$this->fechaDevolucion = $fechaDevolucion;
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
		if(empty($this->idPrestamo)){			
			$this->idPrestamo = $this->con->autoInsert(array(
			"id_prestamista" => $this->idPrestamista,
			"id_estado" => $this->idEstado,
			"fecha_creacion" => $this->fechaCreacion,
			"fecha_devolucion" => $this->fechaDevolucion,
			),"prestamo");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_prestamista" => $this->idPrestamista,
			"id_estado" => $this->idEstado,
			"fecha_creacion" => $this->fechaCreacion,
			"fecha_devolucion" => $this->fechaDevolucion,
			),"prestamo","idPrestamo=".$this->getId());
	}
    
	public function cargarPorId($idPrestamo){
		if($idPrestamo>0){
			$result = $this->con->query("SELECT * FROM `prestamo`  WHERE idPrestamo=".$idPrestamo);
			$this->idPrestamo = $result[0]['idPrestamo'];
			$this->idPrestamista = $result[0]['id_prestamista'];
			$this->idEstado = $result[0]['id_estado'];
			$this->fechaCreacion = $result[0]['fecha_creacion'];
			$this->fechaDevolucion = $result[0]['fecha_devolucion'];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE prestamo");
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
		$rows =$this->con->query("SELECT $fields,idPrestamo FROM `prestamo`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idPrestamo"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, $fields);
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idPrestamo"]);
			$rowsr[$row["idPrestamo"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `prestamo`  WHERE idPrestamo=".$this->getId());
	}
}
?>