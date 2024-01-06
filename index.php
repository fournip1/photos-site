<?php
// INIT
$dir = __DIR__ . DIRECTORY_SEPARATOR . "gallery" . DIRECTORY_SEPARATOR;
$tdir = __DIR__ . DIRECTORY_SEPARATOR . "thumbnail" . DIRECTORY_SEPARATOR;
$maxLong = 600; // maximum width or height, whichever is longer
$images = [];


// FUNCTION TO FETCH THE DATE
function chargerdate($file)
{
$date="";
$exif_tab=[];
if($exif = exif_read_data($file, 'EXIF', true))
          {
                foreach ($exif as $key => $section)
                {
                    foreach ($section as $name => $value)
                    {
                    $exif_tab[$name] .= $value; // Récupération des valeurs dans le tableau $exif_t$
                    }
                }
                if($exif_tab['DateTimeOriginal']) 
                {
                $date = $exif_tab['DateTimeOriginal'];
                }
          }
$oneyear = 24*3600*365;
$now = time();
$year = floor(($now-strtotime($date))/$oneyear);
return ["Date"=>$date,"Year"=>$year];
}



// READ FILES FROM GALLERY FOLDER
$files = glob($dir . "*.{jpg,JPG,jpeg,gif,png,bmp,webp}", GLOB_BRACE);
$years = [];

// CHECK AND GENERATE THUMBNAILS
foreach ($files as $f) {
  $img = basename($f);
  $dateyear = chargerdate($f);
  $date = $dateyear["Date"];
  $year = $dateyear["Year"];
  $images[] = ["File"=>$img,"Date"=>$date,"Year"=>$year];

  if (!in_array($year, $years)) {
    $years[] = $year;
  }

  if (!file_exists($tdir . $img)) {
    // Extract image information
    $ext = strtolower(pathinfo($img)['extension']);
    list ($width, $height) = getimagesize($dir . $img);
    $ratio = $width > $height ? $maxLong / $width : $maxLong / $height ;
    $newWidth = ceil($width * $ratio);
    $newHeight = ceil($height * $ratio);

    // Resize
    $fnCreate = "imagecreatefrom" . ($ext=="jpg" ? "jpeg" : $ext);
    $fnOutput = "image" . ($ext=="jpg" ? "jpeg" : $ext);
    $source = $fnCreate($dir . $img);
    $destination = imagecreatetruecolor($newWidth, $newHeight);

    // Transparent images only
    if ($ext=="png" || $ext=="gif") {
      imagealphablending($destination, false);
      imagesavealpha($destination, true);
      imagefilledrectangle(
        $destination, 0, 0, $newWidth, $newHeight,
        imagecolorallocatealpha($destination, 255, 255, 255, 127)
      );
    }

    imagecopyresampled($destination, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    $fnOutput($destination, $tdir . $img);
  }
}

usort(
  $images,
  function($i1, $i2) {
    	  return $i2["Date"] <=> $i1["Date"];
  }
);

sort($years);
// DRAW HTML 
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Samichou les yeux bleus</title>
    <link href="box.css" rel="stylesheet">
  </head>
  <body>

    <!--- [CHOIX DES ANNEES] --->

  <div id="speriod">
  <fieldset>
  <legend>Choisir la période.</legend>
  
    <?php
    foreach($years as $y) {
      if ($y =='0') {
        printf("<input type='radio' id='year%s' name='period' value='%s' onclick='gallery.showdiv(this)' checked/> \n",$y, $y);
        printf("<label for='year%s'>cette année</label><br> \n",$y);
      }
      elseif ($y =='1') {
        printf("<input type='radio' id='year%s' name='period' value='%s' onclick='gallery.showdiv(this)' /> \n",$y, $y);
        printf("<label for='year%s'>il y a %s an</label><br> \n",$y, $y);
      } 
      else {
        printf("<input type='radio' id='year%s' name='period' value='%s' onclick='gallery.showdiv(this)' /> \n",$y, $y);
        printf("<label for='year%s'>il y a %s ans</label><br> \n",$y, $y);
      }
      
    }
    ?>
</fieldset>
  </div>


    <!-- [LIGHTBOX] -->
    <div id="lback" onclick="gallery.hide()">
      <div id="lfront"></div>
    </div>

    <!-- [THE GALLERIES] -->
    <?php
    $y = $images[0]["Year"];
    printf("<div class='gallery' id='%s'>"."\n",$y);
    foreach ($images as $i) {
      if ($i["Year"]!=$y) {
        printf("</div>"."\n");
        $y = $i["Year"];
        printf("<div class='gallery' id='%s'>"."\n",$y);
      }
      printf("<img src='thumbnail/%s' onclick='gallery.show(this)'/>"."\n", basename($i["File"]));
    }
    ?></div>
  <script src="thumbnail.js"></script>
  </body>
</html>
