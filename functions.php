<?php
/**
 * Functions and definitions for unavoidabledisaster.com
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/jetpack.php',                         // Load Jetpack compatibility file.
	'/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567
	'/woocommerce.php',                     // Load WooCommerce functions.
	'/editor.php',                          // Load Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
);

function debug( $text ) {
    echo '<script>console.log("DEBUG: ' . $text . '")</script>';
}

// debug("Why are you reading the console?");

foreach ( $understrap_includes as $file ) {
	require_once get_template_directory() . '/inc' . $file;
}

/* everything beyond here was added by wmodes */

// enqueue our styles and scripts
//
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
	// Get the theme data
	$the_theme = wp_get_theme();
    // wp_enqueue_style( 'child-understrap-styles',
    //     get_stylesheet_directory_uri() . '/css/child-theme.min.css',
    //     array(), $the_theme->get( 'Version' ) );
    $css_file = '/css/site.css';
    wp_enqueue_style( 'site-specific-styles', get_theme_file_uri($css_file),
        array(), filemtime(get_theme_file_path($css_file)) );
    // wp_enqueue_style( 'site-specific-styles',
    //     get_stylesheet_directory_uri() . '/css/site.css',
    //     array(), $the_theme->get( 'Version' ) );
    // sideline default jquery and replace with latest
    // wp_deregister_script( 'jquery' );
    // wp_enqueue_script( 'script-name', '//code.jquery.com/jquery-2.2.4.min.js', array(), '3.4.1' );
    // wp_enqueue_script( 'child-understrap-scripts',
    //     get_stylesheet_directory_uri() . '/js/child-theme.min.js',
    //     array(), $the_theme->get( 'Version' ), true );
    $js_file = '/js/site.js';
    wp_enqueue_script( 'site-specific-scripts', get_theme_file_uri($js_file),
        array('jquery'), filemtime(get_theme_file_path($js_file)) );
    // wp_enqueue_script( 'site-specific-scripts',
    //     get_stylesheet_directory_uri() . '/js/site.js',
    //     array('jquery'), $the_theme->get( 'Version' ) );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

add_action( 'admin_enqueue_scripts', 'enqueue_admin_scripts' );
function enqueue_admin_scripts( $pagehook ) {
    // do nothing if we are not on the target pages
    if ( 'edit.php' != $pagehook ) {
        return;
    }
    $js_file = '/js/admin.js';
    wp_enqueue_script( 'site-admin-scripts', get_theme_file_uri($js_file),
        array('jquery'), filemtime(get_theme_file_path($js_file)) );
}

