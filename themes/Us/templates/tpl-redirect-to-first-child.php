<?php
/*
Template Name: Redirect to first child
*/
$page_children = get_pages("child_of=".$post->ID."&sort_column=menu_order");

if ($page_children) {
	$first_child = $page_children[0];
	wp_redirect(get_permalink($first_child->ID));
}