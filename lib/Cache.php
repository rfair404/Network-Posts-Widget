<?php

namespace NPW;

class Cache{
    public $cache_time, $table;

    function __construct(){
        add_action('init', array($this, 'set_cache_time'), 10);
        add_action('init', array($this, 'set_table'), 10);
        add_action('init', array($this, 'check_cache') );
    }

    function set_table(){
        $this->table = 'network_posts';
    }

    function set_cache_time(){
        $this->cache_time = apply_filters('nsf_cache_time', 1);
    }

    function check_cache(){
        $cache = get_site_transient('network-posts-widget-cache-output');
        if( ! $cache ){
            $cache = $this->build_cache();
        }
    }

    function clear_cache(){
        delete_site_transient('network-posts-widget-cache-output');
    }

    function build_cache(){
        $network_posts = $this->get_posts();
        if($network_posts){
            $post_html = $this->markup_posts( $network_posts );
            set_site_transient('network-posts-widget-cache-output', $post_html, $this->cache_time );
            return $post_html;
        } else {
            return false;
        }

    }

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
                $markup .= sprintf('<p class="meta"><span class="author"><a href="%s" title="%s">%s</a></span><span class="date">%s</span></p>', get_author_posts_url( $network_post->post_author ), esc_attr($user->user_nicename), $user->user_nicename, $network_post->post_date);
                if($network_post->post_excerpt){
                    $markup .= apply_filters('the_excerpt', $network_post->post_excerpt);
                }else{
                    $markup .= apply_filters('the_excerpt', $network_post->post_content);
                }

                $markup .= '</div>';

            }
            $markup .= '</div>';

            restore_current_blog();
            return $markup;
        }
        return false;
    }

    function get_posts(){
        global $wpdb;

        $posts_options = get_site_option('network-post-widget-settings');
        $query = $wpdb->prepare("SELECT * FROM $wpdb->base_prefix$this->table WHERE `post_type` LIKE 'post' ORDER BY `post_date` DESC LIMIT %d", '10');

        $network_posts = $wpdb->get_results( $query, OBJECT );
        return $network_posts;
    }
}