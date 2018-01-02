<?php
/*
Plugin Name: Agregador de contenidos
Plugin URI: https://github.com/rafaeldcf/AgrContendos
Description: Agregador de contenidos personalizables para insertar en paginas.
Version: 1.0
Author: Rafael del Cerro Flores
Author URI: https://github.com/rafaeldcf/AgrContendos
License: GPLv2
*/


// --------------------------------------------------------------------
// Configuracion Global
// --------------------------------------------------------------------
$post_type_name="agregador_contenidos";
$shortcode="AgregadorContenidos";
// --------------------------------------------------------------------
// Cargamos jQUERY para tener Drag & Drop
wp_enqueue_script("jquery-effects-core");
wp_enqueue_script('jquery-ui-tabs');
// --------------------------------------------------------------------








// --------------------------------------------------------------------
//                               INICIO
// FUNCIONES PARA MOSTRAR LAS OPCIONES DE CONFIGURACION (SETTINGS)
// --------------------------------------------------------------------
// Mostramos el enlace settings en el plugin dentro del menu Plugins
function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="edit.php?post_type=agregador_contenidos&page=settings">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );

// Creamos un submenú de Settings
function agrc_submenu_settings(){
	// Creamos el menu de Settings
	add_submenu_page('edit.php?post_type=agregador_contenidos', __('Settings','menu-test'), __('Settings','menu-test'), 'manage_options', 'settings', 'extra_post_page');	
	add_action( 'admin_init', 'agrc_actualiza_settings' );
}
add_action('admin_menu','agrc_submenu_settings');

// Pagina de Settings y todas las opciones
function extra_post_page(){
?>  
  <h1>Configuracion de opciones</h1>
  <form method="post" action="options.php">
    <?php settings_fields( 'extra-post-settings' ); ?>
    <?php do_settings_sections( 'extra-post-settings' ); ?>
    <table class="form-table">
      <tr valign="top">
      <th scope="row">Esquema de POSTs / PAGEs:</th>
      <td><input type="text" name="agrc_esquema" value="<?php echo get_option('agrc_esquema'); ?>"/></td>
      </tr>
    </table>
    <?php submit_button(); ?>
  </form>

<?php
}
// Guardado de las Settings que se envian. Meter nuevas lineas por cada campo
function agrc_actualiza_settings() {
	register_setting( 'extra-post-settings', 'agrc_esquema' );
}
// --------------------------------------------------------------------
//                               FIN
// --------------------------------------------------------------------

	



// --------------------------------------------------------------------
//                               INICIO
// En el listado de AGRC, quitamos las opciones no deseadas: VIEW y Quick Edit
// --------------------------------------------------------------------
add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
function remove_row_actions( $actions ){
	global $post_type_name;
    if( get_post_type() === $post_type_name ){
        unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );
//        unset( $actions['trash'] );
//        unset( $actions['edit'] );		
	}
    return $actions;
}
// --------------------------------------------------------------------
//                               FIN
// --------------------------------------------------------------------




add_shortcode( $shortcode, 'boton_shortcode' );
function boton_shortcode( $atts, $content = null ) {
	$id=$atts["id"];
	$categoria_name = esc_html( 
		get_post_meta( 
			$id,
			'categoria_name', 
			true 
		) 
	);		
	$campo_orden = esc_html( 
		get_post_meta( 
			$id,
			'campo_orden', 
			true 
		) 
	);
	$tipo_post = esc_html( 
		get_post_meta( 
			$id,
			'tipo_post', 
			true 
		) 
	);	
	/*
	$boton = '
	<table>
		<tr>
			<td style="width: 100%">Titulo</td>
			<td style="width: 150px">Orden</td>
		</tr>';
	
	$mypost = array( 
		'post_type' => $tipo_post,
		'orderby'    => 'meta_value',
		'order'      => 'ASC',
		'cat'   => $categoria_name,
		'meta_query' => array(
			array(
				'key'     => $campo_orden
			),
		
		) 
	);
	$the_query = new WP_Query( $mypost ); 
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$boton.='<tr>';
			$the_query->the_post();
			$boton.= '<td>' . get_the_title() .'</td><td>'.get_metadata('post', get_the_id(), 'orden',true).'</td>';
			$boton.= '</tr>';
			

		}
		$boton.="</table>";
		wp_reset_postdata();	
		
		
		
	} else {
		echo "No hay resultados con esos criterios.";
	}
	*/

	
// 
	$mypost = array( 
		'post_type' => $tipo_post,
		'orderby'    => 'meta_value',
		'order'      => 'ASC',
		'cat'   => $categoria_name,
		'meta_query' => array(
			array(
				'key'     => $campo_orden
			),
		
		) 
	);
$the_query = new WP_Query( $mypost ); 
?>

<div id="grid-wrapper" class="<?php echo implode( ' ', apply_filters('hu_classic_grid_wrapper_classes', array( 'post-list group') ) ) ?>">
	<div class="post-row"> 
	<?php
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			get_template_part( 'content', $tipo_post );


	
		}	
			wp_reset_postdata();
	?>
	</div><!-- post-row -->
