<?PHP
$this->extend('layout');
$this->javascripts->add('LIBRO-SOFT/web/javascript/libro.js');
echo $msg;
?>
<h1>Libro</h1>
<input type="button" id="mostrarFormLibro" value="Mostrar Formulario"  />
<form action="" method="post" enctype="application/x-www-form-urlencoded" id="formularioLibro">
    <input type="hidden" name="idArea"  />
    <table width="200" border="0">
        <tr>
            <td>Titulo</td>
            <td><input type="text" name="titulo" id="titulo"  /></td>
        </tr>
        <tr>
            <td>ISBN</td>
            <td><input type="text" name="ISBN" id="ISBN"  /></td>
        </tr>
        <tr>
            <td>Año Publicación</td>
            <td><input type="text" name="año_publicación" id="año_publicación"  /></td>
        </tr>
        <tr>
            <td>Área de Conocimiento</td>
            <td><input type="text" name="id_area_conocimiento" id="id_area_conocimiento"  /></td>
        </tr>
        <tr>
            <td>Autores</td>
            <td>
           <div id="autores">
           
           </div>
            <input type="text" id="autoautor"  /></td>
        </tr>
        <tr>
            <td>Idioma</td>
            <td><input type="text" name="idioma" id="idioma"  /></td>
        </tr>
        <tr>
            <td>Palabras Claves</td>
            <td><input type="text" name="palabras_claves" id="palabras_claves"  /></td>
        </tr>
        <tr>
            <td>Editorial</td>
            <td><input type="text" name="id_editorial" id="id_editorial"  /></td>
        </tr>
        <tr>
            <td>Caratula</td>
            <td><input type="file" name="caratula" /></td>
        </tr>
        <tr>
            <td>Archivo</td>
            <td><input type="file" name="archivo" /></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" value="Enviar" />
                <input type="button" class="clear" value="Limpiar"/></td>
        </tr>
    </table>
</form>
<div id="result"> </div>

<table id="tLibro">
    <thead>
        <tr>
            <th>idLibro</th>
            <th>ISBN</th>
            <th>Titulo</th>
            <th>Año</th>
            <th>Area</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>idLibro</th>
            <th>ISBN</th>
            <th>Titulo</th>
            <th>Año</th>
            <th>Area</th>
        </tr>
    </tfoot>
    <tbody>
      
    </tbody>
</table>
