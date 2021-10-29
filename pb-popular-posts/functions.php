<?php

function pb_populars_dashboard_widget(){
    wp_add_dashboard_widget( 'pb_populars_dashboard', 'Список популярных постов', 'pb_show_dashboard_widget' );
}

function pb_show_dashboard_widget(){
    $img_src=plugins_url( '/img/loader.gif', __FILE__ );

    $user = wp_get_current_user(  );
    $popular = get_user_meta( $user->ID, 'pb-popular');
    if ( !$popular ){
        echo 'Список пуст';
        return;
    }
    echo '<ul>';
    foreach($popular as $pop){
        echo '<li class="cat-item cat-item-' . $pop . '">
            <a href="' . get_permalink( $pop ) . '" target="_blank">' . get_the_title( $pop ) . '</a>
            <span><a href="#" data-post="' . $pop . '" class="pb-popular-del">&#10008;</a></span>
            <span class="pb-popular-hidden"><img src="' . $img_src . '" class="loader-size"/></span>
        </li>';
    }
    echo '</ul>';
    echo '<div class="pb-popular-del-all"><button class="button" id="pb-popular-del-all">Отчистить список</button>
            <span class="pb-popular-hidden"><img src="' . $img_src . '" class="loader-size"/></span>
          </div>';
}

function pb_show_user_widget(){

    $user = wp_get_current_user(  );
    $popular = get_user_meta( $user->ID, 'pb-popular');
    if ( !$popular ){
        echo 'Список пуст';
        return;
    }
    echo '<ul>';
    foreach($popular as $pop){
        echo '<li class="cat-item cat-item-' . $pop . '">
            <a href="' . get_permalink( $pop ) . '" target="_blank">' . get_the_title( $pop ) . '</a>
        </li>';
    }
    echo '</ul>';
}

function pb_popular_content($content){
    if ( !is_single() || !is_user_logged_in() ) return $content;
    $img_src=plugins_url( '/img/loader.gif', __FILE__ );

    global $post;
    if ( pb_is_popular($post->ID) ){
        return '<p class="pb-popular-link"><span class="pb-popular-hidden"><img src="' . $img_src . '" class="loader-size"/></span><a data-action="del" href="#">Remove from Popular</a></p>' . $content;
    }
    return '<p class="pb-popular-link"><span class="pb-popular-hidden"><img src="' . $img_src . '" class="loader-size"/></span><a data-action="add" href="#">Add in Popular</a></p>' . $content;
}

function pb_popular_admin_scripts($hook){
    if ($hook != 'index.php') return;
    wp_enqueue_script( 'pb-popular-posts-admin-scripts', plugins_url( '/js/pb-popular-posts-admin-scripts.js', __FILE__ ), array('jquery'), null, true);
    wp_enqueue_style( 'pb-popular-posts-admin-style', plugins_url( '/css/pb-popular-posts-admin-style.css', __FILE__) );
    wp_localize_script( 'pb-popular-posts-admin-scripts', 'pbPopulars', ['nonce' => wp_create_nonce( 'pb-popular' )] );
}

function pb_populars_scripts(){
    if ( !is_user_logged_in() ) return;
    //if ( !is_single() || !is_user_logged_in() ) return;
    wp_enqueue_script( 'pb-popular-posts-scripts', plugins_url( '/js/pb-popular-posts-scripts.js', __FILE__ ), array('jquery'), null, true);
    wp_enqueue_style( 'pb-popular-posts-style', plugins_url( '/css/pb-popular-posts-style.css', __FILE__) );
    
    
    global $post; // $post->ID
    wp_localize_script( 'pb-popular-posts-scripts', 'pbPopulars', ['url' => admin_url( 'admin-ajax.php'),
    'nonce' => wp_create_nonce( 'pb-popular' ), 'postId' => get_the_ID()] );
}

function wp_ajax_pb_add(){
    if ( !wp_verify_nonce( $_POST['security'], 'pb-popular' ) ){
        wp_die('Security error');
    }
    
    $post_id = (int)$_POST['postId'];
    $user = wp_get_current_user(  );
    
    if (pb_is_popular($post_id)) wp_die();

    $resArray = array(
        'message' => 'Added',
        'data' => '<li class="cat-item cat-item-' . $post_id . '"><a href="#" target="_blank">New post</a></li>',
    );
    if ( add_user_meta( $user->ID, 'pb-popular', $post_id) ){
        wp_die(json_encode($resArray));
    }

    wp_die('Query is finished');
}

function wp_ajax_pb_del(){
    if ( !wp_verify_nonce( $_POST['security'], 'pb-popular' ) ){
        wp_die('Security error');
    }
    
    $post_id = (int)$_POST['postId'];
    $user = wp_get_current_user(  );
    
    if ( !pb_is_popular($post_id) ) wp_die();

    if ( delete_user_meta( $user->ID, 'pb-popular', $post_id) ){
        wp_die('Removed');
    }

    wp_die('Query is finished');
}

function wp_ajax_pb_del_all(){
    if ( !wp_verify_nonce( $_POST['security'], 'pb-popular' ) ){
        wp_die('Security error');
    }

    $user = wp_get_current_user(  );
    //delete_metadata( $meta_type:string, $object_id:integer, $meta_key:string, $meta_value:mixed, $delete_all:boolean )
    if ( delete_metadata( 'user', $user->ID, 'pb-popular') ){
        wp_die( "List is cleared" );
    }
    else{
        wp_die( "Clearing error" );
    }
}

function pb_is_popular($post_id){
    $user = wp_get_current_user(  );
    $popular = get_user_meta( $user->ID, 'pb-popular');
    
    foreach($popular as $pop){
        if($pop == $post_id) return true;
    }
    return false;
}

function pb_show_popular_posts(){

    if ( !is_user_logged_in() ) return '<p>Авторизуйтесь</p>';

    $user = wp_get_current_user(  );
    $popular = get_user_meta( $user->ID, 'pb-popular');
    if ( !$popular ){
        return '<p>Нет популярных статей</p>';
    }

    $out = '';
    foreach($popular as $pop){
        $post = get_post($pop);
        setup_postdata($post);
        $out .= '<h2><a href="' . get_permalink( $post->ID ) . '">' . get_the_title( $post->ID ) . '</a></h2>';
        $out .= preg_replace('/<img[^>]+./','',get_the_content());
        wp_reset_postdata();
    }

    return $out;
}