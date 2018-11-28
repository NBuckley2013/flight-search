<?php
  session_start();
  require "lib/db.php";
  require "lib/Sabre.php";
  require "lib/Amadeus.php";

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
  if (isset($_GET["origin"])) {
    $origin = $_GET["origin"];
  }
  if (isset($_POST["destination"])) {
    $destination = $_POST["destination"];
  }
  if (isset($_GET["destination"])) {
    $destination = $_GET["destination"];
  }
  if (isset($_POST["depart_date"])) {
    $departDate = $_POST["depart_date"];
  }
  if (isset($_GET["depart_date"])) {
    $departDate = $_GET["depart_date"];
  }
  if (isset($_POST["return_date"])) {
    $returnDate = $_POST["return_date"];
  }
  if (isset($_GET["return_date"])) {
    $returnDate = $_GET["return_date"];
  }
  if (isset($_POST["one_way"])) {
    $oneWay = $_POST["one_way"];
  }
  if (isset($_GET["one_way"])) {
    $oneWay = $_GET["one_way"];
  }
  if (isset($_POST["direct"])) {
    $direct = $_POST["direct"];
  }
  if (isset($_GET["direct"])) {
    $direct = $_GET["direct"];
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="js/jQuery.js" type="text/javascript"></script>
    <link rel="icon" href="favicon.png">
    <title>Flight Search</title>
    <link rel="stylesheet" href="https://code.cdn.mozilla.net/fonts/fira.css">
    <link rel="stylesheet" type="text/css" href="css/styling.css"/>
    <link rel="stylesheet" type="text/css" href="css/dark.css"/>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#282C34">
  </head>

  <body class="flight_search">

    <div id="wrapper">
      <div id="header">
        <link rel="icon" href="favicon.ico" />
        <link rel="shortcut icon" href="favicon.ico" />
        <div id="header_text">
          <select id="select" onchange="changeTheme(this.value)">
            <option id="dark" value="dark">Dark</option>
            <option id="light" value="light">Light</option>
          </select>

          <select id="select" onchange="location = this.value;">
            <option value="">Flight Search</option>
            <option value="inspiration_search.php">Inspiration Search</option>
            <option value="./">About</option>
          </select>
        </div>
        <div id="currency">
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
        </div>
      </div>

      <!-- Change theme -->
      <script type="text/javascript">
        if (localStorage.getItem('css')) {
          changeTheme(localStorage.getItem('css'));
        }

        function changeTheme(css) {
          if (css == null) { var css = "dark" };
          localStorage.setItem("css", css);

          var currentCSS = document.getElementsByTagName("link").item(3);

          var newCSS = document.createElement("link");
          newCSS.setAttribute("rel", "stylesheet");
          newCSS.setAttribute("type", "text/css");
          newCSS.setAttribute("href", "css/" + css + ".css");

          document.getElementsByTagName("head").item(0).replaceChild(newCSS, currentCSS);

          if (css == "dark") {
            document.getElementById("dark").selected = true;
          } else {
            document.getElementById("light").selected = true;
          }
        }
      </script>

      <div id="container">
        <?php
          $imagesDir = "img/airport-codes/images/large/";
          $images = glob($imagesDir . "*.{jpg}", GLOB_BRACE);
          $randomImage = $images[array_rand($images)];
        ?>

        <div id="navigation" style="background-image: url(<?php echo $randomImage; ?>)">
          <form action="flight_search.php" method="post">
            <div id="navigation_options">
              <input type="checkbox" name="direct" value="true" <?php if(isset($direct) && $direct == "true") echo "checked";?> >Direct
              <input type="radio" name="one_way" value="false" <?php if(isset($oneWay) && $oneWay == "false") echo "checked";?> >Return
              <input type="radio" name="one_way" value="true" <?php if(isset($oneWay) && $oneWay == "true") echo "checked";?> >One-Way
            </div><br>
            <input type="text" name="origin" id="origin" autocomplete="off" value="<?php if(isset($origin)) echo $origin ?>" size="16" placeholder="Origin" required>
            <input type="text" name="destination" id="destination" autocomplete="off" value="<?php if(isset($destination)) echo $destination ?>" size="16" placeholder="Destination" required>
            <input type="text" name="depart_date" id="depart_date" autocomplete="off" value="<?php if(isset($departDate)) echo $departDate ?>" size="16" placeholder="Depart Date" required>
            <input type="text" name="return_date" id="return_date" autocomplete="off" value="<?php if(isset($returnDate)) echo $returnDate ?>" size="16" placeholder="Return Date">
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
            if ($oneWay == "false" && empty($returnDate)) {
              echo "<div id='error'>Please select a return date</div>";
            } elseif ($oneWay == "false" && $departDate > $returnDate) {
              echo "<div id='error'>Return date must be greater than departure date</div>";
            } else {
              if ($oneWay == "true") {
                $flights = json_decode($amadeus->cURL($amadeus->getLowFareFlightsOneWay($origin, $destination, $departDate, $_SESSION["currency"], $direct), "GET"), true);
              } else {
                $flights = json_decode($amadeus->cURL($amadeus->getLowFareFlights($origin, $destination, $departDate, $returnDate, $_SESSION["currency"], $direct), "GET"), true);
              }

              if (array_key_exists("results", $flights)) {
                $i = 0;
                foreach ($flights["results"] as $results) {
                  $price = $results["fare"]["total_price"];
                  foreach ($results["itineraries"] as $itineraries) {

                    $originAirport = current($itineraries["outbound"]["flights"]);
                    $originAirportCode = $originAirport["origin"]["airport"];
                    $destinationAirport = end($itineraries["outbound"]["flights"]);
                    $destinationAirportCode = $destinationAirport["destination"]["airport"];

                    // get airport image
                    foreach ($airportImageDir as $file) {
                      if (substr($file->getFilename(), 0, 3) == strtolower($destinationAirportCode)) {
                        $destinationAirportImage = $airportImageDir->getPath() . $file;
                      }
                    }

                    // get city name
                    $originCity = json_decode($amadeus->cURL($amadeus->getCity($origin), "GET"), true);
                    $destinationCity = json_decode($amadeus->cURL($amadeus->getCity($destination), "GET"), true);

                    if (array_key_exists("city", $originCity)) {
                      $originName = $originCity["city"]["name"];
                    } elseif (array_key_exists("airports", $originCity)) {
                        foreach ($originCity["airports"] as $airport) {
                          if ($airport["code"] == $origin) {
                            $originName = $airport["city_name"];
                          }
                        }
                    } else {
                        $originName = $origin;
                    }
                    if (array_key_exists("city", $destinationCity)) {
                      $destinationName = $destinationCity["city"]["name"];
                    } elseif (array_key_exists("airports", $destinationCity)) {
                        foreach ($destinationCity["airports"] as $airport) {
                          if ($airport["code"] == $destination) {
                            $destinationName = $airport["city_name"];
                          }
                        }
                    } else {
                        $destinationName = $destination;
                    }

                    if ($oneWay == "false") {
                      $arrow = " ⇄ ";
                    } else {
                      $arrow = " → ";
                    }

                    echo "<div class='flights_container' style='background-image: url(" . $destinationAirportImage . "); background-repeat: no-repeat; background-size: cover; background-position: center'>";
                    echo "<div class='route'>" . $originName . $arrow . $destinationName . "</div><div class='flights'>";
                    foreach ($itineraries["outbound"] as $outbound) {
                      foreach ($outbound as $flight) {
                        $outboundDepart = $flight["departs_at"];
                        $outboundArrival = $flight["arrives_at"];
                        $outboundOriginAirport = $flight["origin"]["airport"];
                        if (array_key_exists("terminal", $flight["origin"])) {
                          $outboundOriginTerminal = $flight["origin"]["terminal"];
                        }
                        $outboundDestinationAirport = $flight["destination"]["airport"];
                        if (array_key_exists("terminal", $flight["destination"])) {
                          $outboundDestinationTerminal = $flight["destination"]["terminal"];
                        }
                        $outboundMarketingAirline = $flight["marketing_airline"];
                        $outboundOperatingAirline = $flight["operating_airline"];
                        $outboundFlightNumber = $flight["flight_number"];
                        $outboundAircraft = $flight["aircraft"];
                        $outboundTravelClass = $flight["booking_info"]["travel_class"];
                        $outboundBookingCode = $flight["booking_info"]["booking_code"];
                        $outboundSeatsRemaining = $flight["booking_info"]["seats_remaining"];

                        // get airport name
                        $originAirport = json_decode($amadeus->cURL($amadeus->getCity($outboundOriginAirport), "GET"), true);
                        $destinationAirport = json_decode($amadeus->cURL($amadeus->getCity($outboundDestinationAirport), "GET"), true);

                        if (array_key_exists("airports", $originAirport)) {
                          foreach ($originAirport["airports"] as $airport) {
                            if ($airport["code"] == $outboundOriginAirport) {
                              $originAirportName = $airport["name"];
                            }
                          }
                        } else {
                          $originAirportName = "";
                        }

                        if (array_key_exists("airports", $destinationAirport)) {
                          foreach ($destinationAirport["airports"] as $airport) {
                            if ($airport["code"] == $outboundDestinationAirport) {
                              $destinationAirportName = $airport["name"];
                            }
                          }
                        } else {
                          $destinationAirportName = "";
                        }

                        // get airline logos
                        foreach ($airlineLogoDir as $logo) {
                          if ($logo->getFilename() == $outboundOperatingAirline . ".png") {
                            $airlineLogo = $airlineLogoDir->getPath() . $logo->getFilename();
                          } elseif (!$logo->getFilename() == $outboundOperatingAirline . ".png") {
                            $airlineLogo = "";
                          }
                        }

                        // get airline names
                        $airlineInfo = json_decode($sabre->cURL($sabre->getAirline($outboundOperatingAirline), "GET"), true);
                        if (array_key_exists("AirlineInfo", $airlineInfo)) {
                          foreach ($airlineInfo["AirlineInfo"] as $name) {
                            if ($name["AlternativeBusinessName"] > $name["AirlineName"]) {
                              $airlineName = $name["AlternativeBusinessName"];
                            } else {
                            $airlineName = $name["AirlineName"];
                            }
                          }
                        } else {
                          $airlineName = $outboundOperatingAirline;
                        }

                        echo "<table class='flight_details'><tr><td colspan='2' style='padding-right: 10px'>";
                        echo '<span class="airport_name" title="' . $originAirportName . '">' . $outboundOriginAirport . '</span> → ' . '<span class="airport_name" title="' . $destinationAirportName . '">' . $outboundDestinationAirport . '</span></td>';
                        echo '<td align="right"><span title="' . $airlineName . '"><img class="airline_logo" src="' . $airlineLogo . '" width="128" height="37"/></span></td></tr>';
                        echo "<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>";
                        echo "<tr><td align='left'>Date: </td><td align='right' colspan='2'>" . date("l jS F", strtotime($outboundDepart)) . "</td></tr>";
                        echo "<tr><td align='left'>Depart: </td><td align='right' colspan='2'>" . date("g:ia", strtotime($outboundDepart)) . "</td></tr>";
                        echo "<tr><td align='left'>Arrival: </td><td align='right' colspan='2'>" . date("g:ia", strtotime($outboundArrival)) . "</td></tr>";
                        echo "<tr><td align='left'>Flight #: </td><td align='right' colspan='2'>" . $outboundFlightNumber . "</td></tr>";
                        echo "<tr><td align='left'>Class: </td><td align='right' colspan='2'>" . ucfirst(strtolower($outboundTravelClass)) . "</td></tr>";
                        echo "<tr><td align='left'>Availability: </td><td align='right' colspan='2'>" . $outboundSeatsRemaining . "</td></tr>";
                        echo "</table>";
                      }
                    }
                    if (array_key_exists("inbound", $itineraries)) {
                        foreach ($itineraries["inbound"] as $inbound) {
                        foreach ($inbound as $flight) {
                          $inboundDepart = $flight["departs_at"];
                          $inboundArrival = $flight["arrives_at"];
                          $inboundOriginAirport = $flight["origin"]["airport"];
                          if (array_key_exists("terminal", $flight["origin"])) {
                            $inboundOriginTerminal = $flight["origin"]["terminal"];
                          }
                          $inboundDestinationAirport = $flight["destination"]["airport"];
                          if (array_key_exists("terminal", $flight["destination"])) {
                            $inboundDestinationTerminal = $flight["destination"]["terminal"];
                          }
                          $inboundMarketingAirline = $flight["marketing_airline"];
                          $inboundOperatingAirline = $flight["operating_airline"];
                          $inboundFlightNumber = $flight["flight_number"];
                          $inboundAircraft = $flight["aircraft"];
                          $inboundTravelClass = $flight["booking_info"]["travel_class"];
                          $inboundBookingCode = $flight["booking_info"]["booking_code"];
                          $inboundSeatsRemaining = $flight["booking_info"]["seats_remaining"];

                          // get airport name
                          $originAirport = json_decode($amadeus->cURL($amadeus->getCity($inboundOriginAirport), "GET"), true);
                          $destinationAirport = json_decode($amadeus->cURL($amadeus->getCity($inboundDestinationAirport), "GET"), true);

                          if (array_key_exists("airports", $originAirport)) {
                            foreach ($originAirport["airports"] as $airport) {
                              if ($airport["code"] == $inboundOriginAirport) {
                                $originAirportName = $airport["name"];
                              }
                            }
                          } else {
                            $originAirportName = "";
                          }

                          if (array_key_exists("airports", $destinationAirport)) {
                            foreach ($destinationAirport["airports"] as $airport) {
                              if ($airport["code"] == $inboundDestinationAirport) {
                                $destinationAirportName = $airport["name"];
                              }
                            }
                          } else {
                            $destinationAirportName = "";
                          }

                          // get airline logos
                          foreach ($airlineLogoDir as $logo) {
                            if ($logo->getFilename() == $inboundOperatingAirline . ".png") {
                              $airlineLogo = $airlineLogoDir->getPath() . $logo->getFilename();
                            } elseif (!$logo->getFilename() == $inboundOperatingAirline . ".png") {
                              $airlineLogo = "";
                            }
                          }

                          // get airline names
                          $airlineInfo = json_decode($sabre->cURL($sabre->getAirline($inboundOperatingAirline), "GET"), true);
                          if (array_key_exists("AirlineInfo", $airlineInfo)) {
                            foreach ($airlineInfo["AirlineInfo"] as $name) {
                              if ($name["AlternativeBusinessName"] > $name["AirlineName"]) {
                                $airlineName = $name["AlternativeBusinessName"];
                              } else {
                              $airlineName = $name["AirlineName"];
                              }
                            }
                          } else {
                            $airlineName = $inboundOperatingAirline;
                          }

                          echo "<table class='flight_details'><tr><td colspan='2' style='padding-right: 10px'>";
                          echo '<span class="airport_name" title="' . $originAirportName . '">' . $inboundOriginAirport . '</span> → ' . '<span class="airport_name" title="' . $destinationAirportName . '">' . $inboundDestinationAirport . '</span></td>';
                          echo '<td align="right"><span title="' . $airlineName . '"><img class="airline_logo" src="' . $airlineLogo . '" width="128" height="37"/></span></td></tr>';
                          echo "<tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>";
                          echo "<tr><td align='left'>Date: </td><td align='right' colspan='2'>" . date("l jS F", strtotime($inboundDepart)) . "</td></tr>";
                          echo "<tr><td align='left'>Depart: </td><td align='right' colspan='2'>" . date("g:ia", strtotime($inboundDepart)) . "</td></tr>";
                          echo "<tr><td align='left'>Arrival: </td><td align='right' colspan='2'>" . date("g:ia", strtotime($inboundArrival)) . "</td></tr>";
                          echo "<tr><td align='left'>Flight #: </td><td align='right' colspan='2'>" . $inboundFlightNumber . "</td></tr>";
                          echo "<tr><td align='left'>Class: </td><td align='right' colspan='2'>" . ucfirst(strtolower($inboundTravelClass)) . "</td></tr>";
                          echo "<tr><td align='left'>Availability: </td><td align='right' colspan='2'>" . $inboundSeatsRemaining . "</td></tr>";
                          echo "</table>";
                        }
                      }
                    }
                    if ($_SESSION["currency"] == "GBP") {
                      $currency = "£";
                    }
                    if ($_SESSION["currency"] == "EUR") {
                      $currency = "€";
                    }
                    if ($_SESSION["currency"] == "USD") {
                      $currency = "$";
                    }
                    echo "</div><div class='price'>" . $currency . $price . "</div></div><br>";
                  }

                  // break after 5th result
                  $i++;
                  if ($i == 5) {
                    break;
                  }
                }
              } else {
                echo "<div id='error'>No flights found</div>";
              }
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
