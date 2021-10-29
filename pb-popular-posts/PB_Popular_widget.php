<?php

class PB_Popular_widget extends WP_Widget{

    //настройки виджета в списке виджетов
    public function __construct(){
        $args2 = [
            'name' => 'Популярные записи',
            'description' => 'Выводит блок популярных у пользователя записей'
        ];
        parent::__construct( 'pb-popular-widget', '', $args2 );
    }

    // Creating widget front-end
    public function widget( $args, $instance ) {

        if ( !is_user_logged_in() ) return;

        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );
 
        echo $args['before_widget'];
            echo $args['before_title'];
                echo $instance['title'];
            echo $args['after_title'];
            pb_show_user_widget();
        echo $args['after_widget'];
    }
    
    // Creating widget Backend 
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }

        ?>
            <p>
                <label for="<?php echo $this->get_field_name( 'title' ); ?>">Заголовок</label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
        <?php
    }
    
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
 
        return $instance;
    }

}