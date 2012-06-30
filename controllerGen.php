<?PHP
//Class Generator
require_once "lib/DBNative.php";
//require_once 'frontend_tpl_conf.php';
$fileConf = realpath(dirname(__FILE__))."/config/databases.ini";
if (file_exists($fileConf) && is_readable($fileConf)) {
	$aSettings = parse_ini_file($fileConf, true);
	define("DB_SERVER",         $aSettings["remote_database"]["server"]);
	define("DB_NAME",           $aSettings["remote_database"]["name"]);
	define("DB_USER",           $aSettings["remote_database"]["user"]);
	define("DB_PASS",           $aSettings["remote_database"]["password"]);
	define("DB_SERVER_LOCAL",   $aSettings["local_database"]["server"]);
	define("DB_NAME_LOCAL",     $aSettings["local_database"]["name"]);
	define("DB_USER_LOCAL",     $aSettings["local_database"]["user"]);
	define("DB_PASS_LOCAL",     $aSettings["local_database"]["password"]);
} else {
	die("File configuration was not found!");
}
if (in_array($_SERVER['SERVER_ADDR'], array("127.0.0.1", "localhost", "192.168.0.117","192.168.0.128","192.168.1.103","192.168.1.104"))){
	$mode = "local";
	define("DSN", "mysql://".DB_USER_LOCAL.":".DB_PASS_LOCAL."@".DB_SERVER_LOCAL."/".DB_NAME_LOCAL);
}
else{
	$mode = "remote";
	define("DSN", "mysql://".DB_USER.":".DB_PASS."@".DB_SERVER."/".DB_NAME);
}

$con =  DBNative::get(DSN);
$inicio = microtime(true);
function printCode($source_code)
{

	if (is_array($source_code))
		return false;
	 
	$source_code = explode("\n", str_replace(array("\r\n", "\r"), "\n", $source_code));
	$line_count = 1;
	$formatted_code = '';
	foreach ($source_code as $code_line)
	{
		$formatted_code .= '<tr><td>'.$line_count.'</td>';
		$line_count++;
		 
		if (preg_match('/<\?(php)?[^[:graph:]]/', $code_line))
			$formatted_code .= '<td>'. str_replace(array('<code>', '</code>'), '', highlight_string($code_line, true)).'</td></tr>';
		else
			$formatted_code .= '<td>'.@ereg_replace('(&lt;\?php&nbsp;)+', '', str_replace(array('<code>', '</code>'), '', highlight_string('<?php '.$code_line, true))).'</td></tr>';
	}

	return '<table style="font: 1em Consolas, \'andale mono\', \'monotype.com\', \'lucida console\', monospace;">'.$formatted_code.'</table>';
}


