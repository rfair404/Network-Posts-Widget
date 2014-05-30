<?php

class NPW_Widget extends WP_Widget{
    function NPW_Widget(){
        $widget_ops = array( 'classname' => 'network-posts-widget', 'description' => __('Network Posts', 'network-posts-widget') );
        $control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'network-posts-widget' );
        $this->WP_Widget( 'network-posts-widget', 'Network Posts', $widget_ops, $control_ops );
    }

    function widget($args, $instance){
        extract($args);
        echo $before_widget;
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
        if($title){
            echo $before_title .  $title . $after_title;
        }

        $cache = get_site_transient('network-posts-widget-cache-output');
        echo $cache;
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        return $new_instance;
    }

     function form($instance) {
        $instance = wp_parse_args((array)$instance, array(
            'title' => '',
        ));

        $instance['title'] = (!empty($instance['title'])) ? $instance['title'] : '' ; ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'network-posts-widget'); ?>:</label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" size="20" style="width: 100%;" /><br />
        <p><span class="howto" style="clear:both;"><?php _e('Enter the widget title above as you wish it to appear on the site.', 'network-posts-widget'); ?></span></p>

    <?php
    }

}
