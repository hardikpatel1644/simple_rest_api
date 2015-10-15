# simple_rest_api
Simple PHP Rest API

# Database table Used : 

  CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fullname` varchar(25) NOT NULL,
  `user_email` varchar(50) NOT NULL,
  `user_password` varchar(50) NOT NULL,
  `user_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

# How to Use :

1. Install any rest client in browser ( Eg. Postman in chrome)
2. Url : hostname/api.php?request=loing
3. Use Proper method like : POST, GET , DELETE etc.
4. Pass form Data or parameter as per request.
  