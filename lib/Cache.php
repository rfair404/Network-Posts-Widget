<?php

namespace NPW;

class Cache{
    public $cache_time, $table;

    function __construct($id = false){
        $this->set_table();
        $this->set_cache_time();
        add_filter('npw_date', array($this, 'format_date'), 10, 1);
    }

    public function get_cached_output($id){
        // $cache = get_site_transient($id);
        // if( ! $cache ){
            $cache = $this->build_cache($id);
        // }
        return $cache;
    }

    /**
    * Sets the database table name
    * @todo move this to a common class so it can be done once
    */
    function set_table(){
        $this->table = 'network_posts';
    }

    /**
    * sets the default cache timeout
    * allows easy filter to increase/decrease the time
    */
    function set_cache_time(){
        $this->cache_time = apply_filters('npw_cache_time', 60*60);
    }
    // /**
    // * Checks if the cache transient exists, and creates it if not
    // */
    // function check_cache(){
    //     $cache = get_site_transient('network-posts-widget-cache-output');
    //     if( ! $cache ){
    //         $cache = $this->build_cache('network-posts-widget-cache-output');
    //     }
    // }

    /**
    * Clears the cache
    * @todo trigger a refresh
    */
    // function clear_cache(){
    //     delete_site_transient('network-posts-widget-cache-output');
    // }

    /**
    * Queries the posts and stashes the output into a transient
    */
    function build_cache($id){
        $widget_options = get_option('widget_network_posts_widget', true);
        $widget_instance_id = str_replace('network_posts_widget-', '', $id);
        $widget_instance_options = $widget_options[$widget_instance_id];

        $args = array(
            'limit' => $widget_instance_options['num_posts'],
            'exclude_blogs' => (isset($widget_instance_options['exclude_blogs'])) ? explode(',',str_replace(' ', '', $widget_instance_options['exclude_blogs'])) : false,
            'post_type' => (isset($widget_instance_options['post_type'])) ? $widget_instance_options['post_type'] : false,
        );


        $network_posts = $this->get_posts($args);
        if($network_posts){
            $post_html = $this->markup_posts( $network_posts );
            set_site_transient($id, $post_html, $this->cache_time );
            return $post_html;
        } else {
            return false;
        }

    }

    /**
    * Applies a "template" to the posts
    * @todo make this actually something that can be overridden easily
    * @param array $network_posts the posts result
    * @return string $markup the html to store
    */
    function markup_posts($network_posts){
        if(is_array($network_posts)){
            $markup = '<div class="network-posts">';
            foreach($network_posts as $network_post){
                switch_to_blog($network_post->blog_id);
                $markup .= '<div class="hentry">';
                $markup .= sprintf('<h3 class="entry-title"><a href="%s" title="%s">%s</a></h3>', get_the_permalink( $network_post->post_id ), __('View Post', 'network-posts-widget'), apply_filters('the_title', $network_post->post_title));

                if(has_post_thumbnail($network_post->post_id)) {
                    $markup .= sprintf('<figure>%s</figure>', get_the_post_thumbnail( $network_post->post_id ) );
                }
                $user = get_user_by('id', $network_post->post_author);
                $markup .= sprintf('<p class="meta"><span class="author"><a href="%s" title="%s">%s</a></span> <span class="date">%s</span></p>', get_author_posts_url( $network_post->post_author ), esc_attr($user->display_name), $user->display_name, apply_filters('npw_date', $network_post->post_date));
                if($network_post->post_excerpt){
                    $markup .= apply_filters('the_excerpt', $network_post->post_excerpt);
                }else{
                    $markup .= apply_filters('the_excerpt', $network_post->post_content);
                }

                $markup .= '</div>';
                restore_current_blog();
            }
            $markup .= '</div>';

            restore_current_blog();
            return $markup;
        }
        return false;
    }

    /**
    * Queries the latest posts in the index
    * @todo make queries that are more customizable
    * @todo allow interfaces for multiple widgets / blogs to customize
    * @return mixed $network_posts the array of found posts - false if none or error
    */
    function get_posts($args = array()){
        global $wpdb;
        $posts_options = get_site_option('network-post-widget-settings');
        $args = wp_parse_args($args, $posts_options);

        $filterable_where = "WHERE 1=1 ";
        $filterable_where .= (!$args['exclude_blogs']) ? "" : "AND `blog_id` NOT IN('" . implode("', '",$args['exclude_blogs']) . "')";
        $filterable_where .= (!$args['post_type']) ? "AND `post_type` LIKE 'post'" : "";

        $query = $wpdb->prepare("
            SELECT * FROM $wpdb->base_prefix$this->table
            $filterable_where
            ORDER BY `post_date` DESC
            LIMIT %d", $args['limit']);
        $network_posts = $wpdb->get_results( $query, OBJECT );
        return $network_posts;
    }

    /**
    * Formats the date per the WordPress post date format
    * @param srting $date the date timestamp
    * @return string $date the formatted date string
    */
    function format_date($date){
        $date_format = get_option('date_format');
        return date($date_format, strtotime($date));
    }
}