</div>
	<?php

	
	
	return $boton;
}




// --------------------------------------------------------------------
//                               INICIO
// Elegimos que box mostramos en la edición del CTP
// --------------------------------------------------------------------
add_action( 'admin_menu', 'my_remove_meta_boxes' );
function my_remove_meta_boxes() {
		remove_meta_box( 'commentstatusdiv', $post_type_name, 'normal' );
		remove_meta_box( 'commentsdiv', $post_type_name, 'normal' );
		remove_meta_box( 'slugdiv', $post_type_name, 'normal' );
		remove_meta_box( 'submitdiv', $post_type_name, 'side' );
//		remove_meta_box( 'linktargetdiv', $post_type_name, 'normal' );
//		remove_meta_box( 'linkxfndiv', $post_type_name, 'normal' );
//		remove_meta_box( 'linkadvanceddiv', $post_type_name, 'normal' );
//		remove_meta_box( 'postexcerpt', $post_type_name, 'normal' );
//		remove_meta_box( 'trackbacksdiv', $post_type_name, 'normal' );
//		remove_meta_box( 'postcustom', $post_type_name, 'normal' );
//		remove_meta_box( 'postimagediv',$post_type_name,'normal' );
//		remove_meta_box( 'revisionsdiv', $post_type_name, 'normal' );
//		remove_meta_box( 'authordiv', $post_type_name, 'normal' );
//		remove_meta_box( 'sqpt-meta-tags', $post_type_name, 'normal' );
	
}
add_action('do_meta_boxes', 'remove_thumbnail_box');
function remove_thumbnail_box() {
//    remove_meta_box( 'postimagediv',$post_type_name,'side' );
	remove_meta_box( 'slugdiv', $post_type_name, 'normal' );
}
// --------------------------------------------------------------------
//                                 FIN
// --------------------------------------------------------------------







// --------------------------------------------------------------------
//                               INICIO
// FUNCIONES PARA MOSTRAR LAS COLUMNAS EN EL MENU DE ADMINISTRACION
// --------------------------------------------------------------------
add_filter( 'manage_edit-'.$post_type_name.'_columns', 'my_columns' );
function my_columns( $columns ) {
    $columns['shortcode'] = 'Shortcode';
	$columns['tipo'] = 'Tipo';
	$columns['categoria_name'] = 'Categoria';
    $columns['campo_orden'] = 'Campo ordenacion';
	// Quitamos la columna date
	unset( $columns['date'] );
	// Quitamos la columna comentarios
    unset( $columns['comments'] );
    return $columns;
}
add_action( 'manage_posts_custom_column', 'populate_columns' );
function populate_columns( $column ) {
	global $shortcode;
    if ( 'categoria_name' == $column ) {
        $movie_director = esc_html( get_post_meta( get_the_ID(), 'categoria_name', true ) );
        echo get_cat_name( $movie_director );
    }
    elseif ( 'campo_orden' == $column ) {
        $movie_rating = get_post_meta( get_the_ID(), 'campo_orden', true );
        echo $movie_rating;
    }elseif('shortcode' == $column){
		echo "<input type='text' value='[".$shortcode." id=".get_the_ID()."]' size='28'>";
	}elseif('tipo' == $column){
		$tipo_post = get_post_meta( get_the_ID(), 'tipo_post', true );
        echo $tipo_post;
	}
}
// --------------------------------------------------------------------
//                                 FIN
// --------------------------------------------------------------------




