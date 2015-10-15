<?php

/*
  This is an example class script proceeding secured API
  To use this class you should keep same as query string and function name
  Ex: If the query string value rquest=delete_user Access modifiers doesn't matter but function should be
  function delete_user(){
  You code goes here
  }
  Class will execute the function dynamically;

  usage :

  $object->response(output_data, status_code);
  $object->_request	- to get santinized input

  output_data : JSON (I am using)
  status_code : Send status message for headers

  Add This extension for localhost checking :
  Chrome Extension : Advanced REST client Application
  URL : https://chrome.google.com/webstore/detail/hgmloofddffdnphfgcellkdfbfbjeloo

  I used the below table for demo purpose.

  CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fullname` varchar(25) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 */

require_once("Rest.inc.php");

class API extends REST
{

    public $data = "";

    const DB_SERVER = "localhost";
    const DB_USER = "root";
    const DB_PASSWORD = "";
    const DB = "test";

    private $obDb = NULL;

    public function __construct()
    {
        parent::__construct();    // Init parent contructor
        $this->dbConnect();     // Initiate Database connection
    }

    /*
     *  Database connection 
     */

    private function dbConnect()
    {
        $this->obDb = mysqli_connect(self::DB_SERVER, self::DB_USER, self::DB_PASSWORD);
        if ($this->obDb)
            mysqli_select_db($this->obDb, self::DB);
    }

    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */

    public function processApi()
    {
        $func = strtolower(trim(str_replace("/", "", $_REQUEST['request'])));
        if ((int) method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->response('', 404);    // If the method not exist with in this class, response would be "Page not found".
    }

    /*
     * 	Simple login API
     *  Login must be POST method
     *  email : <USER EMAIL>
     *  pwd : <USER PASSWORD>
     */

    private function login()
    {
        // Cross validation if the request method is POST else it will return "Not Acceptable" status
        if ($this->get_request_method() != "POST")
        {
            $this->response('', 406);
        }

        $ssEmail = $this->_request['email'];
        $ssPassword = $this->_request['pwd'];

        // Input validations
        if (!empty($ssEmail) and ! empty($ssPassword))
        {
            if (filter_var($ssEmail, FILTER_VALIDATE_EMAIL))
            {
                $ssSql = mysqli_query($this->obDb, "SELECT user_id, user_fullname, user_email FROM users WHERE user_email = '$ssEmail' AND user_password = '" . md5($ssPassword) . "' LIMIT 1");


                if (mysqli_num_rows($ssSql) > 0)
                {
                    $asResult = mysqli_fetch_array($ssSql, MYSQL_ASSOC);

                    // If success everythig is good send header as "OK" and user details
                    $this->response($this->json($asResult), 200);
                }
                $this->response('', 204); // If no records "No Content" status
            }
        }

        // If invalid inputs "Bad Request" status message and reason
        $error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
        $this->response($this->json($error), 400);
    }

    private function users()
    {
        // Cross validation if the request method is GET else it will return "Not Acceptable" status
        if ($this->get_request_method() != "GET")
        {
            $this->response('', 406);
        }
        $ssSql = mysqli_query($this->obDb, "SELECT user_id, user_fullname, user_email FROM users WHERE user_status = 1");
        if (mysqli_num_rows($ssSql) > 0)
        {
            $asResult = array();
            while ($rlt = mysqli_fetch_array($ssSql, MYSQL_ASSOC))
            {
                $asResult[] = $rlt;
            }
            // If success everythig is good send header as "OK" and return list of users in JSON format
            $this->response($this->json($asResult), 200);
        }
        $this->response('', 204); // If no records "No Content" status
    }

    private function deleteUser()
    {
        // Cross validation if the request method is DELETE else it will return "Not Acceptable" status
        if ($this->get_request_method() != "DELETE")
        {
            $this->response('', 406);
        }
        $snId = (int) $this->_request['id'];
        if ($snId > 0)
        {
            mysqli_query($this->obDb, "DELETE FROM users WHERE user_id = $snId");
            $asSuccess = array('status' => "Success", "msg" => "Successfully one record deleted.");
            $this->response($this->json($asSuccess), 200);
        }
        else
            $this->response('', 204); // If no records "No Content" status
    }

    /*
     * 	Encode array into JSON
     */

    private function json($asData)
    {
        if (is_array($asData))
        {
            return json_encode($asData);
        }
    }

}

// Initiiate Library

$obApi = new API();
$obApi->processApi();
?>