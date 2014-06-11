<?php

class NPW_Widget extends WP_Widget{
    function NPW_Widget(){
        $widget_ops = array( 'classname' => 'network-posts-widget', 'description' => __('Network Posts', 'network-posts-widget') );
        $control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'network_posts_widget' );
        $this->WP_Widget( 'network_posts_widget', 'Network Posts', $widget_ops, $control_ops );
    }
    /**
    * The widget output
    */
    function widget($args, $instance){
        extract($args);
        echo $before_widget;
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base);
        if($title){
            echo $before_title .  $title . $after_title;
        }
        $cache = new NPW\Cache($this->id);
        echo $cache->get_cached_output($this->id);
        echo $after_widget;
    }

    /**
    * Filter the options before save
    */
    function update($new_instance, $old_instance) {
        $cache = new NPW\Cache($this->id);
        $cache->clear_cache($this->id);
        return $new_instance;
    }

    /**
    * the Widget control form
    * @todo add option to exclude blogs
    * @todo add option to limit posts to N posts
    * @todo add option to exclude post types
    * @todo add option to change sort by (date, meta etc)
    */
     function form($instance) {
        $instance = wp_parse_args((array)$instance, array(
            'title' => '',
            'num_posts' => 10,
        ));

        $instance['title'] = (!empty($instance['title'])) ? $instance['title'] : '' ; ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'network-posts-widget'); ?>:</label>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" size="20" style="width: 100%;" /><br />
        <p><span class="howto" style="clear:both;"><?php _e('Enter the widget title above as you wish it to appear on the site.', 'network-posts-widget'); ?></span></p>


        <?php $instance['exclude_blogs'] = (!empty($instance['exclude_blogs'])) ? $instance['exclude_blogs'] : '' ; ?>
        <p><label for="<?php echo $this->get_field_id('exclude_blogs'); ?>"><?php _e('Blog ID\'s to Exclude', 'network-posts-widget'); ?>:</label>
        <input type="text" id="<?php echo $this->get_field_id('exclude_blogs'); ?>" name="<?php echo $this->get_field_name('exclude_blogs'); ?>" value="<?php echo esc_attr( $instance['exclude_blogs'] ); ?>" size="20" style="width: 100%;" /><br />
        <p><span class="howto" style="clear:both;"><?php _e('Select the blogs to exclude.', 'network-posts-widget'); ?></span></p>

        <?php $instance['num_posts'] = (!empty($instance['num_posts'])) ? $instance['num_posts'] : '' ; ?>
        <p><label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e('Number of Posts', 'network-posts-widget'); ?>:</label>
        <input type="text" id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" value="<?php echo esc_attr( $instance['num_posts'] ); ?>" size="5" /><br />
        <p><span class="howto" style="clear:both;"><?php _e('Enter the number of posts you wish to appear on the site.', 'network-posts-widget'); ?></span></p>


    <?php
    }

}
