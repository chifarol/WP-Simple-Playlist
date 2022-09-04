<?php
class SimplePlaylist
{
    protected static $instance = null;
    public  $post_type = 'sp_playlist';
    public  $music_player_custom_style = '';
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
        add_action('init', [$this, 'load_text_domain']);
        add_action('init', [$this, 'register_post_types']);
        add_action('init', [$this, 'register_assets']);
        $settings = get_option('sp-settings', []);

        $mcolor = $settings['mcolor'] ? esc_html($settings['mcolor']) : "#202020";
        $tcolor = $settings['tcolor'] ? esc_html($settings['tcolor']) : "#3e3e3e";
        $acolor = $settings['acolor'] ? esc_html($settings['acolor']) : "#7fd84b";
        $scolor = $settings['scolor'] ? esc_html($settings['scolor']) : "#000";
        $pcolor = $settings['pcolor'] ? esc_html($settings['pcolor']) : "#fff";
        $this->music_player_custom_style = '
        .cp-container {
        background: ' . $mcolor . ';
        color:' . $pcolor . ';
        }
        #cp-polygon {
            background-color:' . $mcolor . ';
        }
        .cp-track {
            background: ' . $tcolor . ';
        }
        .cp-pause-duration {
            background: ' . $acolor . ';
        }
        .cp-pause-play svg {
            fill: ' . $acolor . ';
        }
        .cp-track.cp-selected {
            box-shadow: 0px 0px 18px 0px ' . $scolor . ';
            color: ' . $acolor . ';
        }
        #cp-play-options svg {
            transform: scale(0.4);
            fill: ' . $acolor . ';
        }

        #cp-play-options .gray {
            fill: ' . $tcolor . ';
        }
        .cp-end svg {
            fill: ' . $pcolor . ';
        }
        ';
    }
    public function admin_init()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('add_meta_boxes', [$this, 'register_metaboxes']);
        add_action('save_post_sp_playlist', [$this, 'save_tracks_meta'], 10, 2);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('load-edit.php?post_type=sp_playlist_page_sp-settings', [$this,  'sp_settings_save_options']);
    }
    public function frontend_init()
    {
        add_action('init', [$this, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }
    public function load_text_domain()
    {
        //frontend scripts
        load_plugin_textdomain('simple-playlist', false, 'simple-playlist/languages');
    }
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
                'p_title'     => esc_html__('Title', 'simple-playlist'),
                'p_artiste'       => esc_html__('Artiste(s)', 'simple-playlist'),
                'p_url' => esc_html__('Song URL', 'simple-playlist'),
                'p_image' => esc_html__('Cover Image', 'simple-playlist'),
            ],
            'warning' => [
                'clear' => esc_html__('Are you sure you want to delete all tracks?', 'simple-playlist'),
            ]
        ]);
    }
    public function enqueue_frontend_assets()
    {

        wp_enqueue_script('frontend-music-player-script');
        wp_enqueue_style('frontend-music-player-css');
        wp_add_inline_style('frontend-music-player-css', $this->music_player_custom_style);
    }
    public function enqueue_admin_assets()
    {
        wp_enqueue_script('admin-script');
        wp_enqueue_style('admin-css');
        wp_enqueue_style('frontend-music-player-css');
        wp_add_inline_style('frontend-music-player-css', $this->music_player_custom_style);
        wp_enqueue_media();
    }
    public function register_shortcodes()
    {
        add_shortcode('sp_playlist', [$this, 'shortcode_output']);
    }
    public function add_settings_page()
    {
        add_submenu_page(
            'edit.php?post_type=sp_playlist',
            __('Simple Playlist Settings', 'simple-playlist'),
            __('Settings', 'simple-playlist'),
            'manage_options',
            'sp-settings',
            [$this, 'settings_page_output']
        );
    }
    public function settings_page_output()
    {
        if (isset($_POST['sp-settings']) && wp_verify_nonce($_POST['sp-settings-nce'], __FILE__)) {
            $settings = $_POST['sp-settings'];

            $settings['mcolor'] = esc_html($settings['mcolor']);
            $settings['tcolor'] = esc_html($settings['tcolor']);
            $settings['acolor'] = esc_url($settings['acolor']);
            $settings['scolor'] = esc_url($settings['scolor']);

            update_option('sp-settings', $settings);
        }
        $settings = get_option('sp-settings', []);

        $mcolor = $settings['mcolor'] ? $settings['mcolor'] : "#202020";
        $tcolor = $settings['tcolor'] ? $settings['tcolor'] : "#3e3e3e";
        $acolor = $settings['acolor'] ? $settings['acolor'] : "#7fd84b";
        $scolor = $settings['scolor'] ? $settings['scolor'] : "#000";
        $pcolor = $settings['pcolor'] ? $settings['pcolor'] : "#fff";

?>
        <div class="wrap">
            <form action="" method="post" id="sp-settings-form">
                <h2><?php esc_html_e('Color Settings', 'simple-playlist')?></h2>
                <?php wp_nonce_field(__FILE__, 'sp-settings-nce') ?>
                <div>
                    <label for="sp-mcolor"><?php esc_html_e('Background Color: ', 'simple-playlist') ?></label>
                    <input type="color" id="sp-settings-mcolor" name="sp-settings[mcolor]" value="<?php echo $mcolor ?>" />
                </div>
                <div>
                    <label for="sp-settings-tcolor"><?php esc_html_e('Track Color: ', 'simple-playlist') ?></label>
                    <input type="color" id="sp-settings-tcolor" name="sp-settings[tcolor]" value="<?php echo $tcolor ?>" />
                </div>
                <div>
                    <label for="sp-settings-acolor"><?php esc_html_e('Accent Color: ', 'simple-playlist') ?></label>
                    <input type="color" id="sp-settings-acolor" name="sp-settings[acolor]" value="<?php echo $acolor ?>" />
                </div>
                <div>
                    <label for="sp-settings-scolor"><?php esc_html_e('Shadow Color: ', 'simple-playlist') ?></label>
                    <input type="color" id="sp-settings-scolor" name="sp-settings[scolor]" value="<?php echo $scolor ?>" />
                </div>
                <div>
                    <label for="sp-settings-pcolor"><?php esc_html_e('Primary Color: ', 'simple-playlist') ?></label>
                    <input type="color" id="sp-settings-pcolor" name="sp-settings[pcolor]" value="<?php echo $pcolor ?>" />
                </div>
                <input type="submit" value="<?php esc_html_e('Save', 'simple-playlist') ?>" />
            </form>
            <div class="sp-showcase">
                <h2><?php esc_html_e('Sample Playlist', 'simple-playlist')?></h2>
                <p><?php esc_html_e('<b>Note: </b>If changes does not reflect after saving please refresh', 'simple-playlist')?></p>
                <div class="cp-wrapper">
                    <div class="cp-container">
                        <div class="" id="cp-polygon">
                            <div id="cp-play-options">
                                <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                    <g>
                                        <path d="M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm-4-22.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z" />
                                    </g>
                                </svg>
                            </div>
                        </div>
                        <audio src="" id="cp-audio"></audio>
                        <div class="cp-track cp-selected">
                            <div class="cp-track-cont">
                                <div class="cp-image-container">
                                    <img src="<?php echo content_url('plugins/simple-playlist/public/music-player/images/music-icon-photo.jpg'); ?>">
                                </div>
                                <div class="cp-middle">
                                    <div class="cp-track-info">
                                        <span class="cp-track-title">Abulo</span>
                                        <span class="cp-track-artistes">Phyno</span>
                                    </div>
                                    <div class="cp-load-play-animation">
                                        <span class="durTime">1:02</span>
                                        <div class="cp-pause-duration-container">
                                            <div class="cp-pause-duration" style="width:20%">
                                            </div>
                                        </div>
                                        <div class="cp-pause-play">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                                <g>
                                                    <path d="M26.25 38V10H38v28ZM10 38V10h11.75v28Z" />
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cp-end">
                                <a href="">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                        <g>
                                            <path d="m24 32.35-9.65-9.65 2.15-2.15 6 6V8h3v18.55l6-6 2.15 2.15ZM8 40V29.85h3V37h26v-7.15h3V40Z" />
                                        </g>
                                    </svg></a>
                            </div>
                        </div>
                        <div class="cp-track">
                            <div class="cp-track-cont">
                                <div class="cp-image-container">
                                    <img src="<?php echo content_url('plugins/simple-playlist/public/music-player/images/music-icon-photo.jpg'); ?>">
                                </div>
                                <div class="cp-middle">
                                    <div class="cp-track-info">
                                        <span class="cp-track-title">Ghost Mode</span>
                                        <span class="cp-track-artistes">Phyno x Olamide</span>
                                    </div>
                                    <div class="cp-load-play-animation">
                                        <span class="durTime"></span>
                                        <div class="cp-pause-duration-container">
                                            <div class="cp-pause-duration">
                                            </div>
                                        </div>
                                        <div class="cp-pause-play">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                                <g>
                                                    <path d="M26.25 38V10H38v28ZM10 38V10h11.75v28Z" />
                                                </g>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cp-end">
                                <a href="">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                        <g>
                                            <path d="m24 32.35-9.65-9.65 2.15-2.15 6 6V8h3v18.55l6-6 2.15 2.15ZM8 40V29.85h3V37h26v-7.15h3V40Z" />
                                        </g>
                                    </svg></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php }

    public function register_metaboxes()
    {
        add_meta_box('sp-track-meta-box', esc_html__('Tracks', 'simple-playlist'), [$this, 'tracks_metabox_output'], $this->post_type, 'advanced', 'high');
        add_meta_box('sp-shortcode_text-meta-box', esc_html__('Shortcode', 'simple-playlist'), [$this, 'shortcode_text_metabox_output'], $this->post_type, 'side', 'low');
        add_meta_box('sp-settings-meta-box', esc_html__('Settings', 'simple-playlist'), [$this, 'shortcode_settings_output'], $this->post_type, 'side', 'normal');
    }
    public function tracks_metabox_output($post)
    {
        $tracks = get_post_meta($post->ID, 'sp-tracks');
        if ($tracks) {
        ?><div class='sp-track-input-form'>
                <p><?php esc_html_e('Note: Song URL fields are required', 'simple-playlist') ?></p>
                <button class="sp-add-track secondary"><?php esc_html_e('Add Track', 'simple-playlist') ?></button>
                <button class="sp-clear-playlist secondary"><?php esc_html_e('Clear Playlist', 'simple-playlist') ?></button>
                <button class="sp-toggle-playlist"><?php esc_html_e('Toggle All', 'simple-playlist') ?></button>
                <form method='post'>
                    <?php wp_nonce_field(__FILE__, 'sp-track-nce');
                    ?>
                    <div id="sp-track-container">
                        <?php
                        foreach ($tracks[0] as $key => $track) {
                            $track_pic = '';
                            if (array_key_exists('pic', $track)) {
                                $track_pic = $track['pic'];
                            }
                        ?> <fieldset data-key='<?php echo absint($key) ?>'>
                                <div class="sp-toggle">
                                    <h4></h4><span> &#9650;</span>
                                </div>
                                <div class="sp-toggle-target">

                                    <input type='text' placeholder='<?php esc_attr_e('Title', 'simple-playlist') ?>' class='sp-track-title' name='sp-tracks[<?php echo $key ?>][title] ?>]' value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track['title']) ?>' required />

                                    <input type='text' class='sp-track-artiste' placeholder='<?php esc_attr_e('Arti
                                ste(s)', 'simple-playlist') ?>' name='sp-tracks[<?php echo $key ?>][artiste]' value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track['artiste']) ?>' required />

                                    <div class='sp-input-music-upload-container'>
                                        <input type='url' class='sp-track-url' placeholder='<?php esc_attr_e('Song URL', 'simple-playlist') ?>' name='sp-tracks[<?php echo $key ?>][url]' value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track['url'])  ?>' required />
                                        <button class="sp-upload">Upload</button>
                                    </div>

                                    <div class='sp-input-music-upload-container'>
                                        <input type='url' class='sp-track-pic' placeholder='<?php esc_attr_e('Cover Image', 'simple-playlist') ?>' name='sp-tracks[<?php echo $key ?>][pic]' value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track_pic)  ?>' />
                                        <button class="sp-upload-pic"><?php esc_html_e('Upload', 'simple-playlist') ?></button>
                                    </div>
                                    <input type="button" class="sp-remove-track secondary" value="<?php esc_attr_e('Remove Track', 'simple-playlist') ?>">
                                </div>
                            </fieldset>

                        <?php
                        }
                        ?>

                    </div>
                </form>
                <button class="sp-add-track secondary"><?php esc_html_e('Add Track', 'simple-playlist') ?></button>
                <button class="sp-clear-playlist secondary"><?php esc_html_e('Clear Playlist', 'simple-playlist') ?></button>
            </div>
        <?php      } else {
            // 'Sorry, no tracks was found on this playlist';
        ?>
            <div class='sp-track-input-form'>
                <p><?php esc_html_e('Note: Song URL fields are required', 'simple-playlist') ?></p>
                <button class="sp-add-track secondary"><?php esc_html_e('Add Track', 'simple-playlist') ?></button>
                <button class="sp-clear-playlist secondary"><?php esc_html_e('Clear Playlist', 'simple-playlist') ?></button>
                <button class="sp-toggle-playlist"><?php esc_html_e('Toggle All', 'simple-playlist') ?></button>
                <form method='post'>
                    <?php wp_nonce_field(__FILE__, 'sp-track-nce') ?>

                    <div id="sp-track-container">
                        <fieldset data-key='1'>
                            <div class="sp-toggle">
                                <h4></h4><span> &#9650;</span>
                            </div>
                            <div class="sp-toggle-target">
                                <input type='text' placeholder='<?php esc_attr_e('Title', 'simple-playlist') ?>' name='sp-tracks[1][title]' class='sp-track-title' required />
                                <input type='text' placeholder='<?php esc_attr_e('Artiste(s)', 'simple-playlist') ?>' name='sp-tracks[1][artiste]' class='sp-track-artiste' required />
                                <div class='sp-input-music-upload-container'>
                                    <input type='url' placeholder='<?php esc_attr_e('Song URL', 'simple-playlist') ?>' name='sp-tracks[1][url]' class='sp-track-url' required />
                                    <button class="sp-upload"><?php esc_html_e('Upload', 'simple-playlist') ?></button>
                                </div>
                                <div class='sp-input-music-upload-container'>
                                    <input type='url' placeholder='<?php esc_attr_e('Cover Image', 'simple-playlist') ?>' name='sp-tracks[1][pic]' class='sp-track-pic' />
                                    <button class="sp-upload-pic"><?php esc_html_e('Upload', 'simple-playlist') ?></button>
                                </div>
                                <input type="button" class="sp-remove-track secondary" value="<?php esc_attr_e('Remove Track', 'simple-playlist') ?>">

                            </div>
                        </fieldset>
                    </div>
                </form>
                <button class="sp-add-track secondary"><?php esc_html_e('Add Track', 'simple-playlist') ?></button>
                <button class="sp-clear-playlist secondary"><?php esc_html_e('Clear Playlist', 'simple-playlist') ?></button>
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
        foreach ($tracks[0] as $key => $track) {
            $track['title'] = esc_html($track['title']);
            $track['artiste'] = esc_html($track['artiste']);
            $track['url'] = esc_url($track['url']);
            $track['pic'] = esc_url($track['pic']);
        }
        update_post_meta($post_id, 'sp-tracks', $tracks);
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
        if ($tracks) {
            $player_html = '
            <div class="cp-wrapper">
                <div class="cp-container">
                    <div class="" id="cp-polygon">
                        <div id="cp-play-options">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                        <g >
                        <path d="M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm-4-22.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z"/>
                        </g></svg>
                        </div>
                    </div>
                    <audio src="" id="cp-audio"></audio>';
            foreach ($tracks as $key => $value) {
                $track_pic = $ft_url . 'images/music-icon-photo.jpg';
                if (array_key_exists('pic', $value) && trim($value['pic']) != '') {
                    $track_pic = $value['pic'];
                }
                if (!$value['url']) {
                    return;
                }
                $player_html .= '
                        <div class="cp-track">
                            <div class="cp-track-cont" data-id="' . $key . '"data-url="' . esc_attr($value['url']) . '">
                                <div class="cp-image-container">
                                    <img src="' . esc_attr($track_pic) . '">
                                </div>
                                <div class="cp-middle">
                                    <div class="cp-track-info">
                                        <span class="cp-track-title">' . esc_html($value['title']) . '</span>
                                        <span class="cp-track-artistes">' . esc_html($value['artiste']) . '</span>
                                    </div>
                                    <div class="cp-load-play-animation">
                                        <span class="durTime"></span>
                                        <div class="cp-pause-duration-container">
                                            <div class="cp-pause-duration">
                                            </div>
                                        </div>
                                        <div class="cp-pause-play">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                        <g >
                                        <path d="M26.25 38V10H38v28ZM10 38V10h11.75v28Z"/>
                                        </g>
                                        </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cp-end">
                                <a href="' . esc_attr($value['url']) . '" download="">
                                <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48"> <g > <path d="m24 32.35-9.65-9.65 2.15-2.15 6 6V8h3v18.55l6-6 2.15 2.15ZM8 40V29.85h3V37h26v-7.15h3V40Z"/> </g> </svg></a>
                            </div>
                        </div>';
            }
            $player_html .= '</div></div>';
            return $player_html;
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
            'name'               => esc_html__('Simple Playlists', 'simple-playlist'),
            'singular_name'      => esc_html__('Simple Playlist', 'simple-playlist'),
            'menu_name'          => esc_html__('Simple Playlists', 'simple-playlist'),
            'name_admin_bar'     => esc_html__('Simple Playlist', 'simple-playlist'),
            'add_new'            => esc_html__('Add New Playlist', 'simple-playlist'),
            'add_new_item'       => esc_html__('Add New Playlist', 'simple-playlist'),
            'edit_item'          => esc_html__('Edit Playlist', 'simple-playlist'),
            'new_item'           => esc_html__('New Playlist', 'simple-playlist'),
            'view_item'          => esc_html__('View Playlist', 'simple-playlist'),
            'search_items'       => esc_html__('Search Playlists', 'simple-playlist'),
            'not_found'          => esc_html__('No playlists found', 'simple-playlist'),
            'not_found_in_trash' => esc_html__('No playlists found in the trash', 'simple-playlist'),
        );

        $args = array(
            'labels'          => $labels,
            'singular_label'  => esc_html__('Simple Playlist', 'simple-playlist'),
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
