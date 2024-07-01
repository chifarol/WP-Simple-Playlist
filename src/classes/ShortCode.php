<?php


namespace Chifarol\WPPlaylist\Classes;

use Chifarol\WPPlaylist\Enums\ColorEnums;

class ShortCode
{
    public function __construct()
    {

    }
    /**
     * Register playlist's shortcode.
     * @since 1.0.0
     */
    public function register_shortcodes()
    {
        add_shortcode('sp_playlist', [$this, 'output']);
    }

    /**
     * Render playlist shortcode on frontend.
     *
     * @since 1.0.0
     */
    public function output($attr)
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
                    <audio src="" id="cp-audio" preload="auto"></audio>';
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
                                    <div class="cp-loading">
                                    <div class="cp-loading-circle"></div>
                                    </div>
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
}