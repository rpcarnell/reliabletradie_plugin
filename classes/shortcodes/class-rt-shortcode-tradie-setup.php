<?php
class RT_Shortcode_Tradie_Setup {
     function output()
     {
         if ( ! is_user_logged_in() ) {  reliabletradie_my_account(); return; }
         global $reliableTradie, $wpdb;
         $user_id = get_current_user_id();
         $query = "SELECT usertype FROM ".$wpdb->prefix."reliabletradie_usertype WHERE user_id = $user_id LIMIT 1";
         $usertype = $wpdb->get_var($query);
         if ($usertype != 'tradie') 
         {
             echo ("<p>If you want to use these features, you must register a tradie account</p>");
             return;
         }
         wp_enqueue_style( 'reliabletradie_search_css', $reliableTradie->plugin_url(). '/assets/css/reliabletradie.css'  );
         wp_enqueue_script( 'woocommerce_admin', $reliableTradie->plugin_url() . '/assets/js/rt.js');
         $dir = $reliableTradie->getTemplateDir();
         $rt = new RT_Shortcode_Tradie_Setup();
         $query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_providers WHERE user_id = %d LIMIT 1", $user_id);
         $providerInfo = $wpdb->get_row( $query );
        // print_r($providerInfo);
         $submitted1 = $submitted2 = 0;
         //print_r($_POST);
         if (isset($_POST['action']) && $_POST['action'] == 'rt_tradie_upload') //data must be stored
         { 
             if ($_POST['description'] != '')
             { 
                 $post = $_POST; 
                 $rt->updateProvider($user_id, $providerInfo, $post, $reliableTradie); 
                 $providerInfo->description = $_POST['description'];
             }
             if ($_FILES['providerimage']) 
             {
                $uploaded_pic = $rt->setthisup($_FILES['providerimage'], $reliableTradie, $_POST['providerid']); 
                if ($uploaded_pic) { $rt->storePicInDB($_FILES['providerimage'], $uploaded_pic, $_POST['providerid']); }
             }
         }
         elseif (isset($_POST['action']) && $_POST['action'] == 'rt_tradie_basics') //data must be stored
         {  
             $submitted1 = 1;
             $post = $_POST; 
             $rt->storeBasics($user_id, $providerInfo, $post);
             if ($_POST['description'] != '')
             { 
                 $rt->updateProvider($user_id, $providerInfo, $post, $reliableTradie); 
                 $providerInfo->description = $_POST['description'];
             }
         }
         elseif (isset($_POST['action']) && $_POST['action'] == 'rt_tradie_locations') //data must be stored
         {
             $post = $_POST;
             $rt->storeCities($user_id, $providerInfo, $post);
         } else { }
         $rows = $rt->getImages(get_current_user_id());
         include_once($dir."/tradiesetup.php");
     }
     function storeCities($provider_id, $row, $post)
     {
         global $reliableTradie, $wpdb;
         $token = $this->_providerToken($reliableTradie);
         if (!$token)
         {
             $errorMSG = "Token is not valid"; 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return false;
         }
         $cities = $post['cities'];
          
         if (!is_array($cities)) return;
         foreach ($cities as $city)
         {
             $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."reliabletradie_provilocations (provider_id, location_id) VALUES (%d, %d)", 
             $provider_id, $city);
             $row = $wpdb->query( $query );
         }
         $cities =  "-".implode('-', $cities)."-";
         if (! $row)
         {
             $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."reliabletradie_providers (user_id, cities) VALUES (%d, %s)", 
             $provider_id, $cities);
             $row = $wpdb->query( $query );
         }
         else
         {
             $query = $wpdb->prepare( "UPDATE ".$wpdb->prefix."reliabletradie_providers SET cities = %s WHERE user_id = %d LIMIT 1", $cities, $provider_id);
             $row = $wpdb->query( $query );
         }
         $reliableTradie->add_message('New Data Stored Successfully');  
         $reliableTradie->show_messages();
     }
     function storeBasics($provider_id, & $row, $post)
     {
         global $reliableTradie, $wpdb;
         require_once ABSPATH . "wp-includes/" . 'class-phpmailer.php';
         if ($post['company_name'] == '' || $post['provider_name'] == '' || $post['provider_email'] == '' || $post['phone'] == '') 
         {
             $errorMSG = "One of the fields is empty"; 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return false;
         }
         $validated = PHPMailer::ValidateAddress($post['provider_email']);
         if (!$validated) 
         {
             $errorMSG = "E-mail Address is not valid"; 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return false;
         }
         $token = $this->_providerToken($reliableTradie);
         if (!$token)
         {
             $errorMSG = "Token is not valid"; 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return false;
         }
         $filteroptions = explode('-', $row->filteroptions);
         if (!is_array($filteroptions)) $filteroptions = array();
         $fw = array();
         foreach($filteroptions as $fff)
         {
             if (is_numeric($fff)) $fw[] = $fff;
         }
         if (in_array($post['userrq'], $fw))
         {
             
         }
         else $filteroptions = array_merge($fw, array($post['userrq']));
         $filteroptions = implode('-',$filteroptions);
         $userrq =  "-$filteroptions-";
         $userrq = str_replace('--', '-', $userrq); 
         if (! $row)
         {
             $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."reliabletradie_providers (user_id, company_name, provider_name, provider_email, phone, filteroptions, url) VALUES (%d, %s, %s, %s, %s, %s, %s)", 
             $provider_id, $post['company_name'], $post['provider_name'], $post['provider_email'], $post['phone'], $userrq, $post['url']);
             $row = $wpdb->query( $query );
         }
         else
         {
             $query = $wpdb->prepare( "UPDATE ".$wpdb->prefix."reliabletradie_providers SET company_name = %s, provider_name = %s, provider_email = %s, phone = %s, filteroptions = %s, url = %s WHERE user_id = %d LIMIT 1", $post['company_name'], $post['provider_name'], $post['provider_email'], $post['phone'], $userrq, $post['url'], $provider_id);
             $row = $wpdb->query( $query );
         }
         $query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_providers WHERE user_id = %d LIMIT 1", $provider_id);
         $row = $wpdb->get_row( $query );
     }
    
     function getImages($user_id)
     {
         global $wpdb;
         $rows = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_providerimages WHERE provider_id = %d", $user_id ) );
         return $rows;
     }
     private function _providerToken(& $reliableTradie)
     {
         $token = $reliableTradie->verify_nonce( 'update_tradie_info' );
         if (!$token)
         {
             $errorMSG = "Token is not valid"; 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return false;
         }
         return true;
     }
     function updateProvider($provider_id, $row, $post, & $reliableTradie)
     {
         $verify = $this->_providerToken($reliableTradie);
         global $wpdb;
         if (! $verify) return;
         if (! $row)
         {
             $query = $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."reliabletradie_providers (user_id, description) VALUES (%d, %s)", $provider_id, $post['description']);
             $row = $wpdb->query( $query );
         }
         else
         {
             $query = $wpdb->prepare( "UPDATE ".$wpdb->prefix."reliabletradie_providers SET description = %s WHERE user_id = %d LIMIT 1", $post['description'], $provider_id);
             $row = $wpdb->query( $query );
         }
     }
     function setthisup($file, & $reliableTradie, $user_id)
     {
         $verify = $this->_providerToken($reliableTradie);
         if (! $verify) return;
         //let's upload the image:
         add_filter( 'upload_dir', 'rt_tradie_upload_dir', 10, 0 );
         $allowed = array('image/jpeg', 'image/png', 'image/gif', 'image/JPG', 'image/jpg', 'image/pjpeg');
         if (! $file['name']) //To check if the file are image file
         { return;  }
         elseif (!in_array($file['type'], $allowed)) //To check if the file are image file
         { // print_r($file);
             $errorMSG = sprintf(  'The file you are trying to upload %s is not supported.', $file['type']); 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return;
         }
         global $wpdb;
         $numPhotos = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) as numPhotos FROM ".$wpdb->prefix."reliabletradie_providerimages WHERE provider_id = %d LIMIT 1", $user_id) );
         if ($numPhotos >= 9)
         {
             $errorMSG = "You have already uploaded $numPhotos photos"; 
                     $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return;
         }
         $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."reliabletradie_providerimages WHERE provider_id = %d AND provider_image = %s LIMIT 1", $user_id, $file['name'] ) );
         if ($row)
         {
             $errorMSG = "You have already uploaded this image"; 
             $reliableTradie->add_error($errorMSG); 
             $reliableTradie->show_messages();
             return;
         }
         require_once( ABSPATH . '/wp-admin/includes/file.php' );
         $original = wp_handle_upload( $file, array( 'action'=> 'rt_tradie_upload' ) );
         //global $post;
         if (!isset($original['error']) || empty($original['error']))
         { $reliableTradie->add_message('File was uploaded successfully'); 
              // print_r($original);
               include_once(WP_CONTENT_DIR.'/plugins/reliatabletradie/libraries/thumbnail.inc.php');
               $raw_file = explode('/',$original['file']);
               $raw_filename = $raw_file[count($raw_file) - 1];
               unset($raw_file[count($raw_file) - 1]);//get rid of the filename
               $raw_file = implode('/', $raw_file);
               $imageCr = $this->Right_Creator($raw_file, $raw_filename);
               if ($imageCr === false) { return; }
               $width = imagesx( $imageCr );
               $height = imagesy( $imageCr );
               imagedestroy($imageCr);
               if ($width > 300) { $this->Minimize_Image($raw_file, $raw_filename, 300, 'width'); }
               if ($height > 300) { $this->Minimize_Image($raw_file, $raw_filename, 300, 'height'); }
         }
         else
         { $reliableTradie->add_error( sprintf(  'Upload Failed! Error was: %s', $original['error']) ); return false; }
         $reliableTradie->show_messages();
         return $original;
     }
     function storePicInDB($file, $uploaded_pic_data, $providerid)
     {
         if (!is_numeric($providerid)) return;
         global $wpdb;
         $q = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."reliabletradie_providerimages (provider_id, provider_image, image_url) VALUES (%d, %s, %s)", $providerid, $file['name'], $uploaded_pic_data['url']);
         $wpdb->query($q);
     }
     private function Right_Creator($pathToImages, $fname)
     {
        $pos = strrpos($fname, ".");
         if ($pos === false) return false;
          $extension = strtolower(substr($fname, $pos +1));
          if ($extension == 'gif') $img = @imagecreatefromgif( "{$pathToImages}{$fname}" );
          elseif ($extension == 'png') $img = @imagecreatefrompng( "{$pathToImages}{$fname}" );
          elseif ($extension == 'bmp') $img = $this->ImageCreateFromBMP( "{$pathToImages}{$fname}" );
          else $img = @imagecreatefromjpeg( "{$pathToImages}{$fname}" );
          return $img;
    }
    private function Minimize_Image($pathToImages, $fname, $thumb = "150", $choice ='width', $thumbnail = false)
    {
   //   echo "-> $pathToImages, $fname <-";

       if ($fname == '') return;
       if (!is_writeable($pathToImages))
       {
              echo "<script> alert('Error - Path #1 is not writeable! Exiting now');</script>\n";
              exit;
       }
           $thumbClass = new Thumbnail($pathToImages.$fname);
           if ($thumbnail === true)
          {
              $pathToImages = str_replace('\/\/', '\/', $pathToImages.DS.'thumbnails');
              if (!is_writeable($pathToImages))
       {
              echo "<script> alert('Error - Thumbnail Path is not writeable! Exiting now');</script>\n";
              exit;
       }
          }
           $height = $thumbClass->getCurrentHeight();
           $width = $thumbClass->getCurrentWidth();
         //  echo "height is $height and width is $width"; exit;
           // calculate thumbnail size
           if ($choice == 'width') {$new_width = $thumb;
           $new_height = floor( $height * ( $thumb / $width ) );}
           else {$new_height = $thumb;
            $new_width = floor( $width * ( $thumb / $height ) );}
             $thumbClass->resize($new_width, $new_height);
             $thumbClass->save($pathToImages.$fname, 100);

}
}
function rt_tradie_upload_dir()
{
     global $reliableTradie;
     $path = WP_CONTENT_DIR . '/uploads';
     $path = $path."/reltradie/";
     $newbdir = $path;
     if ( !file_exists( $path ) ) @wp_mkdir_p( $path );
     $newurl    = 'wp-content/uploads/reltradie';//bp_core_avatar_url() . '/group-avatars/' . $group_id;
     $newburl   = $newurl;
     $newsubdir = $path.'userimages';
     return apply_filters( 'rt_tradie_upload_dir', array( 'path' => $path, 'url' => $newurl, 'subdir' => $newsubdir, 'basedir' => $newbdir, 'baseurl' => $newburl, 'error' => false ) );
}

