jQuery(document).ready( function($) {
// FUNCION PARA DRAG&DROP DE LOS POST/PAGES 
	if( $('#sortable').length ){
		$("#sortable").sortable({
			placeholder: "ui-state-highlight"
		});
		$("#sortable").disableSelection();
		$("#sortable").sortable({
			update: function (event, ui) {
				var list = new Array();
				$('#sortable').find('.ui-state-default').each(function () {
					var id = $(this).attr('data-id');
					list.push(id);
				});
				// var listado = JSON.stringify(list);
				var variables_post = {
					action: 'test_response',
					post_var: list
				};
				$.ajax({
					type: "POST",
					url: the_ajax_script.ajaxurl, 
					data: variables_post,
					success: function(response) {
						// alert(response);
					}
				});
			}
		});
	}
// FIN DE FUNCION DRAG&DROP	
	
    
});