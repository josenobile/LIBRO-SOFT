<?PHP 
class SolucitudCompraLibros{
	private $idSolucitudCompraLibros;
	private $idUsuario;
	private $estado;
	private $isbn;
	private $descripcion;
	private $titulo;
	private $fechaSolicitud;
	protected $con;
	public function __construct(){
		$this->con = DBNative::get();
	}
	//Getters

	public function getId(){
		return $this->idSolucitudCompraLibros;
	}	public function getNombreId(){
		return "idSolucitud_Compra_Libros";
	}
	public function getIdSolucitudCompraLibros(){
		return $this->idSolucitudCompraLibros;
	}
	public function getIdUsuario(){
		return $this->idUsuario;
	}
	public function getEstado(){
		return $this->estado;
	}
	public function getIsbn(){
		return $this->isbn;
	}
	public function getDescripcion(){
		return $this->descripcion;
	}
	public function getTitulo(){
		return $this->titulo;
	}
	public function getFechaSolicitud(){
		return $this->fechaSolicitud;
	}

	//Setters

	public function setIdSolucitudCompraLibros($idSolucitudCompraLibros){
		$this->idSolucitudCompraLibros = $idSolucitudCompraLibros;
	}
	public function setIdUsuario($idUsuario){
		$this->idUsuario = $idUsuario;
	}
	public function setEstado($estado){
		$this->estado = $estado;
	}
	public function setIsbn($isbn){
		$this->isbn = $isbn;
	}
	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}
	public function setTitulo($titulo){
		$this->titulo = $titulo;
	}
	public function setFechaSolicitud($fechaSolicitud){
		$this->fechaSolicitud = $fechaSolicitud;
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
		if(empty($this->idSolucitudCompraLibros)){			
			$this->idSolucitudCompraLibros = $this->con->autoInsert(array(
			"id_usuario" => $this->idUsuario,
			"estado" => $this->estado,
			"isbn" => $this->isbn,
			"descripcion" => $this->descripcion,
			"titulo" => $this->titulo,
			"fecha_solicitud" => $this->fechaSolicitud,
			),"solucitud_compra_libros");
			return;
		}
		return $this->con->autoUpdate(array(
			"id_usuario" => $this->idUsuario,
			"estado" => $this->estado,
			"isbn" => $this->isbn,
			"descripcion" => $this->descripcion,
			"titulo" => $this->titulo,
			"fecha_solicitud" => $this->fechaSolicitud,
			),"solucitud_compra_libros","idSolucitud_Compra_Libros=".$this->getId());
	}
    
	public function cargarPorId($idSolucitud_Compra_Libros){
		if($idSolucitud_Compra_Libros>0){
			$result = $this->con->query("SELECT * FROM `solucitud_compra_libros`  WHERE idSolucitud_Compra_Libros=".$idSolucitud_Compra_Libros);
			$this->idSolucitudCompraLibros = $result[0]['idSolucitud_Compra_Libros'];
			$this->idUsuario = $result[0]['id_usuario'];
			$this->estado = $result[0]['estado'];
			$this->isbn = $result[0]['isbn'];
			$this->descripcion = $result[0]['descripcion'];
			$this->titulo = $result[0]['titulo'];
			$this->fechaSolicitud = $result[0]['fecha_solicitud'];
		return $result[0];
		}
 	}
	public function listar($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$whereA = array();
		if(!$exactMatch){
			$campos = $this->con->query("DESCRIBE solucitud_compra_libros");
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
		$rows =$this->con->query("SELECT $fields,idSolucitud_Compra_Libros FROM `solucitud_compra_libros`  WHERE $where $orderBy LIMIT $limit");
		$rowsI = array();
		foreach($rows as $row){
			$rowsI[$row["idSolucitud_Compra_Libros"]] = $row;
		}
		return $rowsI;
	}
	//como listar, pero retorna un array de objetos
	function listarObj($filtros = array(), $orderBy = '', $limit = "0,30", $exactMatch = false, $fields = '*'){
		$rowsr = array();
		$rows = $this->listar($filtros, $orderBy, $limit, $exactMatch, '*');
		foreach($rows as $row){
			$obj = clone $this;
			$obj->cargarPorId($row["idSolucitud_Compra_Libros"]);
			$rowsr[$row["idSolucitud_Compra_Libros"]] = $obj;
		}
		return $rowsr;
	}
	public function eliminar(){
		return $this->con->query("DELETE FROM `solucitud_compra_libros`  WHERE idSolucitud_Compra_Libros=".$this->getId());
	}
}
?>