<?php

namespace NPW;

class Populate{
    private $table = 'network_posts';

    function __construct(){
        add_action('init', array($this, 'clear_cache'), 10, 3);

        add_action('transition_post_status', array($this, 'do_indexing'), 10, 3);
    }

    function do_indexing($new_status, $old_status, $post ){
        if( $old_status == 'publish' && $new_status != 'publish' ){
            //here we're dealing with a status update that 'unpublishes' a post
            $this->remove_post($post);
        }
        elseif( $old_status != 'publish' && $new_status == 'publish' ){
            //ok we're dealing with a newly published post, lets add it
            $this->add_post($post);
        }elseif( $old_status == 'publish' && $new_status == 'publish' ){
            //this is a regular update, need to update the index
            $this->update_post($post);
        }else{
            //what about custom post statuses?
            //do nothing for now.
        }
        return;
    }

    function add_post($post){
        global $wpdb;
        $post_data = $this->populate_post_data($post);
        $wpdb->insert($wpdb->base_prefix . $this->table, $post_data);
        add_post_meta($post->ID, 'npw_id', $wpdb->insert_id);
    }

    function update_post($post){
        if( ! get_post_meta( $post->ID , 'npw_id' )){
            // is set so that existing posts can continue to be indexed
            $this->add_post( $post );
        } else {
            global $wpdb;
            $post_data = $this->populate_post_data($post);
            $where = array('id', get_post_meta( $post->ID, 'npw_id', true));
            $wpdb->update($wpdb_base_prefix . $this->table, $post_data, $where);
        }
    }

    function remove_post($post){
        global $wpdb;
        $where = array('id', get_post_meta( $post->ID, 'npw_id', true));
        $wpdb->delete( $wpdb_base_prefix . $this->table, $where);
        delete_post_meta($post->ID, 'npw_id');
    }

    function populate_post_data($post){
        $post_data = array(
            'post_id' => $post->ID,
            'blog_id' => get_current_blog_id(),
            'post_author' => $post->post_author,
            'post_date' => $post->post_date,
            'post_date_gmt' => $post->post_date_gmt,
            'post_content' => $post->post_content,
            'post_title' => $post->post_title,
            'post_excerpt' => $post->post_excerpt,
            'post_status' => $post->post_status,
            'post_type' => $post->post_type,
            'post_modified_gmt' =>$post->post_modified,
            'post_modified' => $post->post_modified_gmt,
            'guid' => $post->guid
        );
        return $post_data;
    }

}