// disable default image sizes
//
function remove_default_image_sizes( $sizes) {
    unset( $sizes['large']); // Added to remove 1024
    unset( $sizes['thumbnail']);
    unset( $sizes['medium']);
    unset( $sizes['medium_large']);
    unset( $sizes['1536x1536']);
    unset( $sizes['2048x2048']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'remove_default_image_sizes');
//
// add additional image sizes
//
remove_image_size( 'thumbnail' );
add_image_size( 'Thumbnail', 200, 200 );
add_image_size( 'Tiny', 100, 100, false );
add_image_size( 'Small', 300, 300, false );
add_image_size( 'Medium-Small', 450, 450, false );
remove_image_size( 'medium' );
add_image_size( 'Medium', 600, 600, false );
add_image_size( 'Medium-Large', 750, 750, false );
remove_image_size( 'large' );
add_image_size( 'Large', 900, 900, false );
add_image_size( 'Huge', 1200, 1200, false );
add_filter( 'big_image_size_threshold', '__return_false' );

// add custom columns in back end list view
//  * featured image
//  * expires - how many issues
//  * added - has this been added to current issue?
//  * slug
//
add_filter('manage_thing_posts_columns' , 'custom_columns');
function custom_columns( $defaults ) {
    $i = 1;
    $columns = array();
    foreach( $defaults as $key => $value ) {
        if ( 3 == $i++ ) {
            $columns['featured_image'] = __( 'Featured Image', 'my-text-domain' );
            $columns['ad_expires'] = __( 'Expires', 'my-text-domain' );
            $columns['ad_added'] = __( 'Added', 'my-text-domain' );
            $columns['slug'] = __( 'Slug', 'my-text-domain' );
        }
        $columns[$key] = $value;
    }
    return $columns;
}
add_action( 'manage_thing_posts_custom_column' , 'custom_columns_data', 10, 2 );
function custom_columns_data( $column, $post_id ) {
    switch ( $column ) {
        case 'featured_image' :
            the_post_thumbnail( 'thumbnail' );
            break;
        case 'ad_expires' :
            echo get_field('ad_expires');
            break;
        case 'ad_added' :
            // echo get_field('ad_added');
            if (get_field('ad_added') == 1) {
                echo '<input type="checkbox" name="ad_added" checked disabled />';
            } else {
                echo '<input type="checkbox" name="ad_added" disabled />';
            }
            break;
        case 'slug' :
            echo get_post_field( 'post_name', $id, 'raw' );
            break;
    }
}

// Make columns sortable
//
add_filter( 'manage_edit-thing_sortable_columns', 'set_custom_things_sortable_columns' );
function set_custom_things_sortable_columns( $columns ) {
  $columns['ad_expires'] = 'ad_expires';
  $columns['ad_added'] = 'ad_added';
  unset($columns['title']);
  return $columns;
}
//
// teach wp to sort these columns
add_action( 'pre_get_posts', 'things_custom_orderby' );
function things_custom_orderby( $query ) {
  if ( ! is_admin() )
    return;
  $orderby = $query->get( 'orderby');
  switch ($orderby) {
    case 'ad_expires' :
        $query->set( 'meta_key', 'ad_expires' );
        $query->set( 'orderby', 'meta_value_num' );
        break;
    case 'ad_added' :
        $query->set( 'meta_key', 'ad_added' );
        $query->set( 'orderby', 'meta_value_num' );
        break;
  }
  $query->set( 'posts_per_page', -1);
}

// Modify quick edit
// Ref: https://codex.wordpress.org/Plugin_API/Action_Reference/quick_edit_custom_box
//
// Print checkbox in Quick Edit for each custom column.
add_action( 'quick_edit_custom_box', 'quick_edit_add', 10, 2 );
function quick_edit_add( $column_name, $post_type ) {
    switch ( $column_name ) {
        case 'ad_added' :
            if (get_field('ad_added') == 1) {
                $value = "checked";
            } else {
                $value = '';
            }
            echo '<label class="alignleft">
                    <input type="checkbox"' . $value . ' name="ad_added">
                    <span class="checkbox-title">Added to issue</span>
                </label>';
        break;
    }
}
//
// Quick Edit Save
add_action( 'save_post', 'quick_edit_save' );
function quick_edit_save( $post_id ){
    // check user capabilities
    if ( !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    // // check nonce
    // if ( !wp_verify_nonce( $_POST['misha_nonce'], 'misha_q_edit_nonce' ) ) {
    //     return;
    // }
    // // update the price
    // if ( isset( $_POST['price'] ) ) {
    //     update_post_meta( $post_id, 'product_price', $_POST['price'] );
    // }
    // update checkbox
    if ( isset( $_POST['ad_added'] ) ) {
        update_post_meta( $post_id, 'ad_added', '1' );
    } else {
        update_post_meta( $post_id, 'ad_added', '' );
    }
}

// add CPT count to admin user columns
// ref: https://wordpress.stackexchange.com/questions/3233/showing-users-post-counts-by-custom-post-type-in-the-admins-user-list
//
add_action('manage_users_columns','yoursite_manage_users_columns');
function yoursite_manage_users_columns($columns) {
    // we still want to show posts, i.e., issues
    // unset($column_headers['posts']);
    // $inserted = array('custom_posts' => 'Things');
    // array_splice( $column_headers, 6, 0, $inserted );
    $new_columns = array();
    foreach($columns as $key => $title) {
        $new_columns[$key] = $title;
        if ($key=="posts")
            $new_columns['custom_posts'] = 'Things';
    }
    // $column_headers['custom_posts'] = 'Things';
    return $new_columns;
}
//
add_action('manage_users_custom_column','yoursite_manage_users_custom_column',10,3);
function yoursite_manage_users_custom_column($custom_column,$column_name,$user_id) {
    if ($column_name=='custom_posts') {
        $counts = _yoursite_get_author_post_type_counts();
        $custom_column = array();
        if (isset($counts[$user_id]) && is_array($counts[$user_id]))
            foreach($counts[$user_id] as $count) {
                $link = admin_url() . "edit.php?post_type=" . $count['type']. "&author=".$user_id;
                // admin_url() . "edit.php?author=" . $user->ID;
                $custom_column[] = $count['count'];
            }
        $custom_column = implode("\n",$custom_column);
        if (empty($custom_column))
             $custom_column = "0";
    }
    return $custom_column;
}
//
function _yoursite_get_author_post_type_counts() {
    static $counts;
    if (!isset($counts)) {
        global $wpdb;
        global $wp_post_types;
        $sql = <<<SQL
        SELECT
        post_type,
        post_author,
        COUNT(*) AS post_count
        FROM
        {$wpdb->posts}
        WHERE 1=1
        AND post_type='thing'
        AND post_status IN ('publish','pending', 'draft')
        GROUP BY
        post_type,
        post_author
SQL;
        $posts = $wpdb->get_results($sql);
        foreach($posts as $post) {
            $post_type_object = $wp_post_types[$post_type = $post->post_type];
            if (!empty($post_type_object->label))
                $label = $post_type_object->label;
            else if (!empty($post_type_object->labels->name))
                $label = $post_type_object->labels->name;
            else
                $label = ucfirst(str_replace(array('-','_'),' ',$post_type));
            if (!isset($counts[$post_author = $post->post_author]))
                $counts[$post_author] = array();
            $counts[$post_author][] = array(
                'thing' => $label,
                'count' => $post->post_count,
                'type' => $post->post_type,
                );
        }
    }
    return $counts;
}


// Allow negative z-index in elementor
//
add_action( 'elementor/element/common/_section_style/after_section_end', 'drank_allow_z_index_in_controls', 10, 2 );
add_action( 'elementor/element/column/section_advanced/after_section_end', 'drank_allow_z_index_in_controls', 10, 2 );
add_action( 'elementor/element/section/section_advanced/after_section_end', 'drank_allow_z_index_in_controls', 10, 2 );

function drank_allow_z_index_in_controls( $control_stack, $args ) {
    $control_name = '_z_index';
    if ( 'elementor/element/common/_section_style/after_section_end' !== current_action() ) {
        $control_name = 'z_index';
    }
    $control = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $control_stack->get_unique_name(), $control_name );
    if ( is_wp_error( $control ) ) {
        return;
    }
    $control['min'] = -99999;
    $control_stack->update_control( $control_name, $control );
}

// get first words - helper function
function get_first_words($sentence, $count = 10) {
  preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
  return $matches[0];
}

// Handle CF7 form submission and populate new CPT 'Thing'
// Ref: https://wordpress.stackexchange.com/questions/328429/how-to-save-contact-form-7-data-in-custom-post-types-cpt
//
// TODO: Check to see that this is the right form
//
add_action('wpcf7_before_send_mail', 'save_my_form_data_to_my_cpt');
// add_action('wpcf7_mail_sent', 'save_my_form_data_to_my_cpt', 1);
function save_my_form_data_to_my_cpt($contact_form){
		//Get the form ID
		//$contact_form = WPCF7_ContactForm::get_current();
		$contact_form_id = $contact_form->id;

    $submission = WPCF7_Submission::get_instance();
    if (!$submission){
				// you don't have to return the form, this is a hook not a filter
        //return $contact_form;
        return; // exit the hook
    }
		// get posted data
    $posted_data = $submission->get_posted_data();
		if ( class_exists( 'BugFu' ) ) {
			BugFu::log( $posted_data, true );
  	}
		// get file upload info from form
		// $uploadedFiles = $submission->uploaded_files();
		$uploadedFiles = $posted_data['field_image'];
		if ( class_exists( 'BugFu' ) ) {
			BugFu::log( $uploadedFiles, true );
  	}
		//
    // The form fields are now in an array,
    // access them with $posted_data['my-email']
    //
    // create new post array
    $new_post = array();
    //
    // META META FIELDS
    // post_type (i.e., your CPT)
    $new_post['post_type'] = 'thing';
    // post_status (draft, publish, pending)
    $new_post['post_status'] = 'draft';
    //
    // POST FIELDS
    // post_title
    if(isset($posted_data['field_title']) &&
            !empty($posted_data['field_title'])){
        $new_post['post_title'] = $posted_data['field_title'];
    } else {
        $new_post['post_title'] = '[Insert Title Here]';
    }
		//
    // post_content and post_excerpt
    $my_content = "";
    if(isset($posted_data['field_title'])){
      $my_content = $posted_data['field_title'];
    }
    if(isset($posted_data['field_info'])){
			if(!empty($my_content)) {
				$my_content = $my_content  . ". ";
			}
      $my_content = $my_content . $posted_data['field_info'];
    }
    $new_post['post_content'] = $my_content;
    $new_post['post_excerpt'] = wp_trim_words(wp_strip_all_tags($my_content), 20);
    //
    // post_author
    // Check to make sure UltimateMember is active,
    // and if not, assign to current logged in user
    // TODO: Check to make sure this works
    if( function_exists( 'um_user' ) ) {
			$current_user_id = um_user( "ID" );
		} else {
			$current_user_id = get_current_user_id()->ID;
		}
    if(isset($current_user_id)){
        $new_post['post_author'] = $current_user_id;
        // if ( class_exists( 'PC' ) ) PC::debug("Author ID:", print_r($current_user_id, True));
        // $current_user = um_fetch_user( $current_user_id );
        // if ( class_exists( 'PC' ) ) PC::debug("User:", print_r($current_user, True));
    }
    else {
        $new_post['post_author'] = get_user_by('login', 'unavoidabledisaster')->ID;
    }
		if ( class_exists( 'BugFu' ) ) {
			BugFu::log( $new_post, true );
  	}
    // POST CPT
    //When everything is prepared, insert the post into your Wordpress Database
    if($post_id = wp_insert_post($new_post)){
        // it worked so let's continue...
        //
        // TAGS
        wp_set_post_tags( $post_id, $posted_data['field_tags'], true );
				//
        // META FIELDS
        // build post content from all of the fields of the form,
        // or you can save them into some meta fields
        if(isset($posted_data['field_expires']) &&
                !empty($posted_data['field_expires'])){
            update_field('ad_expires', $posted_data['field_expires'], $post_id);
            // $new_post['meta_input']['ad_expires'] = $posted_data['field_expires'];
        }
        if(isset($posted_data['field_addl_tags']) &&
                !empty($posted_data['field_addl_tags'])){
            update_field('ad_addl_tags', $posted_data['field_addl_tags'], $post_id);
            // $new_post['meta_input']['ad_addl_tags'] = $posted_data['field_addl_tags'];
        }
				// every submitted ad is clickable by $default
        // if(isset($posted_data['field_clickable'])) {
        //     if ($posted_data['field_clickable'] == "yes"){
                update_field('ad_clickable', True, $post_id);
        //     } else {
        //         update_field('ad_clickable', False, $post_id);
        //     }
        // }
        if(isset($posted_data['field_info']) &&
                !empty($posted_data['field_info'])){
            update_field('ad_info', $posted_data['field_info'], $post_id);
            // $new_post['meta_input']['ad_info'] = $posted_data['field_info'];
        }
        if(isset($posted_data['field_call_to_action']) &&
                !empty($posted_data['field_call_to_action'])){
            update_field('ad_call_to_action', $posted_data['field_call_to_action'], $post_id);
            // $new_post['meta_input']['ad_call_to_action'] = $posted_data['field_call_to_action'];
        }
        if(isset($posted_data['field_phone_contact']) &&
                !empty($posted_data['field_phone_contact'])){
            update_field('ad_phone_contact', $posted_data['field_phone_contact'], $post_id);
            // $new_post['meta_input']['ad_phone_contact'] = $posted_data['field_phone_contact'];
        }
        if(isset($posted_data['field_email_contact']) &&
                !empty($posted_data['field_email_contact'])){
            update_field('ad_email_contact', $posted_data['field_email_contact'], $post_id);
            // $new_post['meta_input']['ad_email_contact'] = $posted_data['field_email_contact'];
        }
        if(isset($posted_data['field_website']) &&
                !empty($posted_data['field_website'])){
            update_field('ad_website', $posted_data['field_website'], $post_id);
            // $new_post['meta_input']['ad_website'] = $posted_data['field_website'];
        }
        if(isset($posted_data['field_contributor']) &&
                !empty($posted_data['field_contributor'])){
            update_field('ad_contributor', $posted_data['field_contributor'], $post_id);
            // $new_post['meta_input']['ad_contributor'] = $posted_data['field_contributor'];
        }
        if(isset($posted_data['field_notes']) &&
                !empty($posted_data['field_notes'])){
            update_field('ad_notes', $posted_data['field_notes'], $post_id);
            // $new_post['meta_input']['ad_notes'] = $posted_data['field_notes'];
        }
        if(isset($posted_data['field_location']) &&
                !empty($posted_data['field_location'])){
            update_field('ad_location', $posted_data['field_location'], $post_id);
            // $new_post['meta_input']['ad_location'] = $posted_data['field_location'];
        }

        //
        // IMAGE
        // Retrieving and inserting uploaded image as featured image
        // CF7 uploads the image and puts it in a temporary directory,
        //      deleting it after the mail is sent
        // Before it deletes it, we will move into our media library,
        //      and attach it to our post
        // Ref: https://stackoverflow.com/questions/66933665
        //
        // if we have an uploaded image...
        //if( isset($posted_data['field_image']) ){
				if (!empty($uploadedFiles)) {
            // move image from temp folder to upload folder
            $file = file_get_contents($uploadedFiles[0]);
        		$image_name = basename($uploadedFiles[0]);
            $imageUpload = wp_upload_bits(basename($uploadedFiles[0]), null, $file);
            // PC::debug("imageUpload:", print_r($imageUpload, True));
            //
            require_once(ABSPATH . 'wp-admin/includes/admin.php');
            // construct array to register this image
            $filename = $imageUpload['file'];
            $attachment = array(
                'post_mime_type' => $imageUpload['type'],
                'post_parent' => $post_id,
                'post_title' => $posted_data['field_title'] . ' - ' .
                                $posted_data['field_contributor'],
                'post_content' => $posted_data['field_info'],
                'post_status' => 'inherit'
            );
            // attach image to this post
            $attachment_id = wp_insert_attachment( $attachment, $filename, $post_id );
            // PC::debug("attachment_id:", print_r($attachment_id, True));
            // if we succeeded...
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
                // PC::debug("attachment_data:", print_r($attachment_data, True));
                wp_update_attachment_metadata( $attachment_id,  $attachment_data );
                set_post_thumbnail( $post_id, $attachment_id );
                // add image id (attchment id) to ad_image field
                update_field( 'ad_image', $attachment_id, $post_id );
            }
        }
    } else {
       //The post was not inserted correctly, do something (or don't ;) )
    }
    return $contact_form;
}

