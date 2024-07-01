<?php

/**
 * Plugin Name: Simple Playlists
 * Description: A simple playlist plugin.
 * Version: 1.0.0
 * Requires at least: 5.3
 * Requires PHP: 8.0
 * Author: Ilodigwe Chinaza
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-playlist
 * Domain Path: /languages
 */

//plugin license (primary file)
/*
 Copyright (C) 2022 Chifarol
This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require 'vendor/autoload.php';

use Chifarol\WPPlaylist\Classes\Settings;
use Chifarol\WPPlaylist\Classes\SettingsPage;
use Chifarol\WPPlaylist\Classes\Setup;
use Chifarol\WPPlaylist\Classes\Assets;
use Chifarol\WPPlaylist\Classes\MetaBoxes;
use Chifarol\WPPlaylist\Classes\MusicPlayer;
use Chifarol\WPPlaylist\Classes\RegisterPost;
use Chifarol\WPPlaylist\Classes\ShortCode;
use Chifarol\WPPlaylist\Classes\SimplePlaylist;

$settings = new Settings();
$settingsPage = new SettingsPage($settings);
$musicPlayer = new MusicPlayer($settings);
$sssets = new Assets($musicPlayer);
$registerPost = new RegisterPost();
$metaBoxes = new MetaBoxes($registerPost);
$shortCode = new ShortCode();
$simplePlaylist = new SimplePlaylist($metaBoxes, $sssets, $registerPost, $settingsPage, $shortCode, );

(new Setup($simplePlaylist))->setup();
