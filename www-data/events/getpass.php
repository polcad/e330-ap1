<?PHP
  $userid = "";
  $password = "";
  session_unset();
  session_destroy();
  $_SESSION = array();
  if (isset($_GET['userid']))   $userid   = $_GET['userid'];
  if (isset($_GET['password'])) $password = $_GET['password'];
//
// Delete from here to END-DEFAULT-PASSWORDS
// to remove default passwords
// 
// default userid/password  is admin/admin
// actually any userid and password that are the same will allow login
// 
  if ( ($userid == "admin") && ($password == "admin") ) {
    session_start();
    $_SESSION['Login']  = "all";
    echo "1"; 
    exit();
  }
  if ($userid == $password) {
    session_start();
    $_SESSION['Login']  = $userid;
    echo "1"; 
    exit();
  }
//
// END-DEFAULT-PASSWORDS
//

//Uncomment this section and build a user table
//with userid and passwords to implement this functionality

/*
  include("includes/db_connect.php");
  $ut = "users";
  $sql  = "SELECT * FROM $ut where ";
  $sql .= "userid = '".$userid."' and password = '".$password."' ";
  $sql .= "and state  = 'y' ";
  $result = mysql_query($sql, $mylink);
  $num_rows = mysql_num_rows($result);
  if ($num_rows > 0) {
    $list = mysql_fetch_assoc($result);
    session_start();
    $_SESSION['UserID']   = $userid;
    $_SESSION['Password'] = $password;
    $_SESSION['Level']    = $list['level'];
  }
  mysql_free_result($result);
  mysql_close($mylink);
  echo $num_rows; 
*/

  exit();
?>
