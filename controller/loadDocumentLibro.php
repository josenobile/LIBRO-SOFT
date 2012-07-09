<?php
date_default_timezone_set("America/Bogota");
//ob_start();
require_once '../lib/DBNative.php';
// CONFIG DB
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_SERVER", "localhost");
define("DB_NAME", "LIBROSOFT");

define("DB_USER_LOCAL", "root");
define("DB_PASS_LOCAL", "");
define("DB_SERVER_LOCAL", "localhost");
define("DB_NAME_LOCAL", "LIBROSOFT");
// END CONFIG

if (in_array($_SERVER['SERVER_ADDR'], array("127.0.0.1", "localhost")))
{
	define("DSN", "mysql://" . DB_USER_LOCAL . ":" . DB_PASS_LOCAL . "@" .
		DB_SERVER_LOCAL . "/" . DB_NAME_LOCAL);
} else
	define("DSN", "mysql://" . DB_USER . ":" . DB_PASS . "@" . DB_SERVER . "/" .
		DB_NAME);		
class LoadDocument {
    var $dbc;
    
    function LoadDocument($documentID) {
        $this->dbc = DBNative::get(DSN);
        
        $sql = <<<EOT
SELECT titulo, archivo, archivo_content_type
FROM Libro
WHERE idLibro = %s        
EOT;

        $tSql = sprintf($sql, $this->dbc->quote($documentID));
        $rst = $this->dbc->query($tSql);
        $rst = $rst[0];

        if(empty($rst)) {
            ?>
<script type="text/javascript">
alert("Document Not Uploaded");
//history.back();
</script>
<?PHP
            exit(0);
			
        }
		$content = $rst["archivo"];
		$contentLength = strlen($content);
        
        if($contentLength == 0) {
            ?>
<script type="text/javascript">
alert("The document was not uploaded properly, document is empty.");
//history.back();
</script>
<?PHP
            exit(0);
			
        }
        while(ob_get_contents() != '')
			ob_end_clean();//clear buffer
        header('Content-Description: File Transfer');        
        header("Content-Type: {$rst["archivo_content_type"]}");        
        header("Content-Disposition: inline; filename=\"{$rst["titulo"]}\"");
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.$contentLength);        
        echo $content;
        exit(0);
    }
}

if(!empty($_GET["id"])){
    $loadDoc = new LoadDocument($_GET["id"]);
}
?>
