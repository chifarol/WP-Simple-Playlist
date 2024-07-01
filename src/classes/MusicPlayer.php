<?php


namespace Chifarol\WPPlaylist\Classes;

use Chifarol\WPPlaylist\Enums\ColorEnums;

class MusicPlayer
{

    public function __construct(private Settings $settings)
    {

    }
    public function getCSSStyles()
    {
        $colors = $this->settings->getColorSettings();
        return '
        .cp-container {
        background: ' . $colors[ColorEnums::MAINCOLOR->value] . ';
        color:' . $colors[ColorEnums::PRIMARYCOLOR->value] . ';
        }
        #cp-polygon {
            background-color:' . $colors[ColorEnums::MAINCOLOR->value] . ';
        }
        .cp-track {
            background: ' . $colors[ColorEnums::TERTIARYCOLOR->value] . ';
        }
        .cp-pause-duration {
            background: ' . $colors[ColorEnums::ACCENTCOLOR->value] . ';
        }
        .cp-pause-play svg {
            fill: ' . $colors[ColorEnums::ACCENTCOLOR->value] . ';
        }
        .cp-track.cp-selected {
            box-shadow: 0px 0px 18px 0px ' . $colors[ColorEnums::SECONDARYCOLOR->value] . ';
            color: ' . $colors[ColorEnums::ACCENTCOLOR->value] . ';
        }
        #cp-play-options svg {
            transform: scale(0.4);
            fill: ' . $colors[ColorEnums::ACCENTCOLOR->value] . ';
        }

        #cp-play-options .gray {
            fill: ' . $colors[ColorEnums::TERTIARYCOLOR->value] . ';
        }
        .cp-end svg {
            fill: ' . $colors[ColorEnums::PRIMARYCOLOR->value] . ';
        }
        ';
    }
}