// populate taxonomy checkboxes with existing tags
// Ref: https://wordpress.stackexchange.com/questions/115947/contact-form-7-populate-select-list-with-taxonomy
//
/** Dynamic List for Contact Form 7 **/
/** Usage: [select name term:taxonomy_name] **/
add_filter( 'wpcf7_form_tag', 'dynamic_select_list', 10, 2);
function dynamic_select_list($tag, $unused){
    $options = (array)$tag['options'];

    foreach ($options as $option) {
        if (preg_match('%^term:([-0-9a-zA-Z_]+)$%', $option, $matches)) {
            $term = $matches[1];
            // PC::debug("term:", print_r($term, True));
        }
    }
    //check if post_type is set
    if(!isset($term)) {
        return $tag;
    }
    $taxonomy = get_terms(array('taxonomy' => $term, 'hide_empty' => false));
    if (!$taxonomy) {
        return $tag;
    }
    foreach ($taxonomy as $cat) {
        $tag['raw_values'][] = $cat->name;
        $tag['values'][] = $cat->name;
        $tag['labels'][] = $cat->name;
    }
    $tag['raw_values'][] = 'Other';
    $tag['values'][] = 'Other';
    $tag['labels'][] = 'Other - Please Specify Below';

    return $tag;
}

// // add taxonomies to media
// //
// function wptp_add_categories_to_attachments() {
//     register_taxonomy_for_object_type( 'category', 'attachment' );
// }
// add_action( 'init' , 'wptp_add_categories_to_attachments' );

