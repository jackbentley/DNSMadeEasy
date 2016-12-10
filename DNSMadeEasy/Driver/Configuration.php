<?php

namespace DNSMadeEasy\Driver;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Configuration
 * Holds the configuration for the driver.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Configuration
{
    /**
     * The API key.
     * @var string
     */
    private $_apiKey;

    /**
     * The secret key.
     * @var string
     */
    private $_secretKey;

    /**
     * The version
     * @var string
     */
    private $_version = 'V2.0';

    /**
     * The sandbox URL.
     * @var string
     */
    private $_sandboxURL = "https://api.sandbox.dnsmadeeasy.com/";

    /**
     * The production URL.
     * @var string
     */
    private $_apiURL = "https://api.dnsmadeeasy.com/";

    /**
     * Whether the sandbox should be used or not.
     * @var boolean
     */
    private $_useSandbox;

    /**
     * Whether debug mode is on or not.
     * @var boolean
     */
    private $_debug = false;

    /**
     * Construct the configuration object.
     * @param string $apiKey The api key.
     * @param string $secretKey The secret key.
     * @param boolean $useSandbox Whether to use the sandbox or not.
     */
    public function __construct($apiKey, $secretKey, $useSandbox = false)
    {
        $this->_apiKey = $apiKey;
        $this->_secretKey = $secretKey;
        $this->_useSandbox = $useSandbox;
    }

    /**
     * Get the API key.
     * @return string
     */
    public function getAPIKey()
    {
        return $this->_apiKey;
    }

    /**
     * Get the secret key.
     * @return string
     */
    public function getSecretKey()
    {
        return $this->_secretKey;
    }

    /**
     * Get the base URL to be used for the request.
     * @return string
     */
    public function getURL()
    {
        if ($this->_useSandbox) {
            return $this->_sandboxURL . $this->_version;
        } else {
            return $this->_apiURL . $this->_version;
        }
    }

    /**
     * Sets whether the sandbox should be used.
     * @param boolean $value Whether the sandbox should be used.
     */
    public function useSandbox($value)
    {
        $this->_useSandbox = (boolean)$value;
    }

    /**
     * Tells us whether the sandbox is being used or not.
     * @return boolean
     */
    public function usingSandbox()
    {
        return $this->_useSandbox;
    }

    /**
     * Sets whether debug mode is on or off.
     * @param boolean $value Whether debug mode is on or off.
     */
    public function debug($value)
    {
        $this->_debug = (boolean)$value;
    }

    /**
     * Gets whether debug mode is on or off.
     * @return boolean
     */
    public function getDebug()
    {
        return $this->_debug;
    }
}
