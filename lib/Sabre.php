<?php

  class Sabre {

    private $api = "https://api.test.sabre.com";
    private $key = "SABRE_API_KEY";

    function getAirport($query) {
      $path = $this->api . "/v1/lists/utilities/geoservices/autocomplete?query=" . $query;
      return $path;
    }

    function getAirline($code) {
      $path = $this->api . "/v1/lists/utilities/airlines?airlinecode=" . $code;
      return $path;
    }

    function cURL($method, $request) {
      $header[] = "Authorization: Bearer " . $this->key;
      $header[] = "Content-Type: application/json";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $method);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
      if ($request == "POST") {
        $data_string = $method;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      }
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_ENCODING,'gzip');
      curl_setopt($ch, CURLOPT_REFERER, $this->api);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
      curl_setopt($ch, CURLOPT_POST,true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $content = curl_exec($ch);
      return $content;
    }

  }

  $sabre = new Sabre;

?>
