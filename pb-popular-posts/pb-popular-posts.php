<?php
/*
Plugin Name: Добавление статей в популярные
Description: Плагин позволяет авторизованным пользователям добавить статью в раздел "популярные"
Version: 1.0
Author: Сергей
*/

require __DIR__ . '/functions.php';
require __DIR__ . '/PB_Popular_widget.php';

add_filter( 'the_content', 'pb_popular_content' );
add_action( 'wp_enqueue_scripts', 'pb_populars_scripts' );
add_action( 'wp_ajax_pb_add', 'wp_ajax_pb_add' );
add_action( 'wp_ajax_pb_del', 'wp_ajax_pb_del' );
add_action( 'wp_ajax_pb_del_all', 'wp_ajax_pb_del_all' );
add_action( 'wp_dashboard_setup', 'pb_populars_dashboard_widget' );

add_action( 'admin_enqueue_scripts', 'pb_popular_admin_scripts' );

add_action( 'widgets_init', 'pb_popular_widget' );
function pb_popular_widget(){
    register_widget( 'PB_Popular_widget' );
}

add_shortcode( 'popular-posts', 'pb_show_popular_posts');