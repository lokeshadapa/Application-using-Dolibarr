<!--
* Access Url -http://localhost/nbproject/map_clustering.php.
* Extracts customer's location information using dolibarr api and displays on the map using google 
  geocode api.
-->
<?php
  //create a php array to store locations of all customers.
  $data= array();
  //accessing the dolibarr api and get all the customers list in json format.
  $resource="http://localhost/dolibarr/api/index.php/customer/list?api_key=f74ed53dc0bab4c48e5b01ba6d421df7bf3f812a";
  $out = @file_get_contents($resource);
  //decoding the text to json format.
  $result=@json_decode($out,true);
  //if no response from dolibarr server, then raise an error.
  if($result[0] === null )
  {
    echo "No response from Dolibarr";  
  }
  //after receiving response.
  else
  {
    //foreach customer in the response
    for($i=0;$i<sizeof($result);$i++)
    {
      //create uri for google geocode api by extracting customer "zip" and "country".
      $geocode="https://maps.googleapis.com/maps/api/geocode/json?address=".$result[$i]['zip'].",".$result[$i]['country']."&key=AIzaSyDqW_KdMtIq-PPVbr9p_OzYbk6gOJRMQ9k";
      //get response from google geocode api in text format.
      $outi=file_get_contents($geocode);
      //convert text to json.
      $jsmap=json_decode($outi,true);
      //retrive the lat,long of customer from json response.
      $lat=$jsmap["results"][0]["geometry"]["location"]["lat"];
      $lng=$jsmap["results"][0]["geometry"]["location"]["lng"];
      $data[$i][0]=$lat;
      $data[$i][1]=$lng;
    }
  }


 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Customer Locations</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
        p {font-size: 16px;}
  .margin {margin-bottom: 25px;}
        .bg-1 { 
      background-color: #1abc9c; /* Green */
      color: #ffffff;
  }
  .bg-2 { 
      background-color: #474e5d; /* Dark Blue */
      color: #ffffff;
  }
  .bg-3 { 
      background-color: #ffffff; /* White */
      color: #555555;
  }
  .bg-4 { 
      background-color: #2f2f2f; /* Black Gray */
      color: #fff;
  }
  .container-fluid {
      padding-top: 15px;
      padding-bottom: 15px;
  }
      #map {
        height: 50%;
        width: 50%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>

 <div class="container-fluid bg-1 " style="text-align: center;
vertical-align: middle">
  <h2>Application using Dolibarr</h2>
  </div>

  <div class="bg-3 " style="text-align: center;
vertical-align: middle">    
  <h3 class="margin">Customer Locations</h3><br>
  
</div>
<div id="map" style="margin: 0 auto 0 auto "></div>
<footer class="container-fluid bg-4 text-center"; style= "position: fixed; bottom: 0px; width: 100%; height: 50px;">
  <p>@ <strong>Bhargav, Lokesh, Mohan.</strong></p> 
</footer>
    <script>

         function initMap() {

        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 3,
          center: {lat: 19.024, lng: 70.887}
        });

        // Create an array of alphabetical characters used to label the markers.
        var labels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        // Add some markers to the map.
        // Note: The code uses the JavaScript Array.prototype.map() method to
        // create an array of markers based on a given "locations" array.
        // The map() method here has nothing to do with the Google Maps API.
        var markers = locations.map(function(location, i) {
          return new google.maps.Marker({
             position: new google.maps.LatLng(locations[i][0], locations[i][1]),
            label: labels[i % labels.length]
          });
        });

        // Add a marker clusterer to manage the markers.
        var markerCluster = new MarkerClusterer(map, markers,
            {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
      }
      var locations =  <?php echo json_encode($data) ?>;

    </script>
    <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxX8OyUzQ7NLCCBtjHj9bJv1ewVwxfKhQ&callback=initMap">
    </script>
  </body>
</html>
