<?php
/*
 |------------------------------------------------------------------------------
 | CakeFTP\Controller\Component: Ftp component
 |------------------------------------------------------------------------------
 */

namespace CakeFtp\Controller\Component;

use Cake\Controller\Component;

class FtpComponent extends Component {
  
  protected $_host = null;
  
  protected $_port = 21;
  
  protected $_timeout = '90';
  
  protected $_username = null;
  
  protected $_password = null;
  
  protected $_stream = null;
  
  protected $_mode = FTP_ASCII;
  
  // TODO: guessMode () {} (http://www.streetsie.com/ftp-ascii-list/)
  
  public function mode ($mode = null) {
    
  }
  
  public function alloc ($filesize, &$result = '') {
    // NOTE: Many FTP servers do not support this command. It may be best to 
    // reserve this function for servers which explicitly require preallocation
    $stream = $this->connect();
    return ftp_alloc($stream, $filesize, $result);
  }
  
  public function cdup () {
    $stream = $this->connect();
    return ftp_cdup($stream);
  }
  
  public function chdir ($directory) {
    $stream = $this->connect();
    return ftp_chdir($stream, $directory);
  }
  
  public function chmod ($mode, $filename) {
    $stream = $this->connect();
    return ftp_chmod($stream, $directory);
  }
  
  public function close () {
    $stream = $this->connect();
    $result = ftp_close($stream);
    $this->_stream = null;
    return $result;
  }
  
  public function connect ($host = null, $port = null, $timeout = null) {
    if ($this->_stream) {
      return $this->_stream;
    }
    if ($host == null) {
      $host = $this->_host;
    }
    if ($port == null) {
      $port = $this->_port;
    }
    if ($timeout == null) {
      $timeout = $this->_timeout;
    }
    $stream = ftp_connect($host, $port, $timeout);
    return $this->_stream = $stream;
  }
  
  public function login ($username = null, $password = null) {
    $stream = $this->connect();
    if ($username == null) {
      $username = $this->_username;
    }
    if ($password == null) {
      $password = $this->_password;
    }
    return ftp_login($stream, $username, $password);
  }
  
  public function delete ($path) {
    $stream = $this->connect();
    return ftp_delete($stream, $path);
  }
  
  public function exec ($command) {
    $stream = $this->connect();
    return ftp_exec($stream, $command);
  }
  
  public function getOption ($option = null) {
    if ($option === null) {
      return $this->getOptions();
    } 
    $stream = $this->connect();
    if ($option == 'timeout_sec') {
      $option = FTP_TIMEOUT_SEC;
    }
    if ($option == 'autoseek') {
      $option = FTP_AUTOSEEK;
    }
    return ftp_get_option($stream, $option);
  }
  
  public function setOption ($option, $value = null) {
    if (is_array($option)) {
      return $this->setOptions($option);
    }
    $stream = $this->connect();
    if ($option == 'timeout_sec') {
      $option = FTP_TIMEOUT_SEC;
    }
    if ($option == 'autoseek') {
      $option = FTP_AUTOSEEK;
    }
    return ftp_set_option($stream, $option, $value);
  }
  
  // TODO: Docs
  public function getOptions ($option) {
    $stream = $this->connect();
    return [
      'timeout_sec' => ftp_get_option($stream, FTP_TIMEOUT_SEC),
      'autoseek' => ftp_get_option($stream, FTP_AUTOSEEK),
    ];
  }
  
  // TODO: Docs
  public function setOptions ($options) {
    $status = true;
    foreach ($options as $option => $value) {
      if (!$this->setOption($option, $value)) {
        $status = false;
      }
    }
    return $status;
  }
  
  public function get ($local_file, $remote_file, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_get($stream, $local_file, $remote_file, $mode, $resumepos);
  }
  
  public function put ($remote_file, $local_file, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_put($stream, $remote_file, $local_file, $mode, $resumepos);
  }
  
  public function fget ($handle, $remote_file, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_fget($stream, $handle, $remote_file, $mode, $resumepos);
  }
  
  public function fput ($remote_file, $handle, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_fput($stream, $handle, $remote_file, $mode, $resumepos);
  }
  
