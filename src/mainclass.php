<?php
class SimplePlaylist
{
    protected static $instance = null;
    public  $post_type = 'sp_playlist';
    protected static $plugin_url = '';
    protected static $plugin_path = '';
    /**
     * SimplaPlaylist constructor. Intentionally left empty so that instances can be created without
     * re-loading of resources (e.g. scripts/styles), or re-registering hooks.
     * http://wordpress.stackexchange.com/questions/70055/best-way-to-initiate-a-class-in-a-wp-plugin
     * https://gist.github.com/toscho/3804204
     *
     * @since 1.0.0
     */
    public function __construct()
    {
    }

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function plugin_setup()
    {
        self::$plugin_url  = plugin_dir_url(__FILE__);
        self::$plugin_path = plugin_dir_path(__FILE__);

        // Initialization needed in every request.
        $this->init();
        $this->admin_init();
        $this->frontend_init();
        do_action('audioigniter_loaded');
    }
    public function init()
    {
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_assets']);
    }
    public function admin_init()
    {

        add_action('add_meta_boxes', [$this, 'register_metaboxes']);
        add_action('save_post_sp_playlist', [$this, 'save_tracks_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }
    public function frontend_init()
    {
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }
    public function register_assets()
    {
        //frontend scripts
        wp_register_script('frontend-music-player-script', plugins_url('simple-playlist/public/music-player/index.js'), [], '1.0', true);
        wp_register_style('frontend-music-player-css', plugins_url('simple-playlist/public/music-player/style.css'));
        //admin scripts
        wp_register_script('admin-script', plugins_url('simple-playlist/public/assets/index.js'), ['jquery'], '1.0', true);
        wp_register_style('admin-css', plugins_url('simple-playlist/public/assets/style.css'));
    }
    public function enqueue_frontend_assets()
    {
        wp_enqueue_script('frontend-music-player-script');
        wp_enqueue_style('frontend-music-player-css');
    }
    public function enqueue_admin_assets()
    {
        wp_enqueue_script('admin-script');
        wp_enqueue_style('admin-css');
        wp_enqueue_media();
    }
    public function register_shortcodes()
    {
        add_shortcode('sp_playlist', [$this, 'shortcode_output']);
    }
    public function register_metaboxes()
    {
        add_meta_box('sp-track-meta-box', 'Tracks', [$this, 'tracks_metabox_output'], $this->post_type, 'advanced', 'high');
        add_meta_box('sp-shortcode_text-meta-box', 'Shortcode', [$this, 'shortcode_text_metabox_output'], $this->post_type, 'side', 'low');
        add_meta_box('sp-settings-meta-box', 'Settings', [$this, 'shortcode_settings_output'], $this->post_type, 'side', 'normal');
    }
    public function tracks_metabox_output($post)
    {
        $tracks = get_post_meta($post->ID, 'sp-tracks');
        if ($tracks) {
?><div class='sp-track-input-form'>
    <p>Note: Song URL fields are required</p>
                <button class="sp-add-track secondary">Add Track</button>
                <button class="sp-clear-playlist secondary">Clear Playlist</button>
                <button class="sp-toggle-playlist">Toggle All</button>
                <form method='post'>
                    <?php wp_nonce_field(__FILE__, 'sp-track-nce');
                    ?>
                    <div id="sp-track-container">
                        <?php
                        foreach ($tracks[0] as $key => $track) {

                        ?> <fieldset data-key='<?php echo $key ?>'>
                                <div class="sp-toggle">
                                    <h4></h4><span> &#9650;</span>
                                </div>
                                <div class="sp-toggle-target">

                                    <input type='text' placeholder='Title' 
                                    class='sp-track-title'
                                    name='sp-tracks[<?php echo $key ?>][title] ?>]' value='<?php echo $track['title'] ?>' required />
                                    <input type='text' 
                                    class='sp-track-artiste'
                                    placeholder='Arti
                                ste(s)' name='sp-tracks[<?php echo $key ?>][artiste]' value='<?php echo $track['artiste'] ?>' required />
                                    <div class='sp-input-music-upload-container'>
                                        <input type='url' 
                                         class='sp-track-url'
                                         placeholder='Song URL' name='sp-tracks[<?php echo $key ?>][url]' value='<?php echo $track['url']  ?>' required />
                                        <button class="sp-upload">Upload</button>
                                    </div>
                                    <div class='sp-input-music-upload-container'>
                                        <input type='url' 
                                         class='sp-track-pic'
                                         placeholder='Cover Image' name='sp-tracks[<?php echo $key ?>][pic]' value='<?php echo $track['pic']  ?>'  />
                                        <button class="sp-upload-pic">Upload</button>
                                    </div>
                                    <input type="button" class="sp-remove-track secondary" value="Remove Track">
                                </div>
                            </fieldset>

                        <?php
                        }
                        ?>

                    </div>
                </form>
                <button class="sp-add-track secondary">Add Track</button>
                <button class="sp-clear-playlist secondary">Clear Playlist</button>
            </div>
        <?php      } else {
            // 'Sorry, no tracks was found on this playlist';
        ?>
            <div class='sp-track-input-form'>
            <p>Note: Song URL fields are required</p>
                <button class="sp-add-track secondary">Add Track</button>
                <button class="sp-clear-playlist secondary">Clear Playlist</button>
                <button class="sp-toggle-playlist">Toggle All</button>
                <form method='post'>
                    <?php wp_nonce_field(__FILE__, 'sp-track-nce') ?>

                    <div id="sp-track-container">
                        <fieldset data-key='1'>
                            <div class="sp-toggle">
                                <h4></h4><span> &#9650;</span>
                            </div>
                            <div class="sp-toggle-target">
                                <input type='text' placeholder='Title' name='sp-tracks[1][title]' class='sp-track-title' required />
                                <input type='text' placeholder='Artiste(s)' name='sp-tracks[1][artiste]' class='sp-track-artiste' required />
                                <div class='sp-input-music-upload-container'>
                                    <input type='url' placeholder='Song URL' name='sp-tracks[1][url]' class='sp-track-url' required />
                                    <button class="sp-upload">Upload</button>
                                </div>
                                <div class='sp-input-music-upload-container'>
                                    <input type='url' placeholder='Cover Image' name='sp-tracks[1][pic]' class='sp-track-pic'  />
                                    <button class="sp-upload-pic">Upload</button>
                                </div>
                                <input type="button" class="sp-remove-track secondary" value="Remove Track">

                            </div>
                        </fieldset>
                    </div>
                </form>
                <button class="sp-add-track secondary">Add Track</button>
                <button class="sp-clear-playlist secondary">Clear Playlist</button>
            </div>
        <?php

        }
    }
    public function save_tracks_meta($post_id, $post)
    {
        if (!isset($_POST['sp-tracks']) || !wp_verify_nonce($_POST['sp-track-nce'], __FILE__)) {
            return;
        }
        $tracks = $_POST['sp-tracks'];
        $settings = $_POST['sp-settings'];
        update_post_meta($post_id, 'sp-tracks', $tracks);
        update_post_meta($post_id, 'sp-settings', $settings);
        // var_dump($track);
    }
    public function shortcode_text_metabox_output($post)
    {
        echo '[' . $this->post_type . ' id=\'' . $post->ID . '\']';
    }
    public function shortcode_settings_output($post)
    {
        // $download = get_post_meta($post_id, 'sp-settings', true)['download-link'];
        echo ' ';
    }
    public function shortcode_output($attr)
    {
        $ft_url = content_url('plugins/simple-playlist/public/music-player/');
        $defaults = [
            'id' => null
        ];
        $args = shortcode_atts($defaults, $attr);
        $post_id = intval($args['id']);
        $post_meta = get_post_meta($post_id, 'sp-tracks', true);
        $tracks = $post_meta;
        if ($tracks) { ?>
            <div class="cp-wrapper">
            <div class="cp-container">
                <div class="" id="cp-polygon">
                    <div id="cp-play-options">
                        <img src="<?php echo $ft_url ?>images/repeat-solid.svg">
                    </div>
                </div>
                <audio src="" id="cp-audio"></audio>
                <?php foreach ($tracks as $key => $value) {
                    # code...
                ?>
                    <div class="cp-track">
                        <div class="cp-track-cont" data-id="<?php echo $key ?>" data-url="<?php echo $value['url'] ?>">
                            <div class="cp-image-container">
                                <img src="<?php echo  $value['pic'] ? $value['pic'] : $ft_url.'music-icon-photo.jpg' ?> ">
                            </div>
                            <div class="cp-middle">
                                <div class="cp-track-info">
                                    <span class="cp-track-title"><?php echo $value['title'] ?></span>
                                    <span class="cp-track-artistes"><?php echo $value['artiste'] ?></span>
                                </div>
                                <div class="cp-load-play-animation">
                                    <span class="durTime"></span>
                                    <div class="cp-pause-duration-container">
                                        <div class="cp-pause-duration">
                                        </div>
                                    </div>
                                    <div class="cp-pause-play">
                                        <img src="<?php echo $ft_url ?>images/pause-solid.svg" alt="pause/play">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cp-end">
                            <a href="<?php echo $value['url']?>" download=""><img src="<?php echo $ft_url ?>images/download-solid.svg"></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
            </div>
<?php
        } else {
            return;
        }
    }
    public function register_postmeta()
    {
        $args = [
            'single' => false,
            'type' => 'array'
        ];
        register_post_meta($this->post_type, 'sp-tracks', $args);
    }
    protected function plugin_activated()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        $this->register_post_types();
        $this->register_postmeta();

        do_action('simpleplaylist_deactivated');

        flush_rewrite_rules();
    }
    protected function plugin_deactivated()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        unregister_post_type($this->post_type);

        do_action('simpleplaylist_deactivated');

        flush_rewrite_rules();
    }
    public function register_post_types()
    {
        $labels = array(
            'name'               => 'Simple Playlists',
            'singular_name'      => 'Simple Playlist',
            'menu_name'          => 'Simple Playlists',
            'name_admin_bar'     => 'Simple Playlist',
            'add_new'            => 'Add New Playlist',
            'add_new_item'       => 'Add New Playlist',
            'edit_item'          => 'Edit Playlist',
            'new_item'           => 'New Playlist',
            'view_item'          => 'View Playlist',
            'search_items'       => 'Search Playlists',
            'not_found'          => 'No playlists found',
            'not_found_in_trash' => 'No playlists found in the trash',
        );

        $args = array(
            'labels'          => $labels,
            'singular_label'  => 'Simple Playlist',
            'public'          => false,
            'show_ui'         => true,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'has_archive'     => false,
            'supports'        => array('title'),
            'menu_icon'       => 'dashicons-playlist-audio',
        );

        register_post_type($this->post_type, $args);
    }
}

/**
 * Main instance of SimplePlaylist.
 *
 * Returns the working instance of SimplePlaylist.
 *
 * @since  1.0.0
 * @return SimplePlaylist
 */
function SimplePlaylist()
{
    return SimplePlaylist::instance();
}

add_action('plugins_loaded', [SimplePlaylist(), 'plugin_setup']);
register_activation_hook(__FILE__, array(SimplePlaylist(), 'plugin_activated'));
register_deactivation_hook(__FILE__, array(SimplePlaylist(), 'plugin_deactivated'));