// // apply tags to attachments
// function wptp_add_tags_to_attachments() {
//     register_taxonomy_for_object_type( 'post_tag', 'attachment' );
// }
// add_action( 'init' , 'wptp_add_tags_to_attachments' );

// change custom menu item to add user info
//
// This shortcode: [mycred_my_balance]Please login to view your balance[/mycred_my_balance]
//
/**
 * Filters all menu item URLs for a #placeholder#.
 *
 * @param WP_Post[] $menu_items All of the nave menu items, sorted for display.
 *
 * @return WP_Post[] The menu items with any placeholders properly filled in.
 */
add_filter( 'wp_nav_menu_objects', 'dynamic_menu_item_user_data' );
function dynamic_menu_item_user_data( $menu_items ) {
    // here's what we are replacing
    $menu_item_text = '#user-data#';
    // here's what we are replacing this item with
    // <div class="user-name">[user_display_name]</div> [mycred_my_balance]
    //
    // here's parts of what we will be replacing
    $user_name = do_shortcode( "[user_display_name]" );
    $cred_balance = do_shortcode( "[mycred_my_balance]" );
    //
    // and here's the html we will replace with
    $menu_item_html = '<div class="user-data">
                                <div class="user-name">' . $user_name . '</div>
                                <div class="user-coin"></div>
                                <div class="mycred-my-balance-wrapper">
                                    <div>' . $cred_balance . '</div>
                                </div>
                            </div>';
    $placeholders = array(
        $menu_item_text => array(
            'shortcode' => $menu_item_html,
            'atts' => array(), // Shortcode attributes.
            'content' => '', // Content for the shortcode.
        ),
    );
    foreach ( $menu_items as $key => $menu_item ) {
        // if ( class_exists( 'PC' ) ) PC::debug("Tags:", print_r($menu_item, True));
        if ( $menu_item->title == $menu_item_text) {
            // if ( class_exists( 'PC' ) ) PC::debug("Found:", print_r($menu_item, True));
            $menu_item->title = $menu_item_html;
        }
    }
    return $menu_items;
}

