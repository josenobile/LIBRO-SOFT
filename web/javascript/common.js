// JavaScript Document
$(function() {
	// Agregar la capaciada de crecer a los inputs cuando se le agrege mucho
	// texto
	$('input').autoGrowInput({
		comfortZone : 24,
		minWidth : 150,
		maxWidth : 800
	});

	// Clear
	$(".clear").live("click", function() {
		$('[name]', $(this).parents("form")).val('');
		$("#result").html('')
	});

	// Selector de todos los thead que esten dentro de table para agregarles
	// unas clases que le dan estilos de JqueryUI
	$("table thead").addClass("ui-widget-header").parent().children("tbody")
			.addClass("ui-widget-content");

	// cambio de color en las filas dinamico por donde pase el mouse
	$("table tr").live("mouseover", function() {
		$(this).addClass("ui-state-highlight");
	}).live("mouseout", function() {
		$(this).removeClass("ui-state-highlight");
	});

	// efecto zebra estatico
	$("table tr:even").addClass("alt");
	
	$.fn.themeswitcher && $('<div/>').css({
		position: "absolute",
		right: 10,
		top: 10
	}).appendTo(document.body).themeswitcher();
	$.validator.setDefaults({
		highlight: function(input) {
			$(input).addClass("ui-state-highlight");
		},
		unhighlight: function(input) {
			$(input).removeClass("ui-state-highlight");
		}
	});
	$("button, input:submit, input:reset, input:button, a[href*='\\?ac']").button();
		//$j("table.ui-widget").css("font-size", "14px");
	$("input:text:not(.ui-corner-all), select:not(.ui-corner-all), textarea:not(.ui-corner-all)").addClass("text ui-widget-content ui-corner-all");
});