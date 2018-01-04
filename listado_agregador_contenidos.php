<?php
	// Si esta activo el CheckBox significa mostrar subpaginas --> post-parent = '' para mostrarlas
	// Si NO esta activo el CheckBox significa  NO mostrar subpaginas --> post-parent = '0' para no mostrarlas
	if($subpaginas==""){
		$subpaginas_filtro='0';
	}else{
		$subpaginas_filtro='';
	}
	if($pagina_padre!=""){
		$subpaginas_filtro=$pagina_padre;
	}
	$mypost = array( 
		'post_type' => $tipo_post,
		'post_parent' => $subpaginas_filtro,
		'exclude' => '246',
		'orderby'    => 'meta_value',
		'order'      => 'ASC',
		'cat'   => $categoria_name,
		'meta_query' => array(
			array(
				'key'     => $campo_orden
			),
		
		) 
	);
// print_r($mypost);
$the_query = new WP_Query( $mypost ); 

?>

<div id="grid-wrapper" class="<?php echo implode( ' ', apply_filters('hu_classic_grid_wrapper_classes', array( 'post-list group') ) ) ?>">
	<div class="post-row"> 
		
	<?php
		while ( $the_query->have_posts() ) {	
			$the_query->the_post();
			include(plugin_dir_path( __FILE__ ) . "templates/template1.php");
		
		}	
			wp_reset_postdata();
	?>
	</div><!-- post-row -->
</div>