// Update CSS within in Admin
add_action('admin_enqueue_scripts', 'admin_style');
function admin_style() {
  wp_enqueue_style('admin-styles', get_template_directory_uri().'/css/admin.css');
}

// Add function to elementor editor
//
// function elementor_enqueue_scripts() {
//     $js_file = '/js/elementor.js';
//     wp_enqueue_script( 'elementor-scripts', get_theme_file_uri($js_file),
//         array('jquery'), filemtime(get_theme_file_path($js_file)) );
// }
// add_action('elementor/editor/before_enqueue_scripts', elementor_enqueue_scripts);

// Add thing CPT post-type to member profile
//
add_filter( 'um_profile_query_make_posts', 'custom_um_profile_query_make_posts', 12, 1 );
function custom_um_profile_query_make_posts( $args = array() ) {
    // Change the post type to our liking.
    $args['post_type'] = 'thing';
    return $args;
}

// Get InnerHTML of element
function innerHTML($node){
  $doc = new DOMDocument();
  foreach ($node->childNodes as $child)
    $doc->appendChild($doc->importNode($child, true));
  return $doc->saveHTML();
}

function convert_links_to_mycred($html) {
    // check to make sure mycred is active
    if (! is_plugin_active("mycred/mycred.php")) {
        return $html;
    }
    //Instantiate the DOMDocument class.
    $htmlDom = new DOMDocument;
    //Parse the HTML of the page using DOMDocument::loadHTML
    // $htmlDom->loadHTML($html);
    // load without html wrapper
		@$htmlDom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    //Extract the links from the HTML.
    $links = $htmlDom->getElementsByTagName('a');
    //Array that will contain our extracted links.
    $extracted_links = array();
    //Loop through the DOMNodeList.
    //We can do this because the DOMNodeList object is traversable.
    foreach($links as $link){
			// clear the skip_flag
			$skip_flag = False;
      //Get the link text.
      $linkText = innerHTML( $link );
      // $linkText = $link->nodeValue;
      //Get the link in the href attribute.
      $linkHref = $link->getAttribute('href');
			echo "<!--linkText:" . $linkText . " href:" . $linkHref . "-->";
      //If the link is empty, skip it and don't
      //add it to our $extractedLinks array
      if(strlen(trim($linkHref)) == 0){
          continue;
      }
      //Skip if it is a hashtag / anchor link.
      if($linkHref[0] == '#'){
          continue;
      }
			// Here are the attributes we will handle:
			$mycred_attrs = array('href', 'id', 'class', 'rel', 'title', 'target', 'style');
			// If this link is more cpomplicated than that, skip it.
			if ($link->hasAttributes()) {
			  foreach ($link->attributes as $attr) {
			    $name = $attr->nodeName;
			    $value = $attr->nodeValue;
			    // "Attribute '$name' :: '$value'<br />";
					if (! in_array($name, $mycred_attrs)) {
						echo "<!--" . $name . " too complicated - skipping-->";
						$skip_flag = True;
						break;
					}
			  }
			}
			// if this too complicated, skip this link
			if ($skip_flag == True) {
				continue;
			}
			// Get the other attributes of the link
			// compile string we'll use to construct the shortcode
			$attr_str = '';
			foreach ($mycred_attrs as $mattr) {
				$attr = $link->attributes->$mattr;
				if ($link->hasAttribute($mattr)) {
					$new_attr_str = $attr->nodeName . '=' . $attr->nodeValue . ' ';
					$attr_str += $new_attr_str;
				}
			}
			echo "<!--attr_str" . $attr_str . "-->";
			// get mycred link
			$mycred_link = do_shortcode( '[mycred_link href="' .
					$linkHref .
					'" target="_blank"]' .
					$linkText .
					'[/mycred_link]' );

			// ref: https://stackoverflow.com/questions/2233683
			// turn mycred link into a dom object`
			$domDocumentReplace = new \DOMDocument;
			$domDocumentReplace->loadHTML(mb_convert_encoding($mycred_link, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
			// import the replacement node into the document
			$htmlReplaceNode = $htmlDom->importNode($domDocumentReplace->documentElement, true);
			// replace link node with mycred link  node
			$link->parentNode->replaceChild($htmlReplaceNode, $link);
    }
		$html = innerHTML( $htmlDom );
    return $html;
}

add_filter( 'comments_open', 'my_comments_open', 10, 2 );

/* Automatically check “Allow comments” for custom post type */
function my_comments_open( $open, $post_id ) {
  $post = get_post( $post_id );
  if ( 'things' == $post->post_type )
      $open = true;
  return $open;
}
