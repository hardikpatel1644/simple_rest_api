<?php

/* File : Rest.inc.php
 * Author : Hardik Patel
 */

/**
 * Class for setup Rest API
 */
class REST
{

    public $_allow = array();
    public $_content_type = "application/json";
    public $_request = array();
    private $_method = "";
    private $_code = 200;

    /**
     * Default constructer
     */
    public function __construct()
    {
        $this->inputs();
    }

    /**
     * Function to get referer
     * @return type
     */
    public function get_referer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    /**
     * Function to set Header ans generate response
     * @param array $asData
     * @param int $ssStatus
     */
    public function response($asData, $ssStatus)
    {
        $this->_code = ($ssStatus) ? $ssStatus : 200;
        $this->set_headers();
        echo $asData;
        exit;
    }

    /**
     * Function to get status message
     * @return string
     */
    private function get_status_message()
    {
        $asStatus = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported');
        return ($asStatus[$this->_code]) ? $asStatus[$this->_code] : $asStatus[500];
    }

    /**
     * Function to get request method
     * @return string
     */
    public function get_request_method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     *  Function to set input based on request method
     */
    private function inputs()
    {

        switch ($this->get_request_method())
        {
            case "POST":
                $this->_request = $this->cleanInputs($_POST);
                break;
            case "GET":
            case "DELETE":
                $this->_request = $this->cleanInputs($_GET);
                break;
            case "PUT":
                parse_str(file_get_contents("php://input"), $this->_request);
                $this->_request = $this->cleanInputs($this->_request);
                break;
            default:
                $this->response('', 406);
                break;
        }
    }

    /**
     * Function to clean inputs
     * @param array $asData
     * @return array
     */
    private function cleanInputs($asData)
    {
        $asCleanInput = array();
        if (is_array($asData))
        {
            foreach ($asData as $k => $v)
            {
                $asCleanInput[$k] = $this->cleanInputs($v);
            }
        }
        else
        {
            if (get_magic_quotes_gpc())
            {
                $asData = trim(stripslashes($asData));
            }
            $asData = strip_tags($asData);
            $asCleanInput = trim($asData);
        }
        return $asCleanInput;
    }

    /**
     * Function to set Headers 
     */
    private function set_headers()
    {
        header("HTTP/1.1 " . $this->_code . " " . $this->get_status_message());
        header("Content-Type:" . $this->_content_type);
    }

}

?>