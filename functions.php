<?php
include 'demodata.php';
/*
*
*Part 2 - WP preparation
*	-Create chiled Theme for “twenty-twenty” 
*	-enqueue parent rtl styles (Hebrew wordpress installation)
*/


function ns_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style-rtl.css' );
}
add_action( 'wp_enqueue_scripts', 'ns_enqueue_styles' );

/*
*Part 3 - Users
*	-Create editor user
*/
function add_wptest_user() {
    $username = 'wp-test';
    $email = 'wptest@elementor.com';
    $password = '123456789';
	
	//check if username if exist
    $user_id = username_exists( $username );
	
	//if no username found or email create user
    if ( !$user_id && email_exists($email) == false ) {
        $user_id = wp_create_user( $username, $password, $email );
        if( !is_wp_error($user_id) ) {
            $user = get_user_by( 'id', $user_id );
            $user->set_role( 'editor' );
        }
    }
}
add_action('after_switch_theme', 'add_wptest_user');

//Disable wp admin bar for this user 
function disable_editor_admin_bar() {
	global $current_user;
	wp_get_current_user();
	if ($current_user->user_login == 'wp-test') {
		show_admin_bar(false);
	}
}
add_action('after_setup_theme', 'disable_editor_admin_bar');


/*
*Part 4 - Product Post Types
*/

//Add 6 products on theme switch from demo data.php
add_action('after_switch_theme', 'init_sixproduct');

//STAT --------------add product CTP
add_action( 'init', 'products_posttype' );
function products_posttype() {
    // Products Options
	$args = array(
		'labels' => array(
			'name' => __( 'Products' ),
			'singular_name' => __( 'Product' )
		),
		'show_ui' 		=> true,
		'menu_icon' 	=> 'dashicons-store',
		'public' 		=> true,
		'has_archive' 	=> true,
		'rewrite' 		=> array('slug' => 'Products'),
		'show_in_rest' 	=> true,
		'supports'      => array( 'thumbnail','title', 'editor', 'revisions' )

	);	
    register_post_type( 'products', $args );
	register_taxonomy("Categories", array("products"), array("hierarchical" => true, "label" => "Categories", "singular_label" => "Category", "rewrite" => true));
	


}
//END --------------add product CTP

//STAT --------------add product meta box
add_action("admin_init", "init_meta_box");
function init_meta_box(){
	  add_meta_box('product_meta_box', // meta box ID
        'procut data', // meta box title
        'product_meta_data', // callback function that prints the meta box HTML
        'products', // post type where to add it
        'normal', // priority
        'default' ); // position
}
//END --------------add product meta box

//STAT --------------add product meta data
function product_meta_data() {
  global $post;
  $custom = get_post_custom($post->ID);
  $price = $custom["price"][0];
  $sale_price = $custom["sale_price"][0];
  $is_on_sale = $custom["is_on_sale"][0];
  $youtube_video = $custom["youtube_video"][0];

   $meta_key = 'product_galery';
   echo image_uploader( $meta_key, get_post_meta($post->ID, $meta_key, true) );
  ?>
  
  <!--START show metadata on product admin page-->
  <p><label>Price:</label>
  <input type="number" name="price" value="<?php echo $price; ?>"></input></p>
  <p><label>Sale price:</label>
  <input type="number" name="sale_price" value="<?php echo $sale_price; ?>"></input></p>
  <p><label>Is on sale?:</label>
  
  <input type="checkbox" name="is_on_sale" <?php  if($is_on_sale) echo 'checked'; ?>></input></p>
  <p><label>YouTube video:</label>
  <input type="url" placeholder="https://www.youtube.com/embed/video-id" size="60" name="youtube_video" value="<?php echo $youtube_video; ?>"></input></p>
  
  <!--END show metadata-->
  <?php
}
//END --------------add product meta data

//STAT --------------upload images
function image_uploader( $name, $value = '' ) {
     
$html = '<div><ul class="gallery_mtb">';
	/* array with image IDs for hidden field */
	$hidden = array();

	 if (!is_array($value)) {$value = explode(',',$value);}
	if( $images = get_posts( array(
		'post_type' => 'attachment',
		'orderby' => 'post__in', /* we have to save the order */
		'order' => 'ASC',
		'post__in' => $value, /* $value is the image IDs comma separated */
		'numberposts' => -1,
		'post_mime_type' => 'image'
	) ) ) {

		foreach( $images as $image ) {
			$hidden[] = $image->ID;
			$image_src = wp_get_attachment_image_src( $image->ID, array( 80, 80 ) );
			$html .= '<li data-id="' . $image->ID .  '"><img src="' . $image_src[0] . '"><a href="#" class="gallery_remove">&times;</a></li>';
		}

	}

	$html .= '</ul><div style="clear:both"></div></div>';
	$html .= '<input type="hidden" name="'.$name.'" value="' . join(',',$hidden) . '" /><a href="#" class="button upload_gallery_button">Add Images</a>';

	return $html;  
}
//END --------------upload images

