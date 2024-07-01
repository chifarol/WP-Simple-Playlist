<?php


namespace Chifarol\WPPlaylist\Classes;



class MetaBoxes
{
    public function __construct(private RegisterPost $registerPost)
    {

    }

    /**
     * Register all metaboxes for post type.
     *
     * @since 1.0.0
     */
    public function register_metaboxes()
    {
        add_meta_box('sp-track-meta-box', esc_html__('Tracks', 'simple-playlist'), [$this, 'tracks_metabox_output'], $this->registerPost->getPostType(), 'advanced', 'high');
        add_meta_box('sp-shortcode_text-meta-box', esc_html__('Shortcode', 'simple-playlist'), [$this, 'shortcode_text_metabox_output'], $this->registerPost->getPostType(), 'side', 'low');
    }
    /**
     * Render metabox contents.
     *
     * @since 1.0.0
     */
    public function tracks_metabox_output($post)
    {
        $tracks = get_post_meta($post->ID, 'sp-tracks');
        if ($tracks) {
            ?>
            <div class='sp-track-input-form'>
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
                            ?>
                            <fieldset data-key='<?php echo absint($key) ?>'>
                                <div class="sp-toggle">
                                    <h4></h4><span> &#9650;</span>
                                </div>
                                <div class="sp-toggle-target">

                                    <input type='text' placeholder='<?php esc_attr_e('Title', 'simple-playlist') ?>'
                                        class='sp-track-title' name='sp-tracks[<?php echo $key ?>][title] ?>]'
                                        value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track['title']) ?>' required />

                                    <input type='text' class='sp-track-artiste' placeholder='<?php esc_attr_e('Arti
                                ste(s)', 'simple-playlist') ?>' name='sp-tracks[<?php echo $key ?>][artiste]'
                                        value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track['artiste']) ?>' required />

                                    <div class='sp-input-music-upload-container'>
                                        <input type='url' class='sp-track-url'
                                            placeholder='<?php esc_attr_e('Song URL', 'simple-playlist') ?>'
                                            name='sp-tracks[<?php echo $key ?>][url]'
                                            value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track['url']) ?>' required />
                                        <button class="sp-upload">Upload</button>
                                    </div>

                                    <div class='sp-input-music-upload-container'>
                                        <input type='url' class='sp-track-pic'
                                            placeholder='<?php esc_attr_e('Cover Image', 'simple-playlist') ?>'
                                            name='sp-tracks[<?php echo $key ?>][pic]'
                                            value='<?php printf(esc_attr__('%s', 'simple-playlist'), $track_pic) ?>' />
                                        <button class="sp-upload-pic"><?php esc_html_e('Upload', 'simple-playlist') ?></button>
                                    </div>
                                    <input type="button" class="sp-remove-track secondary"
                                        value="<?php esc_attr_e('Remove Track', 'simple-playlist') ?>">
                                </div>
                            </fieldset>

                            <?php
                        }
                        ?>

                    </div>
                </form>
                <button class="sp-add-track secondary"><?php esc_html_e('Add Track', 'simple-playlist') ?></button>
                <button class="sp-clear-playlist secondary"><?php esc_html_e('Clear Playlist', 'simple-playlist') ?></button><button
                    class="sp-toggle-playlist"><?php esc_html_e('Toggle All', 'simple-playlist') ?></button>
            </div>
        <?php } else {
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
                                <input type='text' placeholder='<?php esc_attr_e('Title', 'simple-playlist') ?>'
                                    name='sp-tracks[1][title]' class='sp-track-title' required />
                                <input type='text' placeholder='<?php esc_attr_e('Artiste(s)', 'simple-playlist') ?>'
                                    name='sp-tracks[1][artiste]' class='sp-track-artiste' required />
                                <div class='sp-input-music-upload-container'>
                                    <input type='url' placeholder='<?php esc_attr_e('Song URL', 'simple-playlist') ?>'
                                        name='sp-tracks[1][url]' class='sp-track-url' required />
                                    <button class="sp-upload"><?php esc_html_e('Upload', 'simple-playlist') ?></button>
                                </div>
                                <div class='sp-input-music-upload-container'>
                                    <input type='url' placeholder='<?php esc_attr_e('Cover Image', 'simple-playlist') ?>'
                                        name='sp-tracks[1][pic]' class='sp-track-pic' />
                                    <button class="sp-upload-pic"><?php esc_html_e('Upload', 'simple-playlist') ?></button>
                                </div>
                                <input type="button" class="sp-remove-track secondary"
                                    value="<?php esc_attr_e('Remove Track', 'simple-playlist') ?>">

                            </div>
                        </fieldset>
                    </div>
                </form>
                <button class="sp-add-track secondary"><?php esc_html_e('Add Track', 'simple-playlist') ?></button>
                <button class="sp-clear-playlist secondary"><?php esc_html_e('Clear Playlist', 'simple-playlist') ?></button><button
                    class="sp-toggle-playlist"><?php esc_html_e('Toggle All', 'simple-playlist') ?></button>
            </div>
            <?php

        }
    }
    /**
     * Save metabox fields to post's meta.
     *
     * @since 1.0.0
     */
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
        echo '[' . $this->registerPost->getPostType() . ' id=\'' . $post->ID . '\']';
    }

}