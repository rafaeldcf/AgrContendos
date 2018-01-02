(function () {
	tinymce.PluginManager.add('miboton', function (editor, url) {
		editor.addButton('miboton', {
			text: 'ShortCode',
			icon: true,
			image: url+'/../images/icon.png',
			onclick: function () {
				editor.windowManager.open({
					title: 'Título del shortcode',
					body: [
						{
							type: 'listbox',
							name: 'shortcode',
							label: 'Agregador de Contenido',
							'values': editor.settings.cptPostsList
						}
					],
					onsubmit: function (e) {
						editor.insertContent('['+editor.settings.shortcode+' id=' + e.data.shortcode + ']');
					}
				});
			}
		});
	});
})();