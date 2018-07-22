<?php
  session_start();
  require "lib/db.php";
  require "lib/Sabre.php";
  require "lib/Amadeus.php";
  require "lib/Flickr.php";

  $oneWay = "false";
  $direct = "false";
  $airlineLogoDir = new DirectoryIterator("img/airhex-starter-pack/airline-logos/by-iata-codes/700x200//");
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

  if (isset($_POST["origin"])) {
    $origin = $_POST["origin"];
  }
  if (isset($_POST["date"])) {
    $date = $_POST["date"];
  }
  if (isset($_POST["one_way"])) {
    $oneWay = $_POST["one_way"];
  }
  if (isset($_POST["direct"])) {
    $direct = $_POST["direct"];
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="js/jQuery.js" type="text/javascript"></script>
    <link rel="icon" href="favicon.png">
    <title>Inspiration Search</title>
    <link rel="stylesheet" href="https://code.cdn.mozilla.net/fonts/fira.css">
    <link rel="stylesheet" type="text/css" href="css/styling.css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <meta name="viewport" content="width=device-width, initial-scale=0.7">
  </head>

  <body class="inspiration_search">

    <div id="wrapper">
      <div id="header">
        <link rel="icon" href="favicon.ico" />
        <link rel="shortcut icon" href="favicon.ico" />
        <div id="header_text">
          <select id="select" onchange="location = this.value;">
            <option value="">Inspiration Search</option>
            <option value="flight_search.php">Flight Search</option>
            <option value="./">About</option>
          </select>
        </div>
        <!--<div id="currency">
          <form action="" method="post">
            <?php
            if ($_SESSION["currency"] == "EUR") {
              echo "<button type='submit' name='eur' value='eur' disabled><img src='img/eur.png'></button>";
            } else {
              echo "<button type='submit' name='eur' value='eur'><a href=''><img style='filter: grayscale(100%)' src='img/eur.png'></a></button>";
            }
            if ($_SESSION["currency"] == "GBP") {
              echo "<button type='submit' name='gbp' value='gbp' disabled><img src='img/gbp.png'></button>";
            } else {
              echo "<button type='submit' name='gbp' value='gbp'><a href=''><img style='filter: grayscale(100%)' src='img/gbp.png'></a></button>";
            }
            if ($_SESSION["currency"] == "USD") {
              echo "<button type='submit' name='usd' value='usd' disabled><img src='img/usd.png'></button>";
            } else {
              echo "<button type='submit' name='usd' value='usd'><a href=''><img style='filter: grayscale(100%)' src='img/usd.png'></a></button>";
            }
            ?>
          </form>
        </div> -->
      </div>

      <div id="container">
        <?php
          $imagesDir = "img/airport-codes/images/large/";
          $images = glob($imagesDir . "*.{jpg}", GLOB_BRACE);
          $randomImage = $images[array_rand($images)];
        ?>

        <div id="navigation" style="background-image: url(<?php echo $randomImage; ?>)">
          <form action="" method="post">
            <div id="navigation_options">
              <input type="checkbox" name="direct" value="true" <?php if(isset($direct) && $direct == "true") echo "checked";?> >Direct
              <input type="radio" name="one_way" value="false" <?php if(isset($oneWay) && $oneWay == "false") echo "checked";?> >Return
              <input type="radio" name="one_way" value="true" <?php if(isset($oneWay) && $oneWay == "true") echo "checked";?> >One-Way
            </div><br>
            <input type="text" name="origin" id="origin" autocomplete="off" value="<?php if(isset($origin)) echo $origin ?>" size="16" placeholder="Origin" minlength="3" required>
            <input type="text" name="date" id="date" autocomplete="off" value="<?php if(isset($date)) echo $date ?>" size="16" placeholder="Date" required>
            <input type="submit" name="search" id="search" value="Search" size="5">
          </form>
        </div>

        <div id="loading">
          <!-- Spinner 1 -->
          <div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
          <!-- Spinner 2
          <div class="lds-ring"><div></div><div></div><div></div><div></div></div> -->
        </div>

        <?php
          if (isset($_POST["search"])) {
            $currentMonth = date('m');
            if ($currentMonth == date('m', strtotime($date))) {
              $day = date('d');
            } else {
              $day = "01";
            }
            $dateRange = date("Y-m", strtotime($date)) . "-" . $day . "--" . date("Y-m-t", strtotime($date));
            $flights = json_decode($amadeus->cURL($amadeus->getInspirationFlights($origin, $dateRange, $oneWay, $direct), "GET"), true);

            if (array_key_exists("origin", $flights)) {
              $origin = $flights["origin"];
              $currency = $flights["currency"];
              $i = 0;
              // randomise results
              shuffle($flights["results"]);
              foreach ($flights["results"] as $results) {
                $destination = $results["destination"];
                $depart = $results["departure_date"];
                if (array_key_exists("return_date", $results)) {
                  $return = $results["return_date"];
                }
                $price = $results["price"];
                $airline = $results["airline"];

                // get city name
                $originCity = json_decode($amadeus->cURL($amadeus->getCity($origin), "GET"), true);
                $destinationCity = json_decode($amadeus->cURL($amadeus->getCity($destination), "GET"), true);

                if (array_key_exists("city", $originCity)) {
                  $originName = $originCity["city"]["name"];
                } else {
                  $originName = $origin;
                }
                if (array_key_exists("city", $destinationCity)) {
                  $destinationName = $destinationCity["city"]["name"];
                } else {
                  $destinationName = $destination;
                }

                // Flickr
                $imageID = json_decode($flickr->cURL($flickr->searchForImage(str_replace(" ", "+", $destinationName))), true);
                $imageSource = json_decode($flickr->cURL($flickr->getImageFromID($imageID["photos"]["photo"][0]["id"])), true);

                foreach ($imageSource["sizes"]["size"] as $key => $value) {
                  if ($value["label"] == ("Large")) {
                    $image = $value["source"];
                  } else {
                    $image = $value["source"];
                  }
                }

                // get airline logos
                foreach ($airlineLogoDir as $logo) {
                  if ($logo->getFilename() == $airline . ".png") {
                    $airlineLogo = $airlineLogoDir->getPath() . $logo->getFilename();
                  } elseif (!$logo->getFilename() == $airline . ".png") {
                    $airlineLogo = "";
                  }
                }

                // get airline names
                $airlineInfo = json_decode($sabre->cURL($sabre->getAirline($airline), "GET"), true);
                if (array_key_exists("AirlineInfo", $airlineInfo)) {
                    foreach ($airlineInfo["AirlineInfo"] as $name) {
                    if ($name["AlternativeBusinessName"] > $name["AirlineName"]) {
                      $airlineName = $name["AlternativeBusinessName"];
                    } else {
                    $airlineName = $name["AirlineName"];
                    }
                  }
                } else {
                  $airlineName = $airline;
                }

                if ($oneWay == "false") {
                  $arrow = " ⇄ ";
                } else {
                  $arrow = " → ";
                }

                echo "<a href='flight_search.php?origin=" . $origin . "&destination=" . $destination . "&depart_date=" . $depart;
                if (array_key_exists("return_date", $results)) {
                  echo "&return_date=" . $return;
                }
                echo "&one_way=" . $oneWay . "&direct=" . $direct . "'>";
                echo "<div class='flights_container' style='background-image: url(" . $image . "); background-repeat: no-repeat; background-size: cover; background-position: center'>";
                echo "<div class='route'>";
                echo "Search " . $originName . $arrow . $destinationName . " flights</div><div class='flights'>";
                echo "<table class='flight_details'><tr><td colspan='2' style='padding-right: 10px'>";
                echo '<span title="' . $origin . '">' . $originName . '</span>' . $arrow . '<span title="' . $destination . '">' . $destinationName . '</span></td>';
                echo "<td align='right'><span title='" . $airlineName . "'><img class='airline_logo' src='" . $airlineLogo . "' width='128' height='37'/></span></td></tr>";
                echo "</td></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>";
                echo "<tr><td align='left'>Departs: </td><td align='right'></td><td align='right'>" . date("l jS F", strtotime($depart)) . "</td></tr>";
                if (array_key_exists("return_date", $results)) {
                  echo "<tr><td align='left'>Returns: </td><td align='right'></td><td align='right'>" . date("l jS F", strtotime($return)) . "</td></tr>";
                }
                echo "</table></div>";
                if ($currency == "GBP") {
                  $currency = "£";
                }
                if ($currency == "EUR") {
                  $currency = "€";
                }
                if ($currency == "USD") {
                  $currency = "$";
                }
                echo "<div class='price'>" . $currency . $price . "</div></div></a><br>";

                // break after 5th result
                $i++;
                if ($i == 5) {
                  break;
                }
              }
            } else {
              echo "<div id='error'>" . $origin . " is not currently supported by Inspiration Search, please refer to the <a href='https://github.com/amadeus-travel-innovation-sandbox/sandbox-content/blob/master/flight-search-cache-origin-destination.csv' target='_blank'>supported origins</a></div>";
            }
          }
        ?>
      </div>
    </div>

    <footer id="footer">
      <a href="../">www.neilbuckley.co.uk</a>
    </footer>

  </body>
</html>
