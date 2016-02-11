<?php
/**
 * Plugin Name: Pubwich Social Media
 * Plugin URI: http://nsideas.com
 * Description: social media feed aggregator based on the Pubwich project.
 * Version: 0.0.1
 * Author: Michael Miller
 * Author URI: http://nsideas.com
 * License: GPLv2 or later
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
define('PUBWICH', 1);

require('app/core/jsonWich.php');

jsonWich::init();

// interface with pubwich to return formatted feed items
class wpWich extends jsonWich {
    public function getFeedItems($service_name) {
        $service_object = self::getActiveService($service_name);
        $service_object->init();
        $data_array = $service_object->getProcessedData();
        $processed_array = array();
        foreach ($data_array as $data_item){
            $processed_array[] = $service_object->processDataItem($data_item);
        }
        return $processed_array;
    }

    public function formatFeedItem($item_array, $name) {
        switch($name){
            case "twitter":
                $o = '<div class="'.$name.'">';
                $o .= '<img src="https://www.clevelandfoundation.org/wp-content/themes/tcf/assets/images/icon-sm-twitter.gif" >';
                $o .='<a href="'.$item_array['link'].'">';
                $o .= '<p>'.$item_array['status'].'</p></a>';
                $o .='</div>';
                return $o;
            break;
            case "facebook":
                $o = '<div class="'.$name.'">';
                $o .='<a href="'.$item_array['link'].'">';
                $o .= '<p>'.$item_array['status'].'</p></a>';
                $o .='</div>';
                return $o;
            break;
            case "youtube":
                $o = '<div class="'.$name.'">';
                $o .= '<figure>';
                $o .= '<img src="'.$item_array['media_thumbnail_url'].'">';
                $o .= '<a href="'.$item_array['link'].'">';
                $o .= '<figcaption>'.$item_array['title'].'</figcaption></a>';
                $o .= '</figure>';
                $o .='</div>';
                return $o;
            break;
            case "instagram":
                print_r($item_array);
            break;
            default:
        }
    }

}

function load_tweet($atts) {
    $item_atts = shortcode_atts(array('item_no' => 0), $atts);
    $wpWich = new wpWich();
    $item_arrays = $wpWich->getFeedItems('twitter_feed');
    return $wpWich->formatFeedItem($item_arrays[$item_atts['item_no']], 'twitter');
}

add_shortcode('twitter_item', 'load_tweet');

function load_facebook($atts) {
    $item_atts = shortcode_atts(array('item_no' => 0), $atts);
    $wpWich = new wpWich();
    $item_arrays = $wpWich->getFeedItems('facebook_page');
    return $wpWich->formatFeedItem($item_arrays[$item_atts['item_no']], 'facebook');
}

add_shortcode('fb_item', 'load_facebook');

function load_youtube($atts) {
    $item_atts = shortcode_atts(array('item_no' => 0), $atts);
    $wpWich = new wpWich();
    $item_arrays = $wpWich->getFeedItems('youtube_uploads');
    return $wpWich->formatFeedItem($item_arrays[$item_atts['item_no']], 'youtube');
}

add_shortcode('youtube_item', 'load_youtube');

