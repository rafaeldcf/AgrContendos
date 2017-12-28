<?php
/*
Plugin Name: Agregador de contenidos
Plugin URI: http://rafaeldelcerroflores.com/
Description: Agregador de contenidos personalizables para insertar en paginas.
Version: 1.0
Author: Rafael del Cerro Flores
Author URI: http://rafaeldelcerroflores.com/
License: GPLv2
*/

$post_type_name="agregador_contenidos";
$shortcode="AgregadorContenidos";




add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );
function remove_row_actions( $actions ){
	global $post_type_name;
    if( get_post_type() === $post_type_name )
//        unset( $actions['edit'] );
        unset( $actions['view'] );
//        unset( $actions['trash'] );
        unset( $actions['inline hide-if-no-js'] );
    return $actions;
}



add_shortcode( 'boton', 'boton_shortcode' );
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
	
	$boton = '
	<table>
		<tr>
			<td style="width: 100%">Titulo</td>
			<td style="width: 150px">Orden</td>
		</tr>';
	
	$mypost = array( 
		'post_type' => 'post',
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
	

	
// 
	$mypost = array( 
		'post_type' => 'post',
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
	<?php
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			get_template_part( 'content', 'page' );


	
		}	
			wp_reset_postdata();
	?>
</div>
	<?php

	
	
	return $boton;
}



add_action( 'admin_menu', 'my_remove_meta_boxes' );
function my_remove_meta_boxes() {
	
//		remove_meta_box( 'linktargetdiv', 'movie_reviews', 'normal' );
//		remove_meta_box( 'linkxfndiv', 'movie_reviews', 'normal' );
//		remove_meta_box( 'linkadvanceddiv', 'movie_reviews', 'normal' );
//		remove_meta_box( 'postexcerpt', 'movie_reviews', 'normal' );
//		remove_meta_box( 'trackbacksdiv', 'movie_reviews', 'normal' );
//		remove_meta_box( 'postcustom', 'movie_reviews', 'normal' );
		remove_meta_box( 'commentstatusdiv', $post_type_name, 'normal' );
		remove_meta_box( 'commentsdiv', $post_type_name, 'normal' );
//		remove_meta_box( 'revisionsdiv', 'movie_reviews', 'normal' );
//		remove_meta_box( 'authordiv', 'movie_reviews', 'normal' );
//		remove_meta_box( 'sqpt-meta-tags', 'movie_reviews', 'normal' );
	
}




// --------------------------------------------------------------------
//                               INICIO
// FUNCIONES PARA MOSTRAR LAS COLUMNAS EN EL MENU DE ADMINISTRACION
// --------------------------------------------------------------------
add_filter( 'manage_edit-'.$post_type_name.'_columns', 'my_columns' );
function my_columns( $columns ) {
    $columns['shortcode'] = 'Shortcode';
	$columns['categoria_name'] = 'Categoria';
    $columns['campo_orden'] = 'Campo ordenacion';
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
		echo "<span style='border:1px solid #aab; padding:2px 5px;background-color:#fff;'>[".$shortcode."-".get_the_ID()."]</span>";
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
// Retrieve current name of the Director and Movie Rating based on review ID
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
?>
	<table>
		<tr>
			<td style="width: 100%">Categoria</td>
			<td><?php wp_dropdown_categories( 'show_count=1&hierarchical=1&id=categoria_name&name=categoria_name&selected='.$categoria_name ); ?>
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
<h4>Preview Listado</h4>


				
<?php 
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
	
	/* Restore original Post Data */
	wp_reset_postdata();
} else {
	// no posts found
	echo "<i>No hay resultados con esos criterios.</i>";
}			
	?>
					</table>

    <?php
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
        if ( isset( $_POST['categoria_name'] ) && $_POST['categoria_name'] != '' ) {
            update_post_meta( $movie_review_id, 'categoria_name', $_POST['categoria_name'] );
        }
        if ( isset( $_POST['campo_orden'] ) && $_POST['campo_orden'] != '' ) {
            update_post_meta( $movie_review_id, 'campo_orden', $_POST['campo_orden'] );
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

?>