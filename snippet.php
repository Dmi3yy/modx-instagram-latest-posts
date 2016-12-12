<?php

/*
 *
 * @author Igor Sukhinin <suhinin@gmail.com>, Baltic Design Colors Ltd
 * @license: https://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * @version: 1.1.1
 *
 * Variables
 * ---------
 * @var modX $modx
 *
 * Properties
 * ----------
 * @property    string      $accountName    Instagram account name; required
 * @property    integer     $limit          Limit on the maximum number of items that will be displayed
 * @property    integer     $showVideo      Show the video if it's available (options: 1 | 0)
 * @property    string      $imageQuality   Image quality (options: low_resolution | thumbnail | standard_resolution)
 * @property    string      $videoQuality   Video quality (options: low_resolution | standard_resolution | low_bandwidth)
 * @property    string      $innerTpl       Inner chunk name
 * @property    string      $outerTpl       Outer chunk name
 * @property    string      $errorTpl       Error chunk name
 *
 */


class InstagramLatestPosts
{
    protected $accountName;
    protected $limit;
    protected $showVideo;
    protected $imageQuality;
    protected $videoQuality;
    protected $innerTpl;
    protected $outerTpl;
    protected $errorTpl;
    protected $modx;
    protected $serverMethod;
    protected $accountUrl;
    protected $output;
    protected $error;

    /**
     * InstagramLatestPosts constructor.
     *
     * @param array $config Array of properties
     */
    public function __construct($config = [])
    {
        // Initialize the essential properties
        $this->modx         = $config['modx'];
        $this->accountName  = $config['accountName'];
        $this->limit        = $config['limit'];
        $this->showVideo    = $config['showVideo'];
        $this->imageQuality = $config['imageQuality'];
        $this->videoQuality = $config['videoQuality'];
        $this->innerTpl     = $config['innerTpl'];
        $this->outerTpl     = $config['outerTpl'];
        $this->errorTpl     = $config['errorTpl'];
    }

    /**
     * Runs the data processing
     *
     * @return boolean $result The result of data processing
     */
    public function run()
    {
        // Check if the Instagram account name is not set
        if ($this->accountName == '') {
            $this->error = 'Instagram account name is required. Please set this property in your snippet call.';
            return false;
        }

        // Get the available server method to download the remote content
        $this->serverMethod = $this->getServerMethod();

        // Check if no server method is available
        if ($this->serverMethod === null) {
            $this->error = 'Please enable allow_url_fopen or cURL on your web server.';
            return false;
        }

        // Set the account URL
        $this->accountUrl   = 'https://www.instagram.com/' . $this->accountName;

        // Set the JSON URL
        $jsonUrl = $this->accountUrl . '/media/';

        // Get the JSON content
        $json = $this->getJsonContent($jsonUrl);

        // Check if loading of JSON content failed
        if ($json === false) {
            $this->error = 'The remote loading of JSON content failed. Please check if your account name is correct.';
            return false;
        }

        // Parse JSON in an object
        $data = $this->parseJson($json);

        // Check if JSON parsing failed
        if ($data === null) {
            $this->error = 'The JSON parsing failed.';
            return false;
        }

        // Get JSON data in an object containing resources
        $resources = $this->getResources($data);

        // Check if there is no any resource
        if (count($resources) == 0) {
            $this->error = 'There are no posts yet in the profile: ' . $this->accountName;
            return false;
        }

        // Set the output
        $this->output = $this->setOutput($resources);

        return true;
    }

    /**
     * Gets the output
     *
     * @return string $output HTML output
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Gets the error (if there was any)
     *
     * @return string Error content
     */
    public function getError()
    {
        // Check if there were no any error
        if (!isset($this->error)) {
            return '';
        }

        // Get the error content
        return $this->modx->getChunk($this->errorTpl, ['error' => $this->error]);
    }


