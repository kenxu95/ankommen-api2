<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\User;
use App\Image;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;


class UserController extends Controller
{
  use Helpers;

  // Respond with the current user
  public function show() 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();
    return $currentUser;
  }

  // Update the user's information (name, )
  public function update(Request $request) 
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $currentUser->fill(($request->all()['user']));

    if ($currentUser->save())
      return $this->response->noContent();
    else
      return $this->response->error('could_not_update_user', 500);
  } 

  // Respond with the user's image
  public function showImage(Request $request){
    $currentUser = JWTAuth::parseToken()->authenticate();

    if ($currentUser->image) {
      $filePath = 'images/'.$currentUser->image->filename;
      if (file_exists($filePath)){
        // Convert saved image to dataURL and return
        $type = pathinfo($filePath, PATHINFO_EXTENSION);
        $data = file_get_contents($filePath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return response()->json(array('img' => $base64))
                         ->header('Cache-Control', 'public');
      }
    }
    return response()->json(array("exists" => false));
  }

  // Store the image as the user's new image (discarding the previous one)
  public function storeImage(Request $request)
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    // Check the file: http://fr.php.net/manual/en/function.move-uploaded-file.php
    $tmpName = $_FILES['file']['tmp_name'];
    $orgName = basename($_FILES['file']['name']);

    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK)
      return $this->response->error('error_occured_during_upload', 500);

    // 1) Check that the file is not empty
    if (0 == filesize($tmpName))
      return $this->response->error('file_is_empty', 500);

    // 2) Check file name is in English characters, numbers and (_-.) symbols
    if (! $this->check_file_uploaded_name ($orgName))
      return $this->response->error('unauthorized_characters_in_file_name', 500);

    // 3) Check file name is not longer than 225 characters
    if (! $this->check_file_uploaded_length ($orgName))
      return $this->response->error('file_name_too_long', 500);

    // 4) Check file extensions to make sure it is an image
    if (! $this->check_file_extensions($_FILES['file']['type']))
      return $this->response->error('invalid_file_extension_type', 500);

    // 5) Check that the file size is not too big
    if (filesize($tmpName) > 2 * 1000000) // ~2MB
      return $this->response->error('file_size_too_large', 500);

  //   // AFTER CHECKS  
  //   // If previous picture existed 
    if ($currentUser->image){
      $prevImagePath = 'images/'.$currentUser->image->filename;
      if (file_exists($prevImagePath)){
        // Delete previous picture on server
        if (! unlink($prevImagePath))
          return $this->response->error('could_not_delete_previous_image_file', 500); 
      }
      // Delete previous model
      if (! $currentUser->image()->delete())
        return $this->response->error('could_not_delete_previous_image');
    }

    // Save the image model
    $newName = basename(tempnam('', '')) . basename($_FILES['file']['name']); // Prevent name collisions
    $image = new Image;
    $image->filename = $newName;
    if (! $currentUser->image()->save($image))
      return $this->response->error('could_not_save_image', 500);

    // Save the file on the server
    if (!is_dir('images'))
      mkdir('images');

    if(! move_uploaded_file($tmpName, 'images/' . $newName))
      return $this->response->error('could_not_save_image_file', 500);

    return $this->response->noContent(); // Successful
  }

  private function check_file_uploaded_name ($filename)
  {
    return preg_match("`^[-0-9A-Z_\.]+$`i", $filename);
  }

  private function check_file_uploaded_length ($filename)
  {
    return mb_strlen($filename,"UTF-8") <= 225;
  }

  private function check_file_extensions($filetype)
  {
    $allowed = array('png', 'jpg', 'gif', 'jpe', 'jpeg');
    return dirname($filetype) == 'image' 
           && 
           in_array(basename($filetype), $allowed);
  }

}

















