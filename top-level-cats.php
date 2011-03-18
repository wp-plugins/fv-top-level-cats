<?php
/*
Plugin Name: FV Top Level Categories
Plugin URI: http://foliovision.com/seo-tools/wordpress/plugins/fv-top-level-categories
Description: Removes the prefix from the URL for a category. For instance, if your old category link was <code>/category/catname</code> it will now be <code>/catname</code>
Version: 1.1.2
Author: Foliovision
Author URI: http://foliovision.com/  
*/

/*
Changelog
1.1.2   fix for /category/child-category redirecting to /child-category page
1.1.1   fix for deeper nested categories
1.1   FV Fix for WP 3.1
1.0.1 Original version
*/

// In case we're running standalone, for some odd reason
if (function_exists('add_action'))
{
	register_activation_hook(__FILE__, 'top_level_cats_activate');
	register_deactivation_hook(__FILE__, 'top_level_cats_deactivate');

	// Setup filters
	add_filter('category_rewrite_rules', 'top_level_cats_category_rewrite_rules'); /// ok
	add_filter('generate_rewrite_rules', 'top_level_cats_generate_rewrite_rules');
	add_filter('category_link', 'top_level_cats_category_link', 10, 2);
	
	///  
	add_filter( 'page_rewrite_rules', 'fv_page_rewrite_rules' );
	add_filter( 'request', 'fv_request_page_instead_category' );
	
	global $clean_category_rewrites, $clean_rewrites;
	$clean_category_rewrites = array();
}

function fv_page_rewrite_rules( $rules ) {
  unset( $rules["(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$"] );
  unset( $rules["(.+?)/(feed|rdf|rss|rss2|atom)/?$"] );
  unset( $rules["(.+?)/page/?([0-9]{1,})/?$"] );
  unset( $rules["(.+?)(/[0-9]+)?/?$"] );
  return $rules;
}

function top_level_cats_activate()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function top_level_cats_deactivate()
{
	// Remove the filters so we don't regenerate the wrong rules when we flush
	remove_filter('category_rewrite_rules', 'top_level_cats_category_rewrite_rules');
	remove_filter('generate_rewrite_rules', 'top_level_cats_generate_rewrite_rules');
	remove_filter('category_link', 'top_level_cats_category_link');

	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function top_level_cats_generate_rewrite_rules($wp_rewrite)
{
	global $clean_category_rewrites;
	$wp_rewrite->rules = $wp_rewrite->rules + $clean_category_rewrites;
}

function top_level_cats_category_rewrite_rules($category_rewrite)
{
	global $clean_category_rewrites;
  global $wp_rewrite;

  // Make sure to use verbose rules, otherwise we'll clobber our
  // category permalinks with page permalinks
  //$wp_rewrite->use_verbose_page_rules = true; /// disabling this will make sure posts work, it was here already

	while (list($k, $v) = each($category_rewrite)) {
		// Strip off the category prefix
		$new_k = top_level_cats_remove_cat_base($k);
		$clean_category_rewrites[$new_k] = $v;
	}

  foreach( $category_rewrite AS $key => $item ) {
    if( stripos( $item, 'index.php?pagename' ) !== FALSE ) {
      unset( $category_rewrite[$key] );
    }
  }

	return $category_rewrite;
}

function top_level_cats_category_link($cat_link, $cat_id)
{
	return top_level_cats_remove_cat_base($cat_link);
}

function top_level_cats_remove_cat_base($link)
{
	$category_base = get_option('category_base');
	
	// WP uses "category/" as the default
	if ($category_base == '') 
		$category_base = 'category';

	// Remove initial slash, if there is one (we remove the trailing slash in the regex replacement and don't want to end up short a slash)
	if (substr($category_base, 0, 1) == '/')
		$category_base = substr($category_base, 1);

	$category_base .= '/';

	return preg_replace('|' . $category_base . '|', '', $link, 1);
}

function fv_request_page_instead_category($query_string)
{   
    //echo '<!-- before '.var_export( $query_string, true ).'-->';
    
    //  enable this and disable top_level_cats_category_rewrite_rules, top_level_cats_generate_rewrite_rules to prefer categories over pages
    /*if( isset( $query_string['pagename'] ) ) {
      $cats = get_categories();
      foreach( $cats AS $cat_item ) {
        if( $cat_item->slug == $query_string['pagename'] ) {
          $query_string['category_name'] = $query_string['pagename'];
          unset( $query_string['pagename'] );
        }
      }
    } */
    if( isset( $query_string['category_name'] ) ) {
      global $wpdb;
      
      $parts = explode( '/', $query_string['category_name'] );
      $slug = $wpdb->escape( trim( substr( $query_string['category_name'], strripos( $query_string['category_name'], '/' ) ), '/' ) );
      $page_exists = $wpdb->get_row( "SELECT ID, post_parent FROM $wpdb->posts WHERE post_name='{$slug}' AND post_type = 'page' AND post_status = 'publish' " );
      
      if( $page_exists && $page_exists->post_parent && stripos( $query_string['category_name'], '/' ) !== FALSE ) {
        $query_string['page_id'] = $page_exists->ID;
        unset( $query_string['category_name'] );
      }
      if( $page_exists && !$page_exists->post_parent && count( $parts ) == 1 ) {  //  fix for /category/child-category redirecting to /child-category page
        $query_string['pagename'] = $query_string['category_name'];
        unset( $query_string['category_name'] );
      }
    }
    //echo '<!-- after '.var_export( $query_string, true ).'-->';
  
    return $query_string; //  end
}

/*function fv_redirect_canonical( $url ) {
  if( $url == 'http://localhost/wordpress-3.1/uncategorized/child' ) return false;
  return $url;
}
add_filter( 'redirect_canonical', 'fv_redirect_canonical' );*/

?>