<?php
/*
Template Name: My Custom Template
*/


get_header(); 




$gssmRender->connect_button('Connect with Stripe');


$uid = get_current_user_ID();
$meta = get_user_meta($uid);
echo '<pre>';
print_r($meta);
echo '</pre>';

get_footer(); ?>