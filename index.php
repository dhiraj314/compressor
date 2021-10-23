<?php
ini_set('memory_limit', '-1');
if(isset($_POST['submit']))
{
  echo $img_loc=rand().time();
  mkdir("images/".$img_loc);

  $countfiles = count($_FILES['files']['name']);

  $zip_name = $img_loc.".zip"; // Zip file name 
  $zip = new ZipArchive;
  if ($zip->open("zips/".$zip_name, ZipArchive::CREATE) == TRUE){  
    for($i=0;$i<$countfiles;$i++){
       $newname=$_FILES["files"]["name"][$i];
        if(!empty($newname)){
         $target_file =basename($_FILES["files"]["name"][$i]);
         $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
         $extensions_arr = array("jpg","jpeg","png");
          if(in_array($imageFileType,$extensions_arr)){

            $source = $_FILES['files']['tmp_name'][$i];
            $destination_url = "images/".$img_loc."/".$newname;

            $info = getimagesize($source);
            if ($info['mime'] == 'image/jpeg')
              $src = imagecreatefromjpeg($source);
            elseif ($info['mime'] == 'image/png') 
              $src = imagecreatefrompng($source);
            else
              continue;

            list($width,$height)=getimagesize($source);
            $new_width=$width;
            $new_height=($height/$width)*$new_width;
            $tmp=imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($tmp,$destination_url,75);
            $zip->addFile($destination_url, $newname);
          }  
        }
        else{
          echo "<script>alert('empty files')</script>";
        }
    }
  }
  $zip->close();
  array_map( 'unlink', array_filter((array) glob("images/".$img_loc."/*") ) );
  rmdir("images/".$img_loc);


}

// if(isset($_POST['download'])){
//   $fetch=mysqli_query($con,"select * from compress_zip");
//    $ipaddresss = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//  $rrs= "./zip/".$ipaddresss."/";

//  $ipaddress = getHostByName(getHostName());
//   $r= "./zip/".$ipaddress."/";

  

//       while ($row1=mysqli_fetch_assoc($fetch)) {
//         header('Content-Type: application/zip');
//         header('Content-Disposition: attachment; filename="'.$row1['zip'].'"');
//         header('Content-Length: ' . filesize($rrs.$row1['zip']));
//         flush();
//         readfile($rrs.$row1['zip']);
        // delete file
      //   $v=unlink($rrs.$row1['zip']);
      //   if($v){
      //      $del=mysqli_query($con,"delete  from compress_zip") ;
      //      rmdir($rrs);
      //      rmdir($r);
      //   }
      // }
// }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Compress Data</title>
</head>
<body>
<form method="post" enctype="multipart/form-data">  
  <input type="file" name="files[]" multiple>
  <input type="submit" name="submit" value="submit">
  <br>
  <br>
 <button  name="download" >Download</button>

</form>
</body>
</html>
