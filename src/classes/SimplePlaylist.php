<?php

namespace Chifarol\WPPlaylist\Classes;

use Chifarol\WPPlaylist\Classes\ShortCode;



class SimplePlaylist
{

    /**
     * URL of plugin directory.
     * @since   1.0.0
     */
    protected static $plugin_url = '';
    /**
     * Abs. Path of plugin directory.
     * @since   1.0.0
     */
    protected static $plugin_path = '';


    public function __construct(private MetaBoxes $metabox, private Assets $assets, private RegisterPost $registerPost, private SettingsPage $settings, private ShortCode $shortcode)
    {

    }

    /**
     * Runs the necessary plugin setup routine.
     * @since 1.0.0
     */
    public function plugin_setup()
    {
        self::$plugin_url = plugin_dir_url(__FILE__);
        self::$plugin_path = plugin_dir_path(__FILE__);

        // Initialization needed in every request.
        $this->init();
        $this->admin_init();
        $this->frontend_init();
    }
    /**
     * Register general actions, loads custom playlist colors/style.
     * @since 1.0.0
     */
    public function init()
    {
        add_action('init', [$this, 'load_text_domain']);
        add_action('init', [$this->registerPost, 'registerPostTypes']);
        add_action('init', [$this->registerPost, 'registerPostmeta']);
        add_action('init', [$this->assets, 'register_assets']);

    }
    /**
     * register admin actions.
     * @since 1.0.0
     */
    public function admin_init()
    {
        add_action('admin_menu', [$this->settings, 'add_settings_page']);
        add_action('add_meta_boxes', [$this->metabox, 'register_metaboxes']);
        add_action('save_post_sp_playlist', [$this->metabox, 'save_tracks_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [$this->assets, 'enqueue_admin_assets'], 50);
        add_action('load-edit.php?post_type=sp_playlist_page_sp-settings', [$this, 'sp_settings_save_options']);
    }

    /**
     * Register actions necessary for frontend.
     * @since 1.0.0
     */
    public function frontend_init()
    {
        add_action('init', [$this->shortcode, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this->assets, 'enqueue_frontend_assets'], 50);
    }
    /**
     * loads text domain.
     * @since 1.0.0
     */
    public function load_text_domain()
    {
        //frontend scripts
        load_plugin_textdomain('simple-playlist', false, 'simple-playlist/languages');
    }




}




