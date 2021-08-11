<?php
include_once( ABSPATH . 'wp-admin/includes/image.php' );

//STAT --------------Add 6 products on theme switch
function init_sixproduct()
{
	$images = array();
	$attids = array();
	array_push($images,"https://cdn.ilovefreesoftware.com/wp-content/uploads/2019/06/360-degree-photo-viewer.png");
	array_push($images,"https://st.depositphotos.com/1428083/2946/i/600/depositphotos_29460297-stock-photo-bird-cage.jpg");
	array_push($images,"https://www.gettyimages.com/gi-resources/images/500px/983794168.jpg");
	array_push($images,"https://cdn.pixabay.com/photo/2015/04/19/08/32/marguerite-729510__340.jpg");
	array_push($images,"https://images.freeimages.com/images/small-previews/25a/pink-heart-of-stone-1316358.jpg");
	array_push($images,"https://helpx.adobe.com/content/dam/help/en/stock/how-to/visual-reverse-image-search/jcr_content/main-pars/image/visual-reverse-image-search-v2_intro.jpg");
	array_push($images,"https://static.toiimg.com/photo/imgsize-74502,msid-82791170/82791170.jpg");
	foreach( $images as $image ) {
		$attid = upload_image_from_url($image );
		if($attid != false) array_push($attids,$attid);
	}

	
	for($i = 0; $i < 6; $i++){
		products_creator('product'.$i,
		'products',
		'Solitary bone cyst unspecified tibia and fibula',
		'1',
		'publish',
		'50',
		'45',
		'https://www.youtube.com/embed/JdHCWQM0bD0',
		implode(",",$attids),
		$attids[$i],
		'cat1',
		'cat2'
		);
	}
}
//END --------------Add 6 products on theme switch

//STAT --------------create product function
function products_creator(
	$title      		= 'AUTO POST',
	$type     			= 'products',
	$content   			= 'DUMMY CONTENT',
	$author_id 			= '1',
	$status    			= 'publish',
	$price				= NULL,
	$sale_price			= NULL,
	$youtube_video		= NULL,
	$product_galery		= NULL,
	$thumbnail_id		= NULL,
	$cat1				= NULL,
	$cat2				= NULL
) {

	
	$found_post_title = get_page_by_title( $title, OBJECT, $type );
	$found_post_id = $found_post_title->ID;

	
	if( FALSE === get_post_status( $found_post_id ) ){
		$post_data = array(
			'post_title'    => wp_strip_all_tags( $title ),
			'post_content'  => $content,
			'post_status'   => $status ,
			'post_type'     => $type ,
			'post_author'   => $author_id,
			'page_template' => 'default',
		);
		$post_id = wp_insert_post( $post_data);
		
		//add 2 categories to product
		wp_set_object_terms( $post_id, array( $cat1,$cat2 ), 'Categories' , false);

		//add product meta data
		update_post_meta($post_id, "price", $price);
		update_post_meta($post_id, "sale_price", $sale_price);
		update_post_meta($post_id, "youtube_video", $youtube_video);
		update_post_meta($post_id, "product_galery", $product_galery );
		update_post_meta($post_id, '_thumbnail_id', $thumbnail_id );
	}
	else
	{
		if($product_galery !="")
		{
			update_post_meta($found_post_id, "product_galery", $product_galery );
			update_post_meta($found_post_id,  '_thumbnail_id', $thumbnail_id );
		}
	}
	
}
//END --------------create product function

//STAT --------------upload image from url
function upload_image_from_url($imageurl)
{	

    $pathinfo = pathinfo($imageurl);
	$imagetype = end(explode('/', getimagesize($imageurl)['mime']));
	//$uniq_name = date('dmY').''.(int) microtime(true); 
	$filename = $pathinfo['filename'].'.'.$imagetype;
	$found_post_title = get_page_by_title($filename, OBJECT, 'attachment' );
	$found_post_id = $found_post_title->ID;
	if( FALSE === get_post_status( $found_post_id ) ){
		

		$uploaddir = wp_upload_dir();
		$uploadfile = $uploaddir['path'] . '/' . $filename;
		$contents= file_get_contents($imageurl);
		$savefile = fopen($uploadfile, 'w');
		fwrite($savefile, $contents);
		fclose($savefile);

		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => $filename,
			'post_content' => '',
			'post_status' => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $uploadfile );
		$imagenew = get_post( $attach_id );
		$fullsizepath = get_attached_file( $imagenew->ID );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
		wp_update_attachment_metadata( $attach_id, $attach_data ); 

		return $attach_id;
	}
	return false;
}
//END --------------upload image from url