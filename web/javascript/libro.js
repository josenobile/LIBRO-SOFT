// Cuando se termina de cargar el DOM se ejecuta la siguiente funcion
$(function() {
	
    // Agregar al boton de mostrar el formulario la opcion de ocultar y mostrar
    $("#mostrarFormArea").bind("click", function() {
        if ($("#formularioArea").css("display") == "none") {
            $("#formularioArea").slideDown();
        } else {
            $("#formularioArea").slideUp();
        }
    });

    
    //Autocompletar de Area de Conocimiento
	$("#id_area_conocimiento").autocomplete("index.php?ac=libro&autoCompleteTerm=area",{
		minChars: 1,
		max: 1000,
		delay: 0
	}).addClass("autocomplete");
	
<<<<<<< HEAD
    //Autocompletar de Autor
	$("#autoautor").autocomplete("index.php?ac=libro&autoCompleteTerm=nombre",{
		minChars: 1,
		max: 1000,
		delay: 0
	}).addClass("autocomplete").bind("result", function(){
		
	});
	
    //Autocompletar de Editorial
	$("#id_editorial").autocomplete("index.php?ac=libro&autoCompleteTerm=editorial",{
		minChars: 1,
		max: 1000,
		delay: 0
	}).addClass("autocomplete");

    
    // Validar los campos del formulario, enviarlo por ajax y actulizar la
    // tabla!!
    var v = $("#formularioLibro")
    .validate(
    {
        rules : {
            //area : {required : true},
            
        },
        messages : {
        // varName: {required: "Este campo es requerido"},
        // paramName: {required: "Este campo es requerido"},
        // value: {required: "Este campo es requerido"}
        },
        submitHandler : function(form) {
            $(form)
            .ajaxSubmit(
            {
                dataType : "json",
                success : function(obj,
                    statusText, xhr, $form) {
                    tLibro.fnClearTable(true);// uncomment
                    $("#result").html(obj.msg);
                    // $("input[name=id]").val(obj.id);
                    // $(form).clearForm();
                    $('[name]', form).val('');
                },
                beforeSubmit : function(arr, $form, options) {
                    $("#result")
                    .html("Loading");
                },
                error : function(context, xhr, status, errMsg) {
                    $("#result")
                    .html(
                        status
                        + "<br />"
                        + context["responseText"]);
                }
            });
        }
    });

    // Agregar la funcion a los cositos de editar para que funcionen aJAX
    $(".editarArea").live("click", function(e) {// edit
        e.preventDefault();
        // var arr = {};
        // parse_str($(this).attr("href").substr(1),arr);
        $("#result").html("Loading");
        $("#formularioArea").hide();
        $.get($(this).attr("href"), function(obj) {
            for (i in obj) {
                $("#formularioArea *[name=" + i + "]").val(obj[i]);
            }
            $("#result").html("");
            $("#formularioArea").slideDown();
        // $("#result").html(obj.msg);
        // tLaeOfficeExpenses.fnClearTable(true);//uncomment
        }, "json");
        return false;
    });

    //Eliminar por AJAX con confirmacion
    $(".eliminarArea").live("click", function(e) {// delete
        e.preventDefault();
        if (confirm("Are you sure? Delete?")) {
            $.get($(this).attr("href"), function(obj) {
                $("#result").html(obj.msg);
                tArea.fnClearTable(true);// uncomment
            }, "json");
        }

        return false;
    });

    // Utilizando el plugine Jquery DataTable para hacer el consultar AJAX
    var tArea = $('#tLibro')
    .dataTable(
    {
        "bProcessing" : true,
        "bServerSide" : true,
        "sAjaxSource" : "index.php?ac=libro",
        "bSearchable" : true,
        "sScrollY" : $(window).height() * 0.99 - 377,
        "sDom" : "frtiSHF",
        "bDeferRender" : true,
        "bJQueryUI" : true,
        "sPaginationType" : "full_numbers",
        "sServerMethod" : "POST",
        "aoColumns" : [
        /* null, */{
            "bVisible" : false
        },
        null,
        null,
        null,
        {
            "bSortable" : false,
            "mDataProp" : null,
            "fnRender" : function(o) {
                return '<div style="display:block; width:120px;"><a class="editarArea" href="index.php?ac=area&accion=editar&id='
                + o.aData[0]
                + '">Editar</a> '
                + '<a class="eliminarArea" href="index.php?ac=area&accion=eliminar&id='
                + o.aData[0]
                + '">Eliminar</a></div>';
            }
        }]
    }).columnFilter({
        sPlaceHolder : "foot",
        sRangeSeparator : '~',
        aoColumns : [null, {
            type : "text"
        }, {
            type : "text"
        }, {
            type : "text"
        }, null]
    });

});