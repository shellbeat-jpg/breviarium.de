<?php



  $dir1    = "../cache/";
  $dir2    = "../cache_mobile/";
  $dir3    = "../templates_c/";
  $dir4    = "../templates_c_mobile/";

  deldir($dir1);
  deldir($dir2);
  deldir($dir3);
  deldir($dir4);


  function deldir( $dir ) {
    $handle=opendir ($dir);
    $i=0;
    while (false !== ($file = readdir ($handle))) {
      if( $file=='..' || $file=='.' || $file == '.htaccess' || $file == 'index.html') {
        continue;
      }
      $i++;
      unlink($dir.$file);
   #   if($i>300)
  #    header("Location: http://buch.breviarium.de/hidden/cache.php?&i=".($_GET['i']+$i));
      
    }
    closedir($handle);
  }
  header("refresh:5000,url=http://buch.breviarium.de/hidden/cache.php?&i=".($_GET['i']+$i));
  ?>