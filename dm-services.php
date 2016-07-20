<?php

/*
Plugin Name: DM Services
Plugin URI: http://www.designmissoula.com/
Description: This is not just a plugin, it makes WordPress better.
Author: Bradford Knowlton
Version: 1.0.3
Author URI: http://bradknowlton.com/
GitHub Plugin URI: https://github.com/DesignMissoula/DM-services
*/

add_action( 'init', 'register_cpt_service' );
function register_cpt_service() {
	$labels = array(
		'name' => _x( 'Services', 'service' ),
		'singular_name' => _x( 'Service', 'service' ),
		'add_new' => _x( 'Add New', 'service' ),
		'add_new_item' => _x( 'Add New Service', 'service' ),
		'edit_item' => _x( 'Edit Service', 'service' ),
		'new_item' => _x( 'New Service', 'service' ),
		'view_item' => _x( 'View Service', 'service' ),
		'search_items' => _x( 'Search Services', 'service' ),
		'not_found' => _x( 'No services found', 'service' ),
		'not_found_in_trash' => _x( 'No services found in Trash', 'service' ),
		'parent_item_colon' => _x( 'Parent Service:', 'service' ),
		'menu_name' => _x( 'Services', 'service' ),
	);
	$args = array(
		'labels' => $labels,
		'hierarchical' => true,
		'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ), // 'author', 'editor', 'excerpt', , 'custom-fields'
		'taxonomies' => array( 'service_levels' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'has_archive' => false,
		'query_var' => true,
		'can_export' => false,
		'rewrite' => true,
		'menu_icon' => 'dashicons-hammer',
		'capability_type' => 'post'
	);
	register_post_type( 'service', $args );

	new dm_service_meta_box();
		
}

function dm_service_enqueue_style(){
	wp_register_style( 'dm-service-style', plugins_url( 'css/style.css', __FILE__ ) );
	wp_enqueue_style( 'dm-service-style' );
}

// Creating the widget
class dm_service_meta_box {

	function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'remove_meta_box' ), 100 );
		
		
	}

	function admin_init() {
	    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	    add_action( 'save_post', array( $this, 'save_post' ), 10);	    
	  }

	// Create the meta box
	function add_meta_boxes() {
	      add_meta_box(
	          'select_item',
	          'Enter Service Details',
	          array( $this, 'content' ),
	          'service',
	          'normal',
	          'high'
	      );
	}

	// Create the meta box content
	function content() {
		global $post;
		wp_nonce_field( basename( __FILE__ ), 'dm-service_nonce' );
	    $website_url = get_post_meta( $post->ID, '_website_url', true );
	    ?>
	    <table class="form-table">
		    <tbody>
		        <tr><th scope="row">
		        <label for="website_url" class="prfx-row-title"><?php _e( 'Website URL', 'dm-service' )?></label>    </th>
		 
		    <td>
		        <input type="text" name="website_url" id="website_url" class="regular-text" value="<?php if ( isset ( $website_url ) ) echo $website_url; ?>" />
		        <br>
		        <span class="description">Service Website URL.</span>
		    </td></tr>
		    </tbody>
		</table>
 
	    <?php
	   
	}

	// Save the selection
	function save_post( $post_id ) {
	    $selected_item = null;
	    
	    // Checks save status
	    $is_autosave = wp_is_post_autosave( $post_id );
	    $is_revision = wp_is_post_revision( $post_id );
	    $is_valid_nonce = ( isset( $_POST[ 'dm-service_nonce' ] ) && wp_verify_nonce( $_POST[ 'dm-service_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
	 
	    // Exits script depending on save status
	    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
	        return;
	    }
	 
	    // Checks for input and sanitizes/saves if needed
	    if( isset( $_POST[ 'website_url' ] ) ) {
	        update_post_meta( $post_id, '_website_url', sanitize_text_field( $_POST[ 'website_url' ] ) );
	    
	    }
	    	    
	}
	
	/**
	* Remove the WooThemes metabox on new page
	* @since 1.15.2
	* http://codex.gravityview.co/class-gravityview-theme-hooks-woothemes_8php_source.html
	*/
	public function remove_meta_box() {
	 remove_meta_box( 'woothemes-settings', 'contact', 'normal' );	  
	 remove_meta_box( 'woothemes-settings', 'service', 'normal' );	  
	
	}

	
}


