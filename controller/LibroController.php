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
                    $result = (new AreaModel())
                    ->listar(array("area" => $term), area, "0," . $_REQUEST["limit"], false, $fields = "area, idArea");
                    die(json_encode($result));
                    break;

                case "nombre":
                    $result = (new AutorModel())
                    ->listar(array("primer_apellido" => $term), "primer_apellido", "0," . $_REQUEST["limit"], false, $fields = "DISTINCT CONCAT(primer_apellido, ' ', IFNULL(segundo_apellido,''), ', ', primer_nombre, ' ', IFNULL(segundo_nombre,'')) as nombre, idAutor");
                    die(json_encode($result));
                    break;

                case "editorial":
                    $result = (new EditorialModel())
                    ->listar(array("editorial" => $term), "editorial", "0," . $_REQUEST["limit"], false, $fields = "editorial, idEditorial");
                    die(json_encode($result));
                    break;
            }
        }
        //Listar AJAX
        if (@$_REQUEST['sEcho'] != "") {
            die(
                    $this->libro
                            ->getPager(
                                    array("idLibro", "ISBN", "Titulo", "Año",
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
        //asignarle la fecha de ingreso (fecha hora actual)
        $this->libro->setFechaIngreso(date("Y-m-d H:i:s"));
        //Revisar si desea borrar la caratula
        if($_POST["borrarCaratula"] != ''){
        	$this->libro->setCaratulaSize(NULL);
        	$this->libro->setCaratulaContentType(NULL);
        	$this->libro->setCaratula(NULL);
        }
        //Revisar si desea borrar el Libro
        if($_POST["borrarArchivo"] != ''){
        	$this->libro->setArchivoSize(NULL);
        	$this->libro->setArchivoContentType(NULL);
        	$this->libro->setArchivo(NULL);
        }
        //revisar si cargo la caratula y/o el libro para guardarlos
        if (file_exists($_FILES["caratula"]["tmp_name"])) {
            $this->libro->setCaratulaSize($_FILES["caratula"]["size"]);
            $this->libro->setCaratulaContentType($_FILES["caratula"]["type"]);
            $this->libro
                    ->setCaratula(
                            file_get_contents($_FILES["caratula"]["tmp_name"]));
        }
        //revisar si cargo la caratural y/o el libro para guardarlos
        if (file_exists($_FILES["archivo"]["tmp_name"])) {
            $this->libro->setArchivoSize($_FILES["archivo"]["size"]);
            $this->libro->setArchivoContentType($_FILES["archivo"]["type"]);
            $this->libro
                    ->setArchivo(
                            file_get_contents($_FILES["archivo"]["tmp_name"]));
        }
        //Guardo el Libro
        $this->libro->save();
        //Limpiar los anteriores autores relacionados
        $libroAutorObjs = (new LibroAutor())
        ->listarObj(array("id_libro" => $this->libro->getId()), "id_autor", "0,30", true);
        foreach($libroAutorObjs as $libroAutor)
        	$libroAutor->eliminar();
        //Se guarda las relaciones muchos a muchos de Libro y Autor
        foreach ($_POST["idAutor"] as $idAutor) {
            $libroAutor = new LibroAutor();
            $libroAutor->setIdLibro($this->libro->getId());
            $libroAutor->setIdAutor($idAutor);
            $libroAutor->save();
        }
        $resp = json_encode(
                array(
                    "msg" => "El registro fue grabado. ID="
                    . $this->libro->getId(),
                    "id" => $this->libro->getId()));
        die($resp);
    }

    public function cargarPorId($id) {
        $this->libro->cargarPorId($id);
        $libroAutorObjs = (new LibroAutor())
                ->listarObj(array("id_libro" => $id), "id_autor", "0,30", true);
        $autores = array();
        foreach($libroAutorObjs as $libroAutor){
        	$autor = new Autor();
        	$autor->cargarPorId($libroAutor->getIdAutor());
        	$autores[$libroAutor->getIdAutor()] = $autor->getPrimerApellido()." ".$autor->getSegundoApellido()." ".$autor->getPrimerNombre()." ".$autor->getSegundoNombre();
        }
        $this->aParams["libro"] = array(
        	"idLibro" => $this->libro->getId(),
            "ISBN" => $this->libro->getISBN(),
            "titulo" => $this->libro->getTitulo(),
            "año_publicación" => $this->libro->getAñoPublicación(),
            "id_area_conocimiento" => $this->libro->getArea()->getId(),
            "areaAutoCompletar" => $this->libro->getArea()->getArea(),
            "idioma" => $this->libro->getIdioma(),
            "palabras_claves" => $this->libro->getPalabrasClaves(),
            "id_editorial" => $this->libro->getEditorial()->getId(),
            "editorialAutoCompletar" => $this->libro->getEditorial()
                    ->geteditorial(),
            "autores" => $autores);
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