  // TODO: Docs
  public function getContents ($remote_file, $mode = FTP_ASCII, $resumepos = 0) {
    $handle = fopen('php://temp', 'r+');
    $this->fget($handle, $remote_file, $mode, $resumepos);
    $stat = fstat($handle);
    $contents = fread($handle, $fstats['size']);
    fclose($handle);
    return $contents;
  }
  
  // TODO: Docs
  public function putContents ($remote_file, $contents, $mode = FTP_ASCII, $resumepos = 0) {
    $handle = fopen('php://temp', 'r+');
    fwrite($handle, $contents);
    rewind($handle);
    $status = $this->fput($remote_file, $handle, $mode, $resumepos);
    fclose($handle);
    return $status;
  }
  
  public function mdtm ($remote_file) {
    $stream = $this->connect();
    // NOTE: Not all servers support this function
    // TODO: Apparently, if the filename contains space(s), some servers will
    // error out. A suggested work-around was to wrap the filename in "quotes",
    // however the suggestion was downvoted 3 times (no comments). The problem
    // is not apparent to me -further research needed.
    // Would urlencode() / urldecode() help?
    return ftp_mdtm($stream, $remote_file);
  }
  
  public function mkdir ($directory) {
    $stream = $this->connect();
    return ftp_mkdir($stream, $directory);
  }
  
  public function nbContinue ($directory) {
    $stream = $this->connect();
    return ftp_nb_continue($stream);
  }
  
  public function nbFget ($handle, $remote_file, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_nb_fget($stream, $remote_file, $handle, $mode, $resumepos);
  }
  
  public function nbFput ($remote_file, $handle, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_nb_fput($stream, $remote_file, $handle, $mode, $resumepos);
  }
  
  public function nbGet ($local_file, $remote_file, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_nb_get($stream, $local_file, $remote_file, $mode, $resumepos);
  }
  
  public function nbPut ($remote_file, $local_file, $mode = FTP_ASCII, $resumepos = 0) {
    $stream = $this->connect();
    return ftp_nb_put($stream, $remote_file, $local_file, $mode, $resumepos);
  }
  
  public function nlist ($directory) {
    $stream = $this->connect();
    return ftp_nlist($stream, $directory);
  }
  
  public function pasv ($pasv) {
    $stream = $this->connect();
    return ftp_pasv($stream, $pasv);
  }
  
  public function pwd () {
    $stream = $this->connect();
    return ftp_pwd($stream);
  }
  
  public function quit () {
    return $this->close();
  }
  
  public function raw ($command) {
    $stream = $this->connect();
    return ftp_raw($stream, $command);
  }
  
  public function rawlist ($directory, $recursive = false) {
    $stream = $this->connect();
    return ftp_rawlist($stream, $directory, $recursive);
  }
  
  public function rename ($oldname, $newname) {
    $stream = $this->connect();
    return ftp_rename($stream, $oldname, $newname);
  }
  
  // TODO: ? move() alias for rename
  
  public function rmdir ($directory) {
    $stream = $this->connect();
    // TODO: ? Recursively delete directory and ancestors: good idea -bad idea?
    // If somewhere in the middle, possibly a separate function rrmdir()?
    return ftp_rmdir($stream, $directory);
  }
  
  public function site ($command) {
    $stream = $this->connect();
    return ftp_site($stream, $command);
  }
  
  public function size ($remote_file) {
    $stream = $this->connect();
    // NOTE: Some servers do not support this function
    // TODO: ? Recursively delete directory and ancestors: good idea -bad idea?
    // If somewhere in the middle, possibly a separate function rrmdir()?
    return ftp_site($stream, $remote_file);
  }
  
  // TODO: ? dirsize()
  
  public function sslConnect ($host = null, $port = null, $timeout = null) {
    if ($this->_stream) {
      return $this->_stream;
    }
    if ($host == null) {
      $host = $this->_host;
    }
    if ($port == null) {
      $port = $this->_port;
    }
    if ($timeout == null) {
      $timeout = $this->_timeout;
    }
    if (!function_exists('ftp_ssl_connect')) {
      return false;
    }
    $stream = ftp_ssl_connect($stream);
    return $this->_stream = $stream;
  }
  
  public function systype () {
    $stream = $this->connect();
    return ftp_systype($stream);
  }
  
}
