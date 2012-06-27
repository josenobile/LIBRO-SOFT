<?PHP
require_once 'frontend_tpl_conf.php';

$aParams = array ();

switch ($_GET ["ac"]) {
	case "area" :
		require FRONTEND_PATH_CONTROLLERS . "/AreaController.php";
		$libro = new AreaController($engine);
		$libro->manejadorDeAcciones ();
		break;
	case "libro" :
		require FRONTEND_PATH_CONTROLLERS . "/LibroController.php";
		$libro = new LibroController($engine);
		$libro->manejadorDeAcciones ();
		break;
	default :
		$aParams ['user'] = 'super admin';
		echo $engine->render ( 'index', $aParams );
		break;
}
?>