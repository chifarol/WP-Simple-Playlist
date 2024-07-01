<?php
namespace Chifarol\WPPlaylist\Classes;

use Chifarol\WPPlaylist\Enums\ColorEnums;

class Settings
{
    public function __construct()
    {

    }

    static public $dbKey = "sp-settings";
    static public $mainColor = "#202020";
    static public $tertiaryColor = "#3e3e3e";
    static public $accentColor = "#7fd84b";
    static public $secondaryColor = "#000";
    static public $primaryColor = "#ffffff";

    static public function updateSettings(): void
    {
        $settings = [];
        $settings[ColorEnums::MAINCOLOR->value] = self::$mainColor;
        $settings[ColorEnums::TERTIARYCOLOR->value] = self::$tertiaryColor;
        $settings[ColorEnums::ACCENTCOLOR->value] = self::$accentColor;
        $settings[ColorEnums::SECONDARYCOLOR->value] = self::$secondaryColor;
        $settings[ColorEnums::PRIMARYCOLOR->value] = self::$primaryColor;

        update_option(self::$dbKey, $settings);
    }
    static public function getColorSettings(): array
    {
        $colors = [];
        $settings = get_option(self::$dbKey, []);

        $colors[ColorEnums::MAINCOLOR->value] = array_key_exists(ColorEnums::MAINCOLOR->value, $settings) ? esc_html($settings[ColorEnums::MAINCOLOR->value]) : self::$mainColor;
        $colors[ColorEnums::PRIMARYCOLOR->value] = array_key_exists(ColorEnums::PRIMARYCOLOR->value, $settings) ? esc_html($settings[ColorEnums::PRIMARYCOLOR->value]) : self::$primaryColor;
        $colors[ColorEnums::SECONDARYCOLOR->value] = array_key_exists(ColorEnums::SECONDARYCOLOR->value, $settings) ? esc_html($settings[ColorEnums::SECONDARYCOLOR->value]) : self::$secondaryColor;
        $colors[ColorEnums::TERTIARYCOLOR->value] = array_key_exists(ColorEnums::TERTIARYCOLOR->value, $settings) ? esc_html($settings[ColorEnums::TERTIARYCOLOR->value]) : self::$tertiaryColor;
        $colors[ColorEnums::ACCENTCOLOR->value] = array_key_exists(ColorEnums::ACCENTCOLOR->value, $settings) ? esc_html($settings[ColorEnums::ACCENTCOLOR->value]) : self::$accentColor;
        return $colors;
    }

}