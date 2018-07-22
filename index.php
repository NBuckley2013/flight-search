<?php
  session_start();
  require "lib/db.php";
  require "lib/Sabre.php";
  require "lib/Amadeus.php";

  $airportImageDir = new DirectoryIterator("img/airport-codes/images/large//");

  if ((isset($_POST["gbp"]) || (!isset($_SESSION["currency"])))) {
    $_SESSION["currency"] = "GBP";
  }
  if (isset($_POST["usd"])) {
    $_SESSION["currency"] = "USD";
  }
  if (isset($_POST["eur"])) {
    $_SESSION["currency"] = "EUR";
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="icon" href="/img/favicon.png">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="js/jQuery.js" type="text/javascript"></script>
    <link rel="icon" href="favicon.png">
    <title>About</title>
    <link rel="stylesheet" href="https://code.cdn.mozilla.net/fonts/fira.css">
    <link rel="stylesheet" type="text/css" href="css/styling.css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <meta name="viewport" content="width=device-width, initial-scale=0.7">
  </head>

  <body>

    <div id="wrapper">
      <div id="header">
        <link rel="icon" href="favicon.ico" />
        <link rel="shortcut icon" href="favicon.ico" />
        <div id="header_text">
          <select id="select" onchange="location = this.value;">
            <option value="">About</option>
            <option value="inspiration_search.php">Inspiration Search</option>
            <option value="flight_search.php">Flight Search</option>
          </select>
        </div>
      </div>

      <div id="container">
        <?php
          $imagesDir = "img/airport-codes/images/large/";
          $images = glob($imagesDir . "*.{jpg}", GLOB_BRACE);
          $randomImage = $images[array_rand($images)];
        ?>
        <div id="about_image" style="background-image: url('<?php echo $randomImage ?>')">
          <div id="about">
            <p>Simple flight search application<br>Created by Neil Buckley</p><br>
            <p>Try an <b><a href="inspiration_search.php">Inspiration Search</a></b>
            to find random flights from a number of supported cities, or use <b><a href="flight_search.php">Flight Search</a></b>
            to find specific flights based on a given destination.</p><br>
            <p>Built using:<br><b><a href="https://sandbox.amadeus.com" target="_blank">Amadeus Travel Innovation Sandbox</a><br>
            <a href="https://developer.sabre.com" target="_blank">Sabre Dev Studio</a><br>
            <a href="https://airportcod.es" target="_blank">Airport Codes</a><br>
              <a href="https://airhex.com" target="_blank">AirHex</a><br>
            <a href="https://www.flickr.com/services/developer" target="_blank">Flickr</a><br></b></p>
          </div>
        </div>
      </div>
    </div>

    <footer id="footer">
      <a href="../">www.neilbuckley.co.uk</a>
    </footer>

  </body>
</html>
