<?php

  // TODO put the htpassword from htpasswd_manager path in conf
  define("HTPASSWDFILE", "/etc/apache2/htpasswd_users");

  // Loads the htpasswd file in an associative array
  // Array( username => crypted_pass, ... )
  function loadHtpasswd() {
    if ( !file_exists(HTPASSWDFILE))  {
      die("Error while reading the htpasswd file.");
    }

    $res = Array();
    foreach(file(HTPASSWDFILE) as $l) {
      $array = explode(':',$l);
      $user = $array[0];
      $pass = chop($array[1]);
      $res[$user] = $pass;
    }
    return $res;
  }

  // Used for htpasswd authentication: returns true if the user exists and passwords match, false otherwise.
  function testHtpasswd( $pass_array, $user, $pass ) {
    if ( !isset($pass_array[$user]))
      return False;

    $crypted = $pass_array[$user];

    // Determine the password type
    if ( substr($crypted, 0, 5) == "{SHA}" )
      return (nonSaltedSha1($pass) == $crypted);
    else {
     return crypt( $pass, substr($crypted,0,CRYPT_SALT_LENGTH) ) == $crypted;
    }
  }

  // Generates a password SHA1 not-salted hash.
  function nonSaltedSha1( $pass ) {
    return "{SHA}" . base64_encode(pack("H*", sha1($pass)));
  }

  // Generates a password using SHA-512
  function cryptPasswd( $pass ) {
    return crypt( $pass );
  }

  // Saves the array on the file
  // Returns true in case of success, false otherwise.
  function saveHtpasswd( $passArray ) {
    $result = true;

    ignore_user_abort(true);

    $file = fopen(HTPASSWDFILE, "w+");
    // Obtaining reading exclusive lock
    if (flock($file, LOCK_EX)) {
      while( list($u,$p) = each($passArray))
        fputs($file, "$u:$p\n");

      // Lock release.
      flock($file, LOCK_UN);

    } else {
      $result = false;
    }
    fclose($file);
    ignore_user_abort(false);
    return $result;
  }

?>
