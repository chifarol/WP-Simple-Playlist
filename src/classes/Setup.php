<?php

namespace Chifarol\WPPlaylist\Classes;


class Setup
{
    public function __construct(private SimplePlaylist $sp)
    {

    }

    public function setup()
    {
        add_action('plugins_loaded', [$this->sp, 'plugin_setup']);
        register_activation_hook(__FILE__, array($this->sp, 'plugin_activated'));
        register_deactivation_hook(__FILE__, array($this->sp, 'plugin_deactivated'));
    }

    /**
     * Actions to run on plugin activation.
     *
     * @since 1.0.0
     */
    protected function plugin_activated()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        Settings::updateSettings();

        do_action('simpleplaylist_activated');

        flush_rewrite_rules();
    }
    /**
     * Actions to run on plugin de-activation.
     *
     * @since 1.0.0
     */
    protected function plugin_deactivated()
    {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        unregister_post_type(RegisterPost::getPostType());
        delete_option(Settings::$dbKey);
        do_action('simpleplaylist_deactivated');

        flush_rewrite_rules();
    }
}
