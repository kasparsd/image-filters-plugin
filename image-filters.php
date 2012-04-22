<?php

/*
 Plugin Name: Image Filters
 Plugin URI: 
 Description: Add Image filters to specific image sizes
 Version: 0.1
 Author: Kaspars Dambis
 Author URI: http://konstruktors.com
 Text Domain: image-filters
 */


/*
How to use this plugin it:

// Add a new image size which we'll make black and white
add_image_size('medium-bw', 100, 100);

// Add this size to the filtered image sizes
add_filter('filtered_image_sizes', 'make_medium_image_bw');
function make_medium_image_bw($sizes) {
	$sizes[] = 'medium-bw';
	return $sizes;
}
*/


add_filter('wp_generate_attachment_metadata', 'add_bw_images_communicate', 10, 2);

function add_bw_images_communicate($meta, $att_id) {
	$filtered_image_sizes = apply_filters('filtered_image_sizes', array());

	if (empty($filtered_image_sizes))
		return $meta;

	$wp_upload_dir = wp_upload_dir();

	foreach ($filtered_image_sizes as $i => $image_size) {
		if (empty($meta['sizes'][$image_size]))
			continue;

		$image_path = $wp_upload_dir['basedir'] . '/' .  dirname($meta['file']) . '/' . $meta['sizes'][$image_size]['file'];
		$filtered_image_path = str_replace($meta['sizes'][$image_size]['file'], 'bw-' . $meta['sizes'][$image_size]['file'], $image_path);

		if (!copy($image_path, $filtered_image_path))
			continue;

		$meta['sizes'][$image_size]['file'] = 'bw-' . $meta['sizes'][$image_size]['file'];
		
		// Load the image
		$image = wp_load_image($filtered_image_path);
		// Get it's size
		list($orig_w, $orig_h, $orig_type) = @getimagesize($filtered_image_path);
		// Apply the filter
		imagefilter($image, IMG_FILTER_GRAYSCALE);

		switch ($orig_type) {
			case IMAGETYPE_GIF:
				imagegif($image, $filtered_image_path);
				break;
			case IMAGETYPE_PNG:
				imagepng($image, $filtered_image_path);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($image, $filtered_image_path);
				break;
		}		
	}

	return $meta;
}

