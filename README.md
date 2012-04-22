Image Filters plugin for WordPress
==================================

How to use this plugin it:

```php
// Add a new image size which we'll make black and white
add_image_size('medium-bw', 100, 100);

// Add this size to the filtered image sizes
add_filter('filtered_image_sizes', 'make_medium_image_bw');
function make_medium_image_bw($sizes) {
	$sizes[] = 'medium-bw';
	return $sizes;
}```