add_action( 'init', 'create_agregador_contenido' );
function create_agregador_contenido() { 
	global $post_type_name;
	register_post_type( $post_type_name, 
					   array( 'labels' => array( 
						   'name' => 'AgrContenidos', 
						   'singular_name' => 'Contenidos', 
						   'add_new' => 'Add New', 
						   'add_new_item' => 'Add New', 
						   'edit' => 'Edit', 
						   'edit_item' => 'Edit', 
						   'new_item' => 'New ', 
						   'view' => 'View', 
						   'view_item' => 'View ', 
						   'search_items' => 'Search ', 
						   'not_found' => 'Not found records', 
						   'not_found_in_trash' => 'No found in Trash',
						   'parent' => 'Parent' 
					   ),
							 'public' => true, 
							 'menu_position' => 15, 
							 'supports' => array( 'title', 
												 '', 
												 '', 
												 'thumbnail',
												), 
							 'taxonomies' => array( '' ), 
							 'menu_icon' => plugins_url( 'images/image.png', __FILE__ ), 
							 'has_archive' => true 
							) 
					  ); 
}

/*
add_action( 'init', 'create_my_taxonomies', 0 );
function create_my_taxonomies() {
	global $post_type_name;
    register_taxonomy(
        'movie_reviews_movie_genre',
        $post_type_name,
        array(
            'labels' => array(
                'name' => 'Movie Genre',
                'add_new_item' => 'Add New Movie Genre',
                'new_item_name' => "New Movie Type Genre"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true
        )
	);
}
*/


add_action( 'admin_init', 'my_admin' );
function my_admin() {
	global $post_type_name;
	add_meta_box( 
		'agregador_contenidos_meta_box',
		'Agregador de Contenidos',
		'display_agregador_contenidos_meta_box',
		$post_type_name, 
		'normal', 
		'high'
	);
}



function display_agregador_contenidos_meta_box( $movie_review ) {
	$categoria_name = esc_html( 
		get_post_meta( 
			$movie_review->ID,
			'categoria_name', 
			true 
		) 
	);	
	$campo_orden = esc_html( 
		get_post_meta( 
			$movie_review->ID,
			'campo_orden', 
			true 
		) 
	);	
	$tipo_post = esc_html( 
		get_post_meta( 
			$movie_review->ID,
			'tipo_post', 
			true 
		) 
	);	
?>






	<table>
		<tr>
			<td style="width: 100%">Tipo de Post</td>
			<td>
				
				<select name="tipo_post" id="tipo_post">
					<option value="post" <?php if($tipo_post=="post") echo "selected"; ?>>Post</option>
					<option value="page" <?php if($tipo_post=="page") echo "selected"; ?>>Page</option>
				</select>
			</td>
		</tr>		
		<tr>
			<td style="width: 100%">Categoria</td>
			<td>
				<select name="categoria_name" id="categoria_name">
					<option value="">-- Ninguna --</option>
				<?php
					$categorias_listado=get_categories();
					foreach ($categorias_listado AS $value){
						$cat_name=$value->name;
						$cat_id=$value->cat_ID;
						$cat_total=$value->count;
						$sel="";
						if($cat_id==$categoria_name){
							$sel="selected";
						}
						echo '<option value="'.$cat_id.'" '.$sel.'>'.$cat_name.' ('.$cat_total.')</option>';
					}
				?>
				</select>
				
				<?php //wp_dropdown_categories( 'show_count=1&hierarchical=1&id=categoria_name&name=categoria_name&selected='.$categoria_name ); ?></td>
		</tr>
		<tr>
			<td style="width: 100%">Campo ordenacion</td>
			<td>
<?php
function get_meta_values(){
	global $wpdb;
    $r = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT(pm.meta_key) FROM {$wpdb->postmeta} pm WHERE pm.meta_key NOT Like '\_%' ",null));
    return $r;
}
	$meta_values=get_meta_values();
?>
				<select name="campo_orden">
					<?php
						foreach ($meta_values AS $key=>$value){
							$sel="";
							if($value==$campo_orden){
								$sel="selected";
							}
							echo '<option value="'.$value.'" '.$sel.'>'.$value.'</option>';
						}
					?>
				</select>
				<!-- <input type="text" size="80" name="campo_orden" value="<?php echo $campo_orden; ?>" /> -->
			</td>
		</tr>				
    </table>
<hr>
<h4>Listado (Drag &amp; Drop)</h4>
<style>
	#sortable { 
		list-style-type: none; 
		margin: 0; 
		padding: 0; 
		width: 60%; 
	}
	#sortable li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; background-color: #ddd; border:1px solid #ccc; }
  html>body #sortable li { height: 1.5em; line-height: 1.2em; }
  .ui-state-highlight { height: 1.5em; line-height: 1.2em; }
	#sortable li{
		cursor: move;
	}
  </style>
  <script>
  $( function() {

	  
	  
	  } );
  </script>

