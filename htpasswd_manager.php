<?php

  include 'include/PasswdUtils.php';

  /*
   * ERROR CODES
   * 1: Form not sent correctly.
   * 2: At least one field on the form is empty.
   * 3: Passwords don't match.
   * 4: Wrong username and password.
   * 5: Error while saving new password on file.
   * */

  $username = $_POST["username"];
  $password = $_POST["password"];
  $new_password = $_POST["new_password"];
  $confirm_password = $_POST["confirm_password"];
  $error = $_POST["error"];

  /*
   * Checking that form has been send correctly
   * */
  if($_POST) {

    /*
     * Checking that no form's field is empty
     * */
    if( $username != "" && $password != "" && $new_password != "" && $confirm_password != "") {
      if(strcmp($new_password, $confirm_password) != 0) {
        // Typed passwords don't match
        $error = 3;

      } else {
        $passwdFileAsArray = loadHtpasswd();
        if (!testHtpasswd($passwdFileAsArray, $username, $password)) {
          // Wrong username and passwords.
          $error = 4;

        } else {
          // User already exists in htpasswd file
          //$passwdFileAsArray[$username] = nonSaltedSha1($new_password);
          $passwdFileAsArray[$username] = cryptPasswd($new_password);
          if (!saveHtpasswd($passwdFileAsArray)) {
            $error = 5;

          } else {
            $error = 0;
          }
        }
      }
    } else {
      // At least one field in the form is empty.
      $error = 2;
    }
  }
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8 " />
    <title>Password Manager</title>
    <link rel="stylesheet" href="style.css" type="text/css">
  </head>
  <body>
    <!-- <img src="img/logo.gif" alt="logo" title="logo"/> -->

    <h1>Password Manager</h1>

    <?php

      if(isset($error) && $error == 0) {
        $PASSWORD_CHANGE_OK = "<img class=\"emblem\" src=\"img/ok.png\"><b>New password has been set successfully";
        echo "<div>".$PASSWORD_CHANGE_OK."</div>";
      }

      if(isset($error) && $error == 2) {
        $FORM_NOT_COMPLETE_ERROR = "<img class=\"emblem\" src=\"img/error.png\"><b>Error!</b> All fields in the form must be filled.";
        echo "<div>".$FORM_NOT_COMPLETE_ERROR."</div>";
      }

      if(isset($error) && $error == 3) {
        $PASSWORD_MATCH_ERROR = "<img class=\"emblem\" src=\"img/error.png\"><b>Error!</b> Typed passwords don't match!";
        echo "<div>".$PASSWORD_MATCH_ERROR."</div>";
      }

      if(isset($error) && $error == 4) {
        $WRONG_USER_PASSWORD_ERROR = "<img class=\"emblem\" src=\"img/error.png\"><b>Error!</b> Wrong username and passwords!";
        echo "<div>".$WRONG_USER_PASSWORD_ERROR."</div>";
      }

      if(isset($error) && $error == 5) {
        $FILE_WRITING_ERROR = "<img class=\"emblem\" src=\"img/error.png\">An error occurred while saving the new password.<br>Please contact support.";
        echo "<div>".$FILE_WRITING_ERROR."</div>";
      }

    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="form">
      <table width="400" border="0">
        <tr>
          <td>
            <label>Username:</label>
          </td>
          <td>
            <input type="text" id="username" name="username">
          </td>
        </tr>
        <tr>
          <td>
            <label>Password:</label>
          </td>
          <td>
            <input type="password" id="password" name="password">
          </td>
        </tr>
        <tr>
          <td>
            <label>New password:</label>
          </td>
          <td>
            <input type="password" id="new_password" name="new_password">
          </td>
        </tr>
        <tr>
          <td>
            <label>Confirm new password:</label>
          </td>
          <td>
            <input type="password" id="confirm_password" name="confirm_password">
          </td>
        </tr>
        <tr>
          <td colspan="2">
            <input type="submit" id="submit" value="Change Password">
          </td>
        </tr>
      </table>
    </form>
  </body>
</html>
