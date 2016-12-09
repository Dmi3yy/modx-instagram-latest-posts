<?php

/*
 *
 * Created by: Igor Sukhinin, Baltic Design Colors Ltd
 * License: GNU General Public License v3.0
 *
 */

/*
 * Variables
 * ---------
 * @var modX $modx
 *
 * Properties
 * ----------
 * @property    string     accountName
 * @property    integer    limit
 * @property    integer    showVideo
 * @property    string     imageQuality
 * @property    string     videoQuality
 *
 */

/*
 * Initialize essential properties
*/

// Set the Instagram account name
if (!isset($accountName)) {
    return 'Please set the Instagram account name!';
}

// Set the limit on the maximum number of items that will be displayed
$limit = (isset($limit)) ? $limit : 6;

// Do we need to show the video as well?
// Available options: 1 | 0
$showVideo = (isset($showVideo)) ? $showVideo : 0;

// Set the image quality
// Available options: low_resolution | thumbnail | standard_resolution
$imageQuality = (isset($imageQuality)) ? $imageQuality : 'low_resolution';

// Set the video quality
// Available options: low_resolution | standard_resolution | low_bandwidth
$videoQuality = (isset($videoQuality)) ? $videoQuality : 'low_resolution';

// Set the account URL
$accountUrl = 'https://www.instagram.com/' . $accountName;

// Set the JSON URL
$jsonUrl = $accountUrl . '/media/';

// Get the JSON content
$jsonData = file_get_contents($jsonUrl);

// Decode the JSON in object
$data = json_decode($jsonData);

// Prepare the variables
$resources = [];
$i = 0;
$output = '';
$items = '';

foreach ($data->items as $item) {
    // Check if we reached the limit of items
    if ($i == $limit) {
        // Stop the execution of this loop as we have already reached the limit
        break;
    }

    // Check if video is available and if it should be shown
    if (isset($item->videos) && $showVideo) {
        $resources[$i]['url'] = $item->videos->$videoQuality->url;
        $resources[$i]['type'] = 'video';
    } else {
        // Otherwise set the image preview
        $resources[$i]['url'] = $item->images->$imageQuality->url;
        $resources[$i]['type'] = 'image';
    }

    // Set the link to the corresponding post
    $resources[$i]['link'] = $item->link;

    $i++;
}

foreach ($resources as $resource) {
    // Get the inner content
    $items .= $modx->getChunk('Instagram-Inner', $resource);
}

// Get the outer content
$output = $modx->getChunk('Instagram-Outer', [
    'url'	=> $accountUrl,
    'items'	=> $items,
]);

return $output;