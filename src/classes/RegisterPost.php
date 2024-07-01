<?php

namespace Chifarol\WPPlaylist\Classes;

class RegisterPost
{
    /**
     * Instance of RegisterPost Class.
     * @since   1.0.0
     */
    protected static $instance = null;

    /**
     * Post type Name.
     * @since   1.0.0
     */
    static private $postType = 'sp_playlist';


    /**
     * get post type.
     * @since   1.0.0
     */

    static public function getPostType()
    {
        return self::$postType;
    }

    /**
     * Register post type meta fields.
     *
     * @since 1.0.0
     */
    static public function registerPostmeta()
    {
        $args = [
            'single' => false,
            'type' => 'array'
        ];
        register_post_meta(self::getPostType(), 'sp-tracks', $args);
    }

    /**
     * Register Simple Playlist Post Type.
     *
     * @since 1.0.0
     */
    static public function registerPostTypes()
    {
        $labels = array(
            'name' => esc_html__('Simple Playlists', 'simple-playlist'),
            'singular_name' => esc_html__('Simple Playlist', 'simple-playlist'),
            'menu_name' => esc_html__('Simple Playlists', 'simple-playlist'),
            'name_admin_bar' => esc_html__('Simple Playlist', 'simple-playlist'),
            'add_new' => esc_html__('Add New Playlist', 'simple-playlist'),
            'add_new_item' => esc_html__('Add New Playlist', 'simple-playlist'),
            'edit_item' => esc_html__('Edit Playlist', 'simple-playlist'),
            'new_item' => esc_html__('New Playlist', 'simple-playlist'),
            'view_item' => esc_html__('View Playlist', 'simple-playlist'),
            'search_items' => esc_html__('Search Playlists', 'simple-playlist'),
            'not_found' => esc_html__('No playlists found', 'simple-playlist'),
            'not_found_in_trash' => esc_html__('No playlists found in the trash', 'simple-playlist'),
        );

        $args = array(
            'labels' => $labels,
            'singular_label' => esc_html__('Simple Playlist', 'simple-playlist'),
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'has_archive' => false,
            'supports' => array('title'),
            'menu_icon' => 'dashicons-playlist-audio',
        );

        register_post_type(self::getPostType(), $args);
    }
}