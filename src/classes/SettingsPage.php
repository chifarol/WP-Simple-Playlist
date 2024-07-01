<?php


namespace Chifarol\WPPlaylist\Classes;


class SettingsPage
{
    public function __construct(private Settings $settings)
    {

    }

    /**
     * Register 'Settings' submenu for post type.
     * @since 1.0.0
     */
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


    /**
     * Render content of 'Settings' submenu.
     * @since 1.0.0
     */
    public function settings_page_output()
    {
        if (isset($_POST['sp-settings']) && wp_verify_nonce($_POST['sp-settings-nce'], __FILE__)) {
            $settings = $_POST['sp-settings'];

            $settings['mcolor'] = esc_attr($settings['mcolor']);
            $settings['tcolor'] = esc_attr($settings['tcolor']);
            $settings['acolor'] = esc_attr($settings['acolor']);
            $settings['scolor'] = esc_attr($settings['scolor']);
            $settings['pcolor'] = esc_attr($settings['pcolor']);

            update_option('sp-settings', $settings);
        }
        $settings = get_option('sp-settings', []);
        $mcolor = array_key_exists('mcolor', $settings) ? esc_html($settings['mcolor']) : "#202020";
        $tcolor = array_key_exists('tcolor', $settings) ? esc_html($settings['tcolor']) : "#3e3e3e";
        $acolor = array_key_exists('acolor', $settings) ? esc_html($settings['acolor']) : "#7fd84b";
        $scolor = array_key_exists('scolor', $settings) ? esc_html($settings['scolor']) : "#000";
        $pcolor = array_key_exists('pcolor', $settings) ? esc_html($settings['pcolor']) : "#ffffff";
        ?>

        <div>
            <form action="" method="post" id="sp-settings-form">
                <h2><?php esc_html_e('Color Settings', 'simple-playlist') ?></h2>
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
                <h2><?php esc_html_e('Sample Playlist', 'simple-playlist') ?></h2>
                <p><b><?php esc_html_e('Note: ', 'simple-playlist') ?> </b>
                    <?php esc_html_e('If changes does not reflect after saving please refresh', 'simple-playlist') ?></p>
                <div class="cp-wrapper">
                    <div class="cp-container">
                        <div class="" id="cp-polygon">
                            <div id="cp-play-options">
                                <svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
                                    <g>
                                        <path
                                            d="M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm-4-22.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z" />
                                    </g>
                                </svg>
                            </div>
                        </div>
                        <audio src="" id="cp-audio"></audio>
                        <div class="cp-track cp-selected">
                            <div class="cp-track-cont">
                                <div class="cp-image-container">
                                    <img
                                        src="<?php echo content_url('plugins/simple-playlist/public/music-player/images/music-icon-photo.jpg'); ?>">
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
                                            <path
                                                d="m24 32.35-9.65-9.65 2.15-2.15 6 6V8h3v18.55l6-6 2.15 2.15ZM8 40V29.85h3V37h26v-7.15h3V40Z" />
                                        </g>
                                    </svg></a>
                            </div>
                        </div>
                        <div class="cp-track">
                            <div class="cp-track-cont">
                                <div class="cp-image-container">
                                    <img
                                        src="<?php echo content_url('plugins/simple-playlist/public/music-player/images/music-icon-photo.jpg'); ?>">
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
                                            <path
                                                d="m24 32.35-9.65-9.65 2.15-2.15 6 6V8h3v18.55l6-6 2.15 2.15ZM8 40V29.85h3V37h26v-7.15h3V40Z" />
                                        </g>
                                    </svg></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    <?php }


}