//STAT --------------save product metadata
add_action('save_post', 'save_product_details', 100, 3);
function save_product_details($post_id, $post, $update){
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;
	global $post;

	update_post_meta($post->ID, "price", $_POST["price"]);
	update_post_meta($post->ID, "sale_price", $_POST["sale_price"]);
	update_post_meta($post->ID, "youtube_video", $_POST["youtube_video"]);
	update_post_meta( $post_id, "product_galery", $_POST["product_galery"] );

	If( isset($_POST['is_on_sale']) ){
		update_post_meta($post->ID, "is_on_sale", true );
	}else{
		update_post_meta($post->ID, "is_on_sale", false );
	}
 
	
 
	return $post_id;
}
//END --------------save product metadata

//STAT --------------Add product grit to home page
add_action( 'pre_get_posts', 'add_product_to_home' );

function add_product_to_home( $query ) {
	
    if( $query->is_main_query() && $query->is_home() ) {
        // args
		$args = array( 
			'post_type' 		=> 'products'
		);
		// query
		$the_query = new WP_Query( $args );
		?>

		<!-- START get products loop if have products-->
		<?php if( $the_query->have_posts() ): ?>

			<div class="display-posts-listing grid">
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); $id = get_the_ID();?>
				<div class="listing-item">
					<a class="image" href="<?php echo get_the_post_thumbnail_url();  ?>">
					<img  src="<?php echo get_the_post_thumbnail_url();  ?>" class="attachment-medium size-medium wp-post-image" alt="" ></a> 
					<a class="title" href="<?php echo the_permalink();  ?>"><?php the_title(); ?></a>
				</div>	
			<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<!-- END get products loop-->
		
		<?php wp_reset_query();	 
			
    }
}
//END --------------Add product grit to home page


//STAT  --------------add metadata to single product
add_filter( 'the_content', 'display_metadata_after_product_description',5, 1 );

function display_metadata_after_product_description( $content ){
	
    // Only for CTP single product  pages
    if( ! is_singular('products') ) 
		return $content;
	
	global $post;
	$videourl = get_post_meta($post->ID,'youtube_video',true);
	$price = get_post_meta($post->ID,'price',true);
	$sale_price = get_post_meta($post->ID,'sale_price',true);
	$isonsale = get_post_meta($post->ID,'is_on_sale',true);
	$images = get_post_meta($post->ID,'product_galery',false);
	
	$youtubeframe = '<iframe id="youtube" width="100" height="100" src="'.$videourl.'"></iframe>';
    
	if($isonsale)
	{
		$content =  $content. '<p>sale price: ' .$sale_price . '$</p>';
		$line = 'style="text-decoration: line-through;"';
	}
	
	$content 	= $content. '<p '.$line.'>Full price: ' .$price . '$</p>';	
	$content 	= $content .  $youtubeframe ;
	$images 	= explode(",", $images[0]);
	$content 	= $content . '<p class="product-title">Product Galery</>';
	$content 	= $content . '<div class="galery-listing">';
	
	foreach($images as $image)
	{
		$image_src = wp_get_attachment_image_src( $image, array( 80, 80 ) );
		$content = $content .'<div class="galery-item">';
		$content = $content .'<a class="image" href="' .$image_src[0]  .'">';
		$content = $content .'<img src="' .$image_src[0] .'" class="attachment-medium size-medium wp-post-image" alt=""></a></div>';
	}
	$content = $content .'</div>';

	
    return $content;
}
//END  --------------add metadata to single product


//START  --------------add related product
add_filter( 'the_content', 'display_rel_products', 10, 1 );

function display_rel_products( $content ){
	
    // Only for CTP single product  pages
    if( ! is_singular('products') ) 
		return $content;
	
	global $post;
	
	// going to hold our tax_query params
	$tax_query  = array();
	$custom_terms = wp_get_post_terms( $post->ID, 'Categories' );
	
	
	// add the relation parameter
	if( count( $custom_terms) > 1  )
		$tax_query['relation'] = 'OR' ;

	// loop through Categories and build a tax query
	foreach( $custom_terms as $category ) {

		$tax_query[] = array(
			'taxonomy' => 'Categories',
			'field' => 'slug',
			'terms' => $category->slug,
		);
		

	}
	// put all the WP_Query args together
	$args = array( 'post_type' => 'products',
					'posts_per_page' => 5,
					'tax_query' => $tax_query );

	// finally run the query
	if( count( $custom_terms) > 0  )
	{
	$loop = new WP_Query($args);

	if( $loop->have_posts() ) {
		$content = $content .'<p class="product-title">related products</p>';
		 while ( $loop->have_posts() ) {
			 $loop->the_post(); $id = get_the_ID();
			 
				$content = $content .'<div class="rel-product"><a class="rel-prod-title" href="'.get_post_permalink() .'">'.get_the_title().'</a><a href="'.get_the_post_thumbnail_url().'"><img  src="'.get_the_post_thumbnail_url().'" class="galery-item"  alt="" ></a></div>';	
		 }

	}
	wp_reset_query();
	}
    return $content;
}
//END  --------------add related product

//STAT  --------------add admin scripts
function child_theme_admin_scripts() {

	if ( ! did_action( 'wp_enqueue_media' ) )
		wp_enqueue_media();
    wp_enqueue_script( 'product-gallery-js', get_stylesheet_directory_uri() . '/product-gallery.js', array('jquery'), null, true );
    	

}
add_action( 'admin_enqueue_scripts','child_theme_admin_scripts' );
//END  --------------add admin scripts