$tablas = $con->query("SHOW TABLES");
$contenidos = array();
foreach($tablas as $tabla)
{
	$tabla = $tabla["Tables_in_librosoft"];
	$campos = $con->query("DESCRIBE $tabla");
	//Poner en un array los campos pulpitos
	$camposP = array();
	$primarias = array();
	foreach($campos as $campo)
	{
		if($campo["Key"]!="PRI")
			$camposP[] = $campo["Field"];
		else{
			$primarias[] = $campo["Field"];
			$primary = $campo["Field"];
		}
	}
	if(empty($primary)){
		echo "Saltando tabla {$tabla}: No tiene una clave primaria<br />";
		continue;
	}
	if(count($primarias)>1){
		echo "Saltando tabla {$tabla}: Contiene ".count($primarias). " campos (".implode(", ",$primarias).") como clave primaria
		<a href='http://trac.propelorm.org/ticket/359'>Propel</a> -
		<a href='http://docs.doctrine-project.org/projects/doctrine-orm/en/2.0.x/tutorials/composite-primary-keys.html'>Doctrine</a><br />";
		continue;
	}
	//Inteligencia artificial
	$create = $con->query("SHOW CREATE TABLE `$tabla`");
	if(!isset($create[0]["Create Table"])){
		echo "Saltando $tabla por que no es un base tabla<br />";
		continue;
	}
	$lineas = explode("\n",$create[0]["Create Table"]);
	$foraneas = array();
	//echo "<pre>".print_r($lineas,true)."</pre>";
	foreach($lineas as $linea)
	{
		if(strpos($linea,"CONSTRAINT")!==false)//posicion cero
		{
			//Parsear
			$pos = strpos($linea,"FOREIGN KEY (`")+14;
			$tmp = substr($linea,$pos);
			$pos = strpos($tmp,"`) REFERENCES `");
			$campo = substr($tmp,0,$pos);//Listo el campo
			//echo $campo." --> ";
			$tmp = substr($tmp,$pos+15);
			$pos = strpos($tmp,"` (`");
			$tablatmp = substr($tmp,0,$pos);//Lista la tabla a la que referencia
			//echo $tabla.".";
			$campor = substr($tmp,$pos+4,strpos($tmp,"`) ")-($pos+4));
			//echo $campor."<br />";
			$foraneas[$campo] = array("tabla"=>$tablatmp,"campo" => $campor);
		}
	}

	ob_start();
	$clase = str_replace(" ","",ucwords(str_replace("_"," ",$tabla)));
	$titulos = array();
	foreach($campos as $campo){
		$titulos[] = ucwords(str_replace("_"," ",$campo["Field"]));
	}
?>

class <?PHP echo $clase;?>Controller {
	private $<?PHP echo $tabla;?>;
	private $aParams;
	private $motorDePlantilas;

	public function <?PHP echo $clase;?>Controller(sfTemplateEngine &$engine) {
		$this-><?PHP echo $tabla;?> = new <?PHP echo $clase;?>Model();
		$this->aParams = Array();
		$this->motorDePlantilas = $engine;
	}
	public function manejadorDeAcciones() {
		if(@$_REQUEST['sEcho'] != ""){
			die($this-><?PHP echo $tabla;?>->getPager(array("<?PHP implode("\",\"",$titulos);?>")->getJSON());
		}
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$this->guardar($_POST["<?PHP echo $primary;?>"]);
		}
		if (@$_GET["accion"] == "eliminar" && $_GET["id"] > 0) {
			$this->eliminar(intval($_GET["id"]));
		}
		if (@$_GET["accion"] == "editar" && $_GET["id"] > 0) {
			$this->cargarPorId(intval($_GET["id"]));
			die(json_encode($this->aParams["<?PHP echo $tabla;?>"]));
		}
		$this->consultar();
		$this->mostarPlantilla();
	}
	private function guardar($id) {
		$this-><?PHP echo $tabla;?>->cargarPorId($id);
		$this-><?PHP echo $tabla;?>->setValues($_POST);
		$this-><?PHP echo $tabla;?>->save();
		$resp = json_encode(array("msg"=>"El registro fue grabado. ID=".$this-><?PHP echo $tabla;?>->getId(),"id"=>$this-><?PHP echo $tabla;?>->getId()));
		die($resp);
	}
	
	public function cargarPorId($id){
		$this-><?PHP echo $tabla;?>->cargarPorId($id);
		$this->aParams["<?PHP echo $tabla;?>"] = array(
		<?PHP foreach($campos as $campo){ ?>
				"<?PHP echo $primary;?>" => $this-><?PHP echo $tabla;?>->getId(),
				"<?PHP echo $tabla;?>" => $this->area->getArea(),
				"codigo" => $this->area->getCodigo(),
				"descripcion" => $this->area->getDescripcion()				
		);
	}

	private function eliminar($id) {
		$this->area->cargarPorId($id);
		$this->area->eliminar();
		$this->aParams["message"] = "El registro fue eliminado";
		$resp = json_encode(array("msg"=>$this->aParams["message"]));
		die($resp);
	}

	private function consultar() {
		$this->aParams["areas"] = array();
		$areas = $this->area->listarObj();
		foreach ($areas as $area) {
			$this->aParams["areas"][] = array(
				"idArea" => $area->getId(),
				"area" => $area->getArea(),
				"codigo" => $area->getCodigo(),
				"descripcion" => $area->getDescripcion()
			);
		}
	}

	private function mostarPlantilla() {
		echo $this->motorDePlantilas->render("area", $this->aParams);
	}
}<?PHP
	$contenido = ob_get_contents();
	ob_end_clean();
	$contenidos[$clase] = $contenido;
}
$lineas = 0;
foreach($contenidos as $clase => $codigo){
	echo "<h2>$clase</h2>";
	if(class_exists($clase)){
		echo "Clase $clase ya existe, saltando<br />";
		continue;
	}
	$resp = eval($codigo);
	if($resp === false)
		die("Error al compilar el codigo, el codigo fue <br /><pre>".printCode("<?PHP ".$codigo)."</pre>");
	$codigo = "<"."?"."PHP"." ".$codigo."\r\n?".">";
	$ruta = "controller/{$clase}.php";
	file_put_contents($ruta,utf8_encode($codigo)) or die("Error al grabar $ruta");
	echo "Guardado $ruta <br />";
	$lineas += count(explode("\n",$codigo));
	//echo "<pre>".htmlentities($codigo,ENT_COMPAT,"UTF-8")."</pre>";
	echo "<br />";
}
$fin = microtime(true);
$total = $fin-$inicio;
echo "$lineas lineas generadas<br />";
echo "hecho en $total segundos";
?>
