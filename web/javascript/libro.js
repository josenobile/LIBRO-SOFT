// Cuando se termina de cargar el DOM se ejecuta la siguiente funcion
$(function() {
	
    // Agregar al boton de mostrar el formulario la opcion de ocultar y mostrar
    $("#mostrarFormLibro").bind("click", function() {
        if ($("#formularioLibro").css("display") == "none") {
            $("#formulariLibro").slideDown();
        } else {
            $("#formularioLibro").slideUp();
        }
    });

    
    //Autocompletar de Area de Conocimiento
	$("#areaAutoCompletar").autocomplete("index.php?ac=libro&autoCompleteTerm=area",{
		minChars: 1,
                parse: function(data) {
                        data = jQuery.parseJSON(data);
			return $.map(data, function(row) {
                                return {
					data: [row.area, row.idArea],
					value: row.area,
					result: row.area
				}
			});
		},
		max: 1000,
		delay: 0
	}).addClass("autocomplete").bind("result", function(e, row, nombre ){
		$('input:hidden').filter('[name=id_area_conocimiento]').val(row[1]);
		//$("<input type='hidden' name='id_area_conocimiento' value='"+row[1]+"' />").insertBefore($("#areaAutoCompletar"));
	});
	
    //Autocompletar de Autor
	$("#autoautor").autocomplete("index.php?ac=libro&autoCompleteTerm=nombre",{
		minChars: 1,
                parse: function(data) {
                        data = jQuery.parseJSON(data);
			return $.map(data, function(row) {
                                return {
					data: [row.nombre, row.idAutor],
					value: row.nombre,
					result: row.nombre
				}
			});
		},
		max: 1000,
		delay: 0
	}).addClass("autocomplete").bind("result", function(e, row, nombre ){
		$("<input type='hidden' name='idAutor[]' value='"+row[1]+"' />").insertBefore($("#autoautor"));
		$("<span class='labelAutor'>"+nombre+"<span class='closeAutoAutor'></span></span>").appendTo("#autores").
		find(".closeAutoAutor").data("id", row[1]).bind("click", function(){
			$('input:hidden').filter('[value="'+$(this).data("id")+'"]').remove();
			$(this).parent().remove();
        });
        $("#autoautor").val('');
	});
	
    //Autocompletar de Editorial
	$("#editorialAutoCompletar").autocomplete("index.php?ac=libro&autoCompleteTerm=editorial",{
		minChars: 1,
                parse: function(data) {
                        data = jQuery.parseJSON(data);
			return $.map(data, function(row) {
                                return {
					data: [row.editorial, row.idEditorial],
					value: row.editorial,
					result: row.editorial
				}
			});
		},
		max: 1000,
		delay: 0
	}).addClass("autocomplete").bind("result", function(e, row, nombre ){
		$('input:hidden').filter('[name=id_editorial]').val(row[1]);
		//$("<input type='hidden' name='id_editorial' value='"+row[1]+"' />").insertBefore($("#editorialAutoCompletar"));
	});
    
    // Validar los campos del formulario, enviarlo por ajax y actulizar la
    // tabla!!
    var v = $("#formularioLibro")
    .validate(
    {
        rules : {
            titulo : {required : true},
			ISBN : {required : true},
			areaAutoCompletar : {required: true},
			idioma : {required : true},
			palabras_claves : {required : true}            
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
    $(".editarLibro").live("click", function(e) {// edit
        e.preventDefault();
        // var arr = {};
        // parse_str($(this).attr("href").substr(1),arr);
        $("#result").html("Loading");
        $("#formularioLibro").hide();
        $.get($(this).attr("href"), function(obj) {
            for (i in obj) {
                $("#formularioLibro *[name=" + i + "]").val(obj[i]);
            }
            $("#result").html("");
            $("#formularioLibro").slideDown();
        // $("#result").html(obj.msg);
        // tLaeOfficeExpenses.fnClearTable(true);//uncomment
        }, "json");
        return false;
    });

    //Eliminar por AJAX con confirmacion
    $(".eliminarLibro").live("click", function(e) {// delete
        e.preventDefault();
        if (confirm("Are you sure? Delete?")) {
            $.get($(this).attr("href"), function(obj) {
                $("#result").html(obj.msg);
                tLibro.fnClearTable(true);// uncomment
            }, "json");
        }

        return false;
    });

    // Utilizando el plugine Jquery DataTable para hacer el consultar AJAX
    var tLibro = $('#tLibro')
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
                return '<div style="display:block; width:120px;"><a class="editarLibro" href="index.php?ac=libro&accion=editar&id='
                + o.aData[0]
                + '">Editar</a> '
                + '<a class="eliminarLibro" href="index.php?ac=libro&accion=eliminar&id='
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