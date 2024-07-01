<?php
namespace Chifarol\WPPlaylist\Classes;


class Assets
{
    public function __construct(private MusicPlayer $musicPlayer)
    {

    }
    /**
     * Register all scripts and stylesheets.
     * @since 1.0.0
     */
    public function register_assets()
    {
        //frontend scripts
        wp_register_script('frontend-music-player-script', plugins_url('simple-playlist/public/music-player/index.js'), [], '1.0', true);
        wp_register_style('frontend-music-player-css', plugins_url('simple-playlist/public/music-player/style.css'));
        //admin scripts
        wp_register_script('admin-script', plugins_url('simple-playlist/public/assets/index.js'), ['jquery'], '1.0', true);
        wp_register_style('admin-css', plugins_url('simple-playlist/public/assets/style.css'));
        wp_localize_script('admin-script', 'sp_scripts', [
            'newField' => [
                'p_title' => esc_html__('Title', 'simple-playlist'),
                'p_artiste' => esc_html__('Artiste(s)', 'simple-playlist'),
                'p_url' => esc_html__('Song URL', 'simple-playlist'),
                'p_image' => esc_html__('Cover Image', 'simple-playlist'),
            ],
            'warning' => [
                'clear' => esc_html__('Are you sure you want to delete all tracks?', 'simple-playlist'),
            ]
        ]);
    }
    /**
     * Enqueue all frontend scripts and styles.
     * @since 1.0.0
     */
    public function enqueue_frontend_assets()
    {

        wp_enqueue_script('frontend-music-player-script');
        wp_enqueue_style('frontend-music-player-css');
        wp_add_inline_style('frontend-music-player-css', $this->musicPlayer->getCSSStyles());
    }
    /**
     * Enqueue all admin scripts and stylesheets.
     * @since 1.0.0
     */
    public function enqueue_admin_assets()
    {
        wp_enqueue_script('admin-script');
        wp_enqueue_style('admin-css');
        wp_enqueue_style('frontend-music-player-css');
        wp_add_inline_style('frontend-music-player-css', $this->musicPlayer->getCSSStyles());
        wp_enqueue_media();
    }

}