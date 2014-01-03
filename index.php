<?php

  include 'include/PasswdUtils.php';

  define("R_OK",                 0);
  define("R_FORM_NOT_OK",        1);
  define("R_EMPTY_FIELD",        2);
  define("R_PASSWORDS_MISMATCH", 3);
  define("R_WRONG_CRED",         4);
  define("R_WHILE_SAVING",       5);

  $username = $_POST["username"];
  $password = $_POST["password"];
  $new_password = $_POST["new_password"];
  $confirm_password = $_POST["confirm_password"];

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
        $result = R_PASSWORDS_MISMATCH;

      } else {
        $passwdFileAsArray = loadHtpasswd();
        if (!testHtpasswd($passwdFileAsArray, $username, $password)) {
          // Wrong username and passwords.
          $result = R_WRONG_CRED;

        } else {
          // User already exists in htpasswd file
          //$passwdFileAsArray[$username] = nonSaltedSha1($new_password);
          $passwdFileAsArray[$username] = cryptPasswd($new_password);
          if (!saveHtpasswd($passwdFileAsArray)) {
            $result = R_WHILE_SAVING;

          } else {
            $result = R_OK;
          }
        }
      }
    } else {
      // At least one field in the form is empty.
      $result = R_EMPTY_FIELD;
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Password manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <div style="float:none; margin: 10% 33%">
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h3 class="panel-title">Password manager</h3>
        </div>
        <div class="panel-body">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="form" class="form-horizontal" role="form">
            <div class="form-group">
              <label for="username" class="col-sm-2 control-label">Username</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= $username ?>">
              </div>
            </div>
            <div class="form-group">
              <label for="password" class="col-sm-2 control-label">Current</label>
              <div class="col-sm-10">
                <input type="password" class="form-control" id="password" name="password" placeholder="Current password">
              </div>
            </div>
            <div class="form-group">
              <label for="new_password" class="col-sm-2 control-label">New</label>
              <div class="col-sm-10">
                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New password">
              </div>
            </div>
            <div class="form-group">
              <label for="confirm_password" class="col-sm-2 control-label">Confirm</label>
              <div class="col-sm-10">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">Change</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <?php
        if (isset($result)) {
          switch ($result) {
            case R_OK                : $level = "success"; $mess = "New password successfully set"; break;
            case R_EMPTY_FIELD       : $level = "warning"; $mess = "All fields of the form must be filled"; break;
            case R_PASSWORDS_MISMATCH: $level = "warning"; $mess = "Typed passwords don't match"; break;
            case R_WRONG_CRED        : $level = "warning"; $mess = "Wrong username and passwords"; break;
            case R_WHILE_SAVING      : $level = "danger";  $mess = "An error occurred while saving the new password. Please contact support"; break;
          }
          echo "<div class='alert alert-$level'>$mess</div>";
        }
      ?>
      </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
