<?php
 /**
/*
 Plugin Name: Custom Plugin
 Plugin URI: https://github.com/Abhishek1987-nov
 Author: Abhishek Barat
 */

/////show Purchased Products in Order Section ///////////////////////////////
 add_filter('manage_edit-shop_order_columns', 'woo_order_items_column' );
function woo_order_items_column( $order_columns ) {
    $order_columns['order_products'] = "Purchased products";
    return $order_columns;
}
 
add_action( 'manage_shop_order_posts_custom_column' , 'order_items_column_cnt' );
function order_items_column_cnt( $colname ) {
	global $the_order;
 
 	if( $colname == 'order_products' ) {
 
		
		$order_items = $the_order->get_items();
 
		if ( !is_wp_error( $order_items ) ) {
			foreach( $order_items as $order_item ) {
				 $product = $order_item->get_product();
				 $imageproduct=$product->get_image( array( 50, 50 ) );
 
 				echo $order_item['quantity'] .' × <a href="' . admin_url('post.php?post=' . $order_item['product_id'] . '&action=edit' ) . '">'. $order_item['name'] .''. $imageproduct .'</a><br />';
			
 
			}
		}
 
	}
 
}

/////show order image in Order Section ///////////////////////////////
add_filter( 'manage_edit-shop_order_columns', 'admin_orders_list_add_column', 10, 1 );
function admin_orders_list_add_column( $columns ){
    $columns['custom_column'] = __( 'Order Image', 'woocommerce' );

    return $columns;
}
add_action( 'manage_shop_order_posts_custom_column' , 'admin_orders_list_column_content', 10, 2 );
function admin_orders_list_column_content( $column, $post_id ){
    global $the_order;

    if( 'custom_column' === $column ){
        $count = 0;

        // Loop through order items
        foreach( $the_order->get_items() as $item ) {
            $product = $item->get_product(); // The WC_Product Object
            $style   = $count > 0 ? ' style="padding-left:6px;"' : '';

            // Display product thumbnail
            printf( '<span%s>%s</span>', $style, $product->get_image( array( 50, 50 ) ) );

            $count++;
        }
    }
}



//////////////remove all updates//////////////////////////////////
function remove_core_updates(){
        global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
    }
    add_filter('pre_site_transient_update_core','remove_core_updates');
    add_filter('pre_site_transient_update_plugins','remove_core_updates');
    add_filter('pre_site_transient_update_themes','remove_core_updates');

//////custom post type/////////////
function custom_post_type_events() {
  $labels = array(
    'name'                => _x( 'events ', 'Post Type General Name', 'menu' ),
    'singular_name'       => _x( 'events ', 'Post Type Singular Name', 'menu' ),
    'menu_name'           => __( 'events ', 'menu' ),
    'parent_item_colon'   => __( 'Parent events ', 'menu' ),
    'all_items'           => __( 'All events ', 'menu' ),
    'view_item'           => __( 'View events ', 'menu' ),
    'add_new_item'        => __( 'Add New events', 'menu' ),
    'add_new'             => __( 'Add New', 'menu' ),
    'edit_item'           => __( 'Edit events ', 'menu' ),
    'update_item'         => __( 'Update events ', 'menu' ),
    'search_items'        => __( 'Search events ', 'menu' ),
    'not_found'           => __( 'Not Found', 'menu' ),
    'not_found_in_trash'  => __( 'Not found in Trash', 'menu' ),
  );
  $args = array(
    'label'               => __( 'events ', 'menu' ),
    'description'         => __( 'events news and reviews', 'menu' ),
    'labels'              => $labels,
    'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', ),
    'taxonomies'          => array( 'genres' ), 
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'menu_icon' => get_template_directory_uri() . '/images/tagged.jpg',
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'taxonomies'          => array( 'category' ),
  );
  register_post_type( 'events', $args );
}
add_action( 'init', 'custom_post_type_events', 0);

////////////////////add shorcode///////////////////////////////////////////////////////
if ( ! function_exists('events_shortcode') ) 
{

    function events_shortcode() 
    {
     $tagged_array=array(
            
            'posts_per_page'   => 12,
            'post_type'        => 'events',
            'post_status'      => 'publish'
         );
         $tagged_listing = new WP_Query( $tagged_array );
          global $post;
         
         if ( $tagged_listing->have_posts() ) :
         while ( $tagged_listing->have_posts() ) : $tagged_listing->the_post();
         $tagged_image = wp_get_attachment_image_src(get_post_thumbnail_id(),'large');
       
       
         ?>


                   <img src="<?php echo $tagged_image[0]; ?>" class="img-responsive"> 
              <?php  the_post_thumbnail('thumbnail');?>
           <div>
<?php echo get_the_post_thumbnail_url( get_the_ID(), 'medium' );?>
</div>
            <?php the_title(); ?></span> 
        
          


              <?php 
             endwhile; 
              endif; 
          wp_reset_postdata();
          ?>
  
    <?php
			}

add_shortcode( 'eventss', 'events_shortcode' );   
}

////////////////////widget in blog sidebar/////////////////////////////////////////////////////////////
class wpb_widget extends WP_Widget {
  
function __construct() 
{
parent::__construct(
  
'wpb_widget', __('WPBeginner Widget', 'wpb_widget_domain'), 
array( 'description' => __( 'Sample widget based on WPBeginner Tutorial', 'wpb_widget_domain' ), ) 
);
}
  
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
  

echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
  

echo __( 'Hello, World!', 'wpb_widget_domain' );
echo $args['after_widget'];
}
          
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'wpb_widget_domain' );
}

?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
      

public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
 

} 
function wpb_load_widget() 
{
    register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );
////////////////////////////Show table Values Operation///////////////////////////////////////////////////

?>