<?php
// Realizamos el bucle para sacar los POST/PAGES necesarios
// Imprimimos los datos dentro de etiquetas <ul></ul>
?>
<?php 
	
	$mypost = array( 
		'post_type' => $tipo_post,
		'orderby'    => 'meta_value',
		'order'      => 'ASC',
		'cat'   => $categoria_name,
		'meta_query' => array(
			array(
				'key'     => $campo_orden,
			),
		
		) 
	);
	$the_query = new WP_Query( $mypost ); 
if ( $the_query->have_posts() ) {
	echo '<ul id="sortable" style="width:100%">';
	while ( $the_query->have_posts() ) {
		$the_query->the_post();
		$orden=get_metadata('post', get_the_id(), 'orden',true);
		echo '<li class="ui-state-default" data-id="'.get_the_id().'">';
		echo get_the_title();
		echo '</li>';
	}
	echo "</ul>";
	
	wp_reset_postdata();
} else {
	// no posts found
	echo "<i>No hay resultados con esos criterios.</i>";
}			
// FIN del bucle para mostrar POST/PAGES
?>




				
<?php 
	/*
	$mypost = array( 
		'post_type' => 'post',
		'orderby'    => 'meta_value',
		'order'      => 'ASC',
		'cat'   => $categoria_name,
		'meta_query' => array(
			array(
				'key'     => $campo_orden,
			),
		
		) 
	);
	$the_query = new WP_Query( $mypost ); 
if ( $the_query->have_posts() ) {
?>
				<table>
					<tr style="background-color:#ccc">
						<td style="width: 60%">Titulo del Post</td>
						<td style="width: 200px">Orden del campo</td>
						<td style="width: 100px">Accion</td>
					</tr>
					<?php
	while ( $the_query->have_posts() ) {
		echo '<tr style="background-color:#fff">';
		$the_query->the_post();
		$orden=get_metadata('post', get_the_id(), 'orden',true);
		echo '<td>' . get_the_title() .'</td><td>'.$orden.'</td>';
		echo '<td><a href="'.$_SERVER['REQUEST_URI'].'&item='.get_the_id().'&acc=up&orden='.$orden.'">Up</a> / <a href="'.$_SERVER['REQUEST_URI'].'&item='.get_the_id().'&acc=down&orden='.$orden.'">Down</a></td>';
		echo '</tr>';
	}
	
	
	wp_reset_postdata();
} else {
	// no posts found
	echo "<i>No hay resultados con esos criterios.</i>";
}	
	
	?>
					</table>

    <?php
	*/
}




add_action( 'save_post', 'add_agregador_contenidos_fields', 10, 2 );
function add_agregador_contenidos_fields( $movie_review_id, $movie_review ) {
	global $post_type_name;
    // Check post type for movie reviews
    if ( $movie_review->post_type == $post_type_name ) {
        // Store data in post meta table if present in post data
        if ( isset( $_POST['movie_review_director_name'] ) && $_POST['movie_review_director_name'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_director', $_POST['movie_review_director_name'] );
        }
        if ( isset( $_POST['movie_review_rating'] ) && $_POST['movie_review_rating'] != '' ) {
            update_post_meta( $movie_review_id, 'movie_rating', $_POST['movie_review_rating'] );
        }
        if ( isset( $_POST['categoria_name'] ) ) {
            update_post_meta( $movie_review_id, 'categoria_name', $_POST['categoria_name'] );
        }
        if ( isset( $_POST['campo_orden'] ) && $_POST['campo_orden'] != '' ) {
            update_post_meta( $movie_review_id, 'campo_orden', $_POST['campo_orden'] );
        }
        if ( isset( $_POST['tipo_post'] ) && $_POST['tipo_post'] != '' ) {
            update_post_meta( $movie_review_id, 'tipo_post', $_POST['tipo_post'] );
        }		
    }
}


add_filter( 'template_include','include_template_function', 1 );
function include_template_function( $template_path ) {
    if ( get_post_type() == $post_type_name ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-'.$movie_review.'.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-'.$movie_review.'php';
            }
        }
    }
    return $template_path;
}


if(isset($_GET["acc"])){
	$orden=$_GET["orden"];
	$item=$_GET["item"];
	$post=$_GET["post"];
	if($_GET["acc"]=="up" AND $orden>1){
		$orden-=1;
		update_post_meta( $item, "orden", $orden);
	}
	if($_GET["acc"]=="down"){
		$orden+=1;
		update_post_meta( $item, "orden", $orden);
	}
	header("Location:post.php?post=".$post."&action=edit");
}


