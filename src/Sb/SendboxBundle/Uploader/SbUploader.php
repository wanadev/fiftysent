<?php

namespace Sb\SendboxBundle\Uploader;

class SbUploader
{
  private $sizeLimit;
  private $targetDir;
  private $cleanupTargetDir = true;
  private $maxFileAge = 18000;
  private $chunk;
  private $chunks;
  private $fileName;
  private $filePath;
  private $contentType;

  public function __construct($sizeLimit, $targetDir)
  {
    $this->sizeLimit = $sizeLimit;
    $this->targetDir = $targetDir;
    $this->chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    $this->chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
    $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
    $this->fileName = preg_replace('/[^\w\._]+/', '_', $fileName);

    if (!file_exists($this->targetDir)) {
      @mkdir($this->targetDir, 0777, true);
    }

    $this->filePath = $this->targetDir . DIRECTORY_SEPARATOR . $this->fileName;

    if (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
      $this->contentType = $_SERVER["HTTP_CONTENT_TYPE"];
    }

    if (isset($_SERVER["CONTENT_TYPE"])) {
      $this->contentType = $_SERVER["CONTENT_TYPE"];
    }

    $this->checkServerSettings();
  }

  private function checkServerSettings()
  {        
    $postSize = $this->toBytes(ini_get('post_max_size'));
    $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
    
    if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
      $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
      die("{'error':'increase post_max_size and upload_max_filesize to $size'}");    
    }        
  }

  private function toBytes($str)
  {
    $val = trim($str);
    $last = strtolower($str[strlen($str)-1]);
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;        
    }
    return $val;
  }

  public function isUnique()
  {
    if ($this->chunks < 2 && file_exists($this->targetDir . DIRECTORY_SEPARATOR . $this->fileName)) {
      $ext = strrpos($this->fileName, '.');
      $fileNameA = substr($this->fileName, 0, $ext);
      $fileNameB = substr($this->fileName, $ext);

      $count = 1;
      while (file_exists($this->targetDir . DIRECTORY_SEPARATOR . $fileNameA . '_' . $count . $fileNameB)) {
        $count++;
      }

      $this->fileName = $fileNameA . '_' . $count . $fileNameB;
    }
  }

  private function cleanTargetDir()
  {
    if ($this->cleanupTargetDir && is_dir($this->targetDir) && ($dir = opendir($this->targetDir))) {
      while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $this->targetDir . DIRECTORY_SEPARATOR . $file;

        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $this->maxFileAge) && ($tmpfilePath != "{$this->filePath}.part")) {
          @unlink($tmpfilePath);
        }
      }

      closedir($dir);

      return false;
    } else {
      return '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}';
    }
  }

  public function save()
  {
    // Return JSON-RPC response
    $result = '{"jsonrpc" : "2.0", "result" : "progress", "chunk" : "'.$this->chunk.'"}';

    $error = $this->cleanTargetDir();
    $result = $error === false ? $result : $error;

    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if (strpos($this->contentType, "multipart") !== false) {
      if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
        // Open temp file
        $out = fopen("{$this->filePath}.part", $this->chunk == 0 ? "wb" : "ab");
        if ($out) {
          // Read binary input stream and append it to temp file
          $in = fopen($_FILES['file']['tmp_name'], "rb");

          if ($in) {
            while ($buff = fread($in, 4096)) {
              fwrite($out, $buff);
            }
          } else {
            $result = '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}';
          }
          fclose($in);
          fclose($out);
          @unlink($_FILES['file']['tmp_name']);
        } else {
          $result = '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}';
        }
      } else {
        $result = '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}';
      }
    } else {
      // Open temp file
      $out = fopen("{$this->filePath}.part", $this->chunk == 0 ? "wb" : "ab");
      if ($out) {
        // Read binary input stream and append it to temp file
        $in = fopen("php://input", "rb");

        if ($in) {
          while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
          }
        } else {
          $result = '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}';
        }

        fclose($in);
        fclose($out);
      } else {
        $result = '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}';
      }
    }

    // Check if file has been uploaded
    if (!$this->chunks || $this->chunk == $this->chunks - 1) {
      // Strip the temp .part suffix off 
      rename("{$this->filePath}.part", $this->filePath);
      $result = '{"jsonrpc" : "2.0", "success" : true, "filename" : "'.$this->fileName.'"}';
    }

    return $result;
  }
}