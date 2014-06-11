<?php

namespace NPW;

class Populate{
    private $table = 'network_posts';

    function __construct(){
        add_action('transition_post_status', array($this, 'do_indexing'), 10, 3);
    }
    /**
    * determines if the post should be indexed based on post status
    * @param string $new_status the incoming (updated) post status
    * @param string $old_status the outcoing (outdated) post status
    * @param mixed $post the edited post object
    */
    function do_indexing($new_status, $old_status, $post ){
        if( $old_status == 'publish' && $new_status != 'publish' ){
            //here we're dealing with a status update that 'unpublishes' a post
            $this->remove_post($post);
        }
        elseif( $old_status != 'publish' && $new_status == 'publish' ){
            //ok we're dealing with a newly published post, lets add it
            $this->add_post($post);
        }else{
            $this->update_post($post);
        }
        return;
    }

    /**
    * adds post to the network index
    * @todo remove dependency on get post meta and rely on something more reliable like matching blog_id and post_id
    * @param mixed $post the post to use
    */
    function add_post($post){
        global $wpdb;
        $post_data = $this->populate_post_data($post);
        $wpdb->insert($wpdb->base_prefix . $this->table, $post_data);
    }

    /**
    * Updates posts in the indes (or creates them if they don't exist)
    * @todo add a more reliable way to add/update, perhaps use $wpdb->replace instead
    * @param object $post the post to update
    */
    function update_post($post){
        global $wpdb;
        $post_data = $this->populate_post_data($post);
        $where = array('post_id' => $post->ID, 'blog_id' => get_current_blog_id());
        $wpdb->update($wpdb->base_prefix . $this->table, $post_data, $where);
    }

    /**
    * Removes posts from the index
    * @todo don't rely on post meta
    * @param mixed $post the post to remove
    */
    function remove_post($post){
        global $wpdb;
        $where = array('post_id' => $post->ID, 'blog_id' => get_current_blog_id());
        $wpdb->delete( $wpdb->base_prefix . $this->table, $where);
    }

    /**
    * matches up the post object values to our table's expected keys
    * @param mixed $post the post to assign
    * @return array $post_data the assigned post data
    */
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