// --------------------------------------------------------------------
//                               INICIO
// FUNCIONES LLAMADAS DE AJAX
// --------------------------------------------------------------------
function test_ajax_load_scripts() {
	wp_enqueue_script( "ajax-test", plugin_dir_url( __FILE__ ) . '/js/funciones.js', array( 'jquery' ) );
	wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
}
add_action('wp_print_scripts', 'test_ajax_load_scripts');
function text_ajax_process_request() {
  	if ( isset( $_POST["post_var"] ) ) {
		$response = $_POST["post_var"];
		$post_id=$_GET["post"];
		// Para cada elemento en el array
		foreach($response AS $key=>$value){
			$orden=$key+1;
			// guardamos el orden en la base de datos
			update_post_meta($value, "orden", $orden);
		}
		die();
	}
}
add_action('wp_ajax_test_response', 'text_ajax_process_request');
// --------------------------------------------------------------------
//                               FIN
// --------------------------------------------------------------------





add_action('admin_head', 'mis_botones');
function mis_botones() {
   if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
     return;
   }

   if ( get_user_option( 'rich_editing' ) !== 'true' ) {
     return;
   }

   add_filter( 'mce_external_plugins', 'nuevos_botones' );
   add_filter( 'mce_buttons', 'registrar_botones' );
}
function nuevos_botones( $plugin_array ) {
   $plugin_array['miboton'] = plugin_dir_url( __FILE__ ).'/js/shortcode.js';
   return $plugin_array;
}

function registrar_botones( $buttons ) {
   array_push( $buttons, 'miboton');
   return $buttons;
}

function twd_posts( $post_type ) {

	global $wpdb;
   	$cpt_type = $post_type;
	$cpt_post_status = 'publish';
        $cpt = $wpdb->get_results( $wpdb->prepare(
        "SELECT ID, post_title
            FROM $wpdb->posts 
            WHERE $wpdb->posts.post_type = %s
            AND $wpdb->posts.post_status = %s
            ORDER BY ID DESC",
        $cpt_type,
        $cpt_post_status
    ) );

    $list = array();

    foreach ( $cpt as $post ) {
		$selected = '';
		$post_id = $post->ID;
		$post_name = $post->post_title;
		$list[] = array(
			'text' =>	$post_name,
			'value'	=>	$post_id
		);
	}

	wp_send_json( $list );
}
function twd_list_ajax() {
	// check for nonce
	check_ajax_referer( 'twd-nonce', 'security' );
	$posts = twd_posts( 'agregador_contenidos' );
	return $posts;
}
add_action( 'wp_ajax_twd_cpt_list', 'twd_list_ajax' );

/**
 * Function to output button list ajax script
 * @since  1.6
 * @return string
 */
function twd_cpt_list() {
	// create nonce
	global $pagenow, $shortcode;
	if( $pagenow != 'admin.php' ){
		$nonce = wp_create_nonce( 'twd-nonce' );
		?><script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				var data = {
					'action'	: 'twd_cpt_list', // wp ajax action
					'security'	: '<?php echo $nonce; ?>' // nonce value created earlier
				};
				// fire ajax
			  	jQuery.post( ajaxurl, data, function( response ) {
			  		// if nonce fails then not authorized else settings saved
			  		if( response === '-1' ){
				  		// do nothing
				  		console.log('error');
			  		} else {
			  			if (typeof(tinyMCE) != 'undefined') {
			  				if (tinyMCE.activeEditor != null) {
								tinyMCE.activeEditor.settings.cptPostsList = response;
								tinyMCE.activeEditor.settings.shortcode = '<?php echo $shortcode; ?>';
							}
						}
			  		}
			  	});
			});
		</script>
<?php 
	}
}
add_action( 'admin_footer', 'twd_cpt_list' );






// --------------------------------------------------------------------
//                               INICIO
// Añadimos un metabox para el estilo del listado de post
// --------------------------------------------------------------------
add_action( 'add_meta_boxes', 'add_metabox_estilos' );
function add_metabox_estilos(){
    add_meta_box( 'metabox_estilos', 'Estilo del layout', 'metabox_estilos', 'agregador_contenidos', 'normal');
}
function metabox_estilos(){
    echo 'Selecciona el estilo que quieres para tus POST/PAGEs:'; 
	?>
	<table style="width: 100%">
		<tr style="background-color:#ccc;">
			<td>Estilo 1</td>
			<td>Estilo 2</td>
		</tr>
		<tr>
			<td>Aqui va el estilo 1</td>
			<td>Aqui va el estilo 2</td>
		</tr>		
	</table>
	<?
}


?>