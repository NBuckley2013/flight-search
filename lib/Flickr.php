<?php

  class Flickr {

    private $api = "https://api.flickr.com/services/rest/";
    private $key = "FLICKR_API_KEY";

    function searchForImage($city) {
      $path = $this->api . "?method=flickr.photos.search&api_key=" . $this->key . "&text=" . $city . "&tags=city&format=json&nojsoncallback=1&per_page=1&sort=relevance";
      return $path;
    }

    function getImageFromID($id) {
      $path = $this->api . "?method=flickr.photos.getSizes&api_key=" . $this->key . "&photo_id=" . $id . "&format=json&nojsoncallback=1";
      return $path;
    }

    function cURL($method) {
      $header[] = "Authorization: Bearer " . $this->key;
      $header[] = "Content-Type: application/json";

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $method);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
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

  $flickr = new Flickr;

?>
