<?PHP

class LibroController {

    private $libro;
    private $aParams;
    private $motorDePlantilas;

    public function LibroController(sfTemplateEngine &$engine) {
        $this->libro = new LibroModel();
        $this->aParams = Array();
        $this->motorDePlantilas = $engine;
    }

    public function manejadorDeAcciones() {
        if (@$_REQUEST["autoCompleteTerm"] != "") {
            $term = $_GET['q'];
            switch ($_GET['autoCompleteTerm']) {

                case "area":
                    $result = (new AreaModel())->listar(array($_REQUEST["autoCompleteTerm"] => $term), $_REQUEST["autoCompleteTerm"], "0," . $_REQUEST["limit"], false, $fields = "DISTINCT " . $_REQUEST["autoCompleteTerm"]);
                    break;

                case "nombre":
                    $result = (new AutorModel())->listar(array("primer_apellido" => $term), "primer_apellido", "0," . $_REQUEST["limit"], false, $fields = "DISTINCT CONCAT(primer_apellido, ' ', IFNULL(segundo_apellido,''), ', ', primer_nombre, ' ', IFNULL(segundo_nombre,'')) as nombre, idAutor");
                    die(json_encode($result));
                    break;

                case "editorial":
                    $result = (new EditorialModel())->listar(array($_REQUEST["autoCompleteTerm"] => $term), $_REQUEST["autoCompleteTerm"], "0," . $_REQUEST["limit"], false, $fields = "DISTINCT " . $_REQUEST["autoCompleteTerm"]);
                    break;
            }

            $aResp = array();
            if (!is_array($result))
                exit;
         	foreach ($result as $row) {
                echo $row[$_REQUEST["autoCompleteTerm"]] . "\r\n";
            }
			exit;
        }
        //Listar AJAX
        if (@$_REQUEST['sEcho'] != "") {
            die(
                    $this->libro->getPager(array("idLibro", "ISBN", "Titulo", "Año",
                                        "Area"))->getJSON());
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->guardar($_POST["idLibro"]);
        }
        if (@$_GET["accion"] == "eliminar" && $_GET["id"] > 0) {
            $this->eliminar(intval($_GET["id"]));
        }
        if (@$_GET["accion"] == "editar" && $_GET["id"] > 0) {
            $this->cargarPorId(intval($_GET["id"]));
            die(json_encode($this->aParams["libro"]));
        }
        $this->aParams["langs"] = $this->libro->getLangs();
        $this->mostarPlantilla();
    }

    private function guardar($id) {
        $this->libro->cargarPorId($id);
        $this->libro->setValues($_POST);
        $this->libro->save();
        $resp = json_encode(
                array(
                    "msg" => "El registro fue grabado. ID="
                    . $this->libro->getId(),
                    "id" => $this->libro->getId()));
        die($resp);
    }

    public function cargarPorId($id) {
        $this->libro->cargarPorId($id);
        $this->aParams["libro"] = array("idLibro" => $this->libro->getId(),
            "ISBN" => $this->libro->getISBN(),
            "Titulo" => $this->libro->getTitulo(),
            "Año" => $this->libro->getAñoPublicación(),
            "Area" => $this->libro->getArea()->getArea());
    }

    private function eliminar($id) {
        $this->libro->cargarPorId($id);
        $this->libro->eliminar();
        $this->aParams["message"] = "El registro fue eliminado";
        $resp = json_encode(array("msg" => $this->aParams["message"]));
        die($resp);
    }

    private function mostarPlantilla() {
        echo $this->motorDePlantilas->render("libro", $this->aParams);
    }

}

?>