    /**
     * Gets the available server method which allows to download the remote content
     *
     * @return mixed string | null $serverMethod The name of server method or null if both allow_url_fopen and cURL are disabled
     */
    protected function getServerMethod()
    {
        // Check if file_get_contents is enabled
        if (ini_get('allow_url_fopen')) {
            $serverMethod = 'fopen';
        } // Check if cURL is enabled
        elseif (function_exists('curl_version')) {
            $serverMethod = 'curl';
        }

        return (isset($serverMethod)) ? $serverMethod : null;
    }

    /**
     * Gets the JSON content
     *
     * @param string $url JSON url
     * @return mixed string | false $json JSON content or false if there was some error while downloading the remote content
     */
    protected function getJsonContent($url = '')
    {
        $json = null;

        // Check if file_get_contents is enabled
        if ($this->serverMethod == 'fopen') {
            // Get the JSON content using fopen
            $json = $this->loadFileOpen($url);
        } // Check if cURL is enabled
        elseif ($this->serverMethod == 'curl') {
            // Try to get the JSON content using cURL
            $json = $this->loadCurl($url);
        }

        return $json;
    }

    /**
     * Parses the JSON string in an object
     *
     * @param string $json JSON content
     * @return mixed stdClass | null $data Object containing JSON data or null if the JSON processing failed
     */
    protected function parseJson($json = '')
    {
        // Decode JSON an object
        return @json_decode($json);
    }

    /**
     * Loads the remote content using file_get_contents()
     *
     * @param string $url
     * @return mixed string | boolean
     */
    protected function loadFileOpen($url = '')
    {
        return file_get_contents($url);
    }

    /**
     * Loads the remote content using Client URL Library (cURL)
     *
     * @param string $url
     * @return mixed
     */
    protected function loadCurl($url = '')
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

    /**
     * Gets the resources
     *
     * @param stdClass $data Object containing JSON data
     * @return array $resources Prepared array of resources
     */
    protected function getResources(stdClass $data)
    {
        // Prepare the variables
        $resources = [];
        $i = 0;
        $videoQuality = $this->videoQuality;
        $imageQuality = $this->imageQuality;

        foreach ($data->items as $item) {
            // Check if we reached the limit of items
            if ($i == $this->limit) {
                // Stop the execution of this loop as we have already reached the limit
                break;
            }

            // Check if video is available and if it should be shown
            if (isset($item->videos) && $this->showVideo) {
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

        return $resources;
    }


    /**
     * Sets the output
     *
     * @param array $resources
     * @return string $output
     */
    protected function setOutput($resources = [])
    {
        $items = '';

        foreach ($resources as $resource) {
            // Get the inner content
            $items .= $this->modx->getChunk($this->innerTpl, $resource);
        }

        // Get the outer content
        $output = $this->modx->getChunk($this->outerTpl, [
            'accountUrl'    => $this->accountUrl,
            'items'         => $items,
        ]);

        return $output;
    }

}

// Create config array
$config = [
    'modx'          => $modx,
    'accountName'   => $modx->getOption('accountName', $scriptProperties, '', true),
    'limit'         => $modx->getOption('limit', $scriptProperties, 6, true),
    'showVideo'     => $modx->getOption('showVideo', $scriptProperties, 0, true),
    'imageQuality'  => $modx->getOption('imageQuality', $scriptProperties, 'low_resolution', true),
    'videoQuality'  => $modx->getOption('videoQuality', $scriptProperties, 'low_resolution', true),
    'innerTpl'      => $modx->getOption('innerTpl', $scriptProperties, 'Instagram-Inner', true),
    'outerTpl'      => $modx->getOption('outerTpl', $scriptProperties, 'Instagram-Outer', true),
    'errorTpl'      => $modx->getOption('errorTpl', $scriptProperties, 'Instagram-Error', true),
];

// Create a new InstagramLatestPosts class instance
$inst = new InstagramLatestPosts($config);

// Run the data processing
if ($inst->run()) {
    // Get the output if processing was successfull
    $output = $inst->getOutput();
} else {
    // Get the error explaining the issue
    $output = $inst->getError();
}

return $output;