<?php

namespace Sb\SendboxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sb\SendboxBundle\Uploader\SbUploader;
use ZipArchive;
use Sb\SendboxBundle\Entity\DestEmail;
use Swift_Message;
use SimpleXMLElement;

class IndexController extends Controller
{
  public function indexAction()
  {
    $this->get('session')->set('token', $this->generateToken());
    return $this->container->get('templating')->renderResponse('SbSendboxBundle:Index:index.html.twig');
  }
  
  public function uploadAction(Request $request)
  {
    $token =  $this->get('session')->get('token');
    $this->get('session')->set('token', $token);
    $sizeLimit = 10 * 1024 * 1024;
    $path =  $this->getPathByToken($token);
    $uploader = new SbUploader($sizeLimit, $path);
    $uploader->isUnique();
    $result = $uploader->save();
    $result = json_decode($result, true);

    if(array_key_exists('success', $result) && $result['success'] == true) {
      if(file_exists($path.$token.'.xml')) {
        $newsXML = simplexml_load_file($path.$token.'.xml');
      }
      else {
        $newsXML = new SimpleXMLElement("<upload></upload>");
        $newsXML->addAttribute('token', $token);
      }
      $newsIntro = $newsXML->addChild('file', $result['filename']);
      $newsIntro->addAttribute('size', filesize($path.$result['filename']));
      $newsXML->saveXML($path.$token.'.xml');
    }

    return new Response(htmlspecialchars(json_encode($result), ENT_NOQUOTES));
  }
  
  public function sendAction(Request $request)
  {
    $email = $request->get('fromMail');
    $de = new DestEmail();
    $de->setEmail($email);

    $validator = $this->get('validator');
    $errors = $validator->validate($de);

    if (count($errors) > 0) {
        return new Response("L'email de l'emmetteur est invalide.");
    }
    else {
      $token = $this->get('session')->get('token');
      $newsXML = simplexml_load_file($this->getPathByToken($token).$token.'.xml');
      $size = 0;
      foreach($newsXML->file as $f) {
        $files[] = array('name' => "$f", "size" => $this->returnFileSize($f->attributes()->size));
        $size += $f->attributes()->size;
      }

      $toMail = $request->get('toMail');
      $toMail = explode(',', $toMail);
      $message = Swift_Message::newInstance()
      ->setSubject('Invitation à télécharger des fichiers')
      ->setFrom($email)
      ->setTo($toMail)
      ->setBody($this->renderView('SbSendboxBundle:Index:send.html.twig', array('token' =>$token, 'files' => $files, 'size' => $this->returnFileSize($size))), 'text/html');
      $this->get('mailer')->send($message);
      $this->get('session')->set('token', $this->generateToken());

      return new Response('SUCCESS');
    }
  }

  public function downloadAction(Request $request, $token)
  {
    $newsXML = simplexml_load_file($this->getPathByToken($token).$token.'.xml');
    foreach($newsXML->file as $f) {
      $files[] = array('name' => "$f", "size" => $this->returnFileSize($f->attributes()->size));
    }
    return $this->container->get('templating')->renderResponse('SbSendboxBundle:Index:download.html.twig', array('token' => $token, 'files' => $files));
  }

  public function generateGzipAction(Request $request, $token)
  {
    $newsXML = simplexml_load_file($this->getPathByToken($token).$token.'.xml');
    $files = array();
    foreach($newsXML->file as $f) {
      $files[] = array('name' => "$f", 'path' => $this->getPathByToken($token).$f);
    }

    if (count($files) > 1) {
      $path = $this->getPathByToken($token);
      if (!file_exists($path.'sendbox-files-'.$token.'.zip')) {
        $zip = new ZipArchive();
        $res = $zip->open($path.'sendbox-files-'.$token.'.zip', ZipArchive::CREATE);
        if ($res === true) {
          $zip->addEmptyDir('sendbox-files-'.$token);
          foreach ($files as $file) {
            if (is_readable($file['path'])) {
              $zip->addFile($file['path'], 'sendbox-files-'.$token. DIRECTORY_SEPARATOR .$file['name']);
            }
          }
          $zip->close();
          $return = array('name' => 'sendbox-files-'.$token.'.zip', 'path' => $this->getUrlByToken($token).'sendbox-files-'.$token.'.zip');
        }
      }
      else {
        $return = array('name' => 'sendbox-files-'.$token.'.zip', 'path' => $this->getUrlByToken($token).'sendbox-files-'.$token.'.zip');
      }
    }
    else {
      $return = array('name' => $files[0]['name'], 'path' => $this->getUrlByToken($token).$files[0]['name']);
    }

    return new Response(htmlspecialchars(json_encode($return), ENT_NOQUOTES));
  }

  public function validateAction(Request $request)
  {
    $email = $request->get('email');
    $de = new DestEmail();
    $de->setEmail($email);

    $validator = $this->get('validator');
    $errors = $validator->validate($de);

    if (count($errors) > 0) {
        return new Response(json_encode(array('status' => 'failure')));
    }
    else {
        return new Response(json_encode(array('status' => 'success', 'value' => $email)));
    }
  }
  
  public function getPathByToken($token)
  {
    $md5 = str_split($token);
    $path = implode('/', $md5);
    return $_SERVER['DOCUMENT_ROOT'].'/uploads/'.date('Y').'/'.date('m').'/'.$path.'/';
  }
  
  public function getUrlByToken($token)
  {
    $md5 = str_split($token);
    $path = implode('/', $md5);
    return '/uploads/'.date('Y').'/'.date('m').'/'.$path.'/';
  }

  private function generateToken($size = 8)
  {
    $chars = 'azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890';
    $retour = '';

    for ($i=0;$i<$size;$i++) {
      $retour .= $chars[rand(0, 35)];
    }

    return $retour;
  }

  private function returnFileSize($fileSize) {
    switch ($fileSize) :
      case ($fileSize < 1024):
          return $fileSize.' B';
      case ($fileSize > 1024 && $fileSize < 1048576):
          return round($fileSize/1024, 1).' KB';
      case ($fileSize > 1048576 && $fileSize < 1073741824):
          return round($fileSize/1048576, 1).' MB';
      case ($fileSize > 1073741824 && $fileSize < 1099511627776 ):
          return round($fileSize/1073741824, 1).' GB';
      case ($fileSize > 1099511627776 && $fileSize < 1125899906842624):
          return round($fileSize/1099511627776, 1).' TB';
      case ($fileSize > 1125899906842624):
          return round($fileSize/1125899906842624, 1).' PB';
      default:
          return $fileSize;
    endswitch;
  }
}
