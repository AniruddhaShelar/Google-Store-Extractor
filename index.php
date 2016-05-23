<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
<style type="text/css">
 #map_canvas {
 	width: 100%;
    height: 700px;
	
  }

@media print {
  html, body {
    height: auto;
  }

 
}
</style>
    <title>Store Finder</title>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true&libraries=places"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script>
var map = null;
var gmarkers = [];
var service = null;
var myCoordsLenght = 6;
var geocoder = new google.maps.Geocoder();
var infowindow = new google.maps.InfoWindow({size: new google.maps.Size(150,50)});

function initialize() {
	
  map = new google.maps.Map(document.getElementById('map_canvas'), {
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	streetViewControl: true
  });
  var defaultBounds = new google.maps.LatLngBounds(
    new google.maps.LatLng(18.8928676, 72.77590889999999),
    new google.maps.LatLng(19.2716339, 72.98649939999996)
	
  );
  

  map.fitBounds(defaultBounds);

  var input = document.getElementById('target');
  var searchBox = new google.maps.places.SearchBox(input);
  searchBox.setBounds(defaultBounds);
  var markers = [];
  service = new google.maps.places.PlacesService(map);

  google.maps.event.addListener(searchBox, 'places_changed', function() {
    var places = searchBox.getPlaces();
    // alert("getPlaces returns "+places.length+" places");

    for (var i = 0; i < gmarkers.length; i++) {
      gmarkers[i].setMap(null);
    }
    gmarkers = [];
    var bounds = new google.maps.LatLngBounds();
	
   
    
    for (var i = 0, place; place = places[i]; i++) {
      var place = places[i];
 //     createMarker(place);
      bounds.extend(place.geometry.location);
    }
	var mar = places[0];
	var mymarker = new google.maps.Marker({
                    map: map,
                    position: mar.geometry.location,
                    draggable: true 
                });     
	geocoder.geocode({'latLng': mar.geometry.location }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            $('#address').val(results[0].formatted_address);
                            $('#latitude').val(mymarker.getPosition().lat());
                            jQuery('#storename').val(mar.name);
                            jQuery('#place_id').val(mar.place_id);
                            jQuery('#types').val(mar.types);
                            $('#longitude').val(mymarker.getPosition().lng());
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, mymarker);
                        }
                    }
                });

                               
                google.maps.event.addListener(mymarker, 'dragend', function() {

                geocoder.geocode({'latLng': mymarker.getPosition()}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            $('#address').val(results[0].formatted_address);
                            $('#latitude').val(mymarker.getPosition().lat());
                            $('#longitude').val(mymarker.getPosition().lng());
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, mymarker);
                        }
                    }
                });
            });
		gmarkers.push(mymarker);

    map.fitBounds(bounds);
    // if (markers.length == 1) map.setZoom(17);
  });

  google.maps.event.addListener(map, 'bounds_changed', function() {
    var bounds = map.getBounds();
    searchBox.setBounds(bounds);
  });

      }
      google.maps.event.addDomListener(window, 'load', initialize);


    </script>
    <style>
	#listing {
        width: 100%;
        height: 300px;
        overflow: auto;
        
        top: 500px;
        cursor: pointer;
        overflow-x: hidden;
      }
      #search-panel {
        position: absolute;
        top: 5px;
        left: 50%;
        margin-left: -180px;
        width: 350px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }
      #target {
        width: 345px;
      }
      #place_info {
         position: absolute;
        bottom: 30px;
        left: 15%;
        margin-left: -180px;
        width: 345px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
      }
    </style>
  </head>
  <body>
    <div id="search-panel">
      <input id="target" type="text" placeholder="Search the Place">
    </div>
	
    <div id="map_canvas"></div>
    
	
	
	<div id="listing">
	<div id="top_bar"></div>
	
	<div id = "place_info"> 
	<form action="radarsearch.php" method="post">
	<table>
	<tr>
		<td>
		<label>Api Key : </label>
		</td>
		<td>
		<input type="text" name="key" id="key" placeholder="Api Key"/>
		</td>
	</tr>
		<tr>
		<td>
		<label>Place Name :  </label>
		</td>
		<td>
		<input id="storename" type="text" name = "store_name"/>
		</td>
	</tr>
		<tr>
		<td>
		<label>Latitude :  </label>
		</td>
		<td>
		<input type="text" name="latitude" id="latitude" placeholder="Latitude"/>
		</td>
	</tr>
		<tr>
		<td>
		<label>Longitude :  </label>
		</td>
		<td>
		<input type="text" name="longitude" id="longitude" placeholder="Longitude"/>
		</td>
	</tr>
		<tr>
		<td>
		<label>Radius :  </label>
		</td>
		<td>
		<select name="radius" id="radius">
					<?php echo '<option value="1000">1</option>';
					echo '<option value="2000">2</option>';
					echo '<option value="3000">3</option>';
					echo '<option value="5000">5</option>';
					echo '<option value="10000">10</option>';
					?>
	</select>		
		</td>
		</tr>
		<tr>
	<td>
	
	<label>  Type : </label>
	</td>
	<td>
	<select name="type" id="type">
	
					<option value="store">Store</option>
					<option value="accounting">Accounting</option>
					<option value="art_gallery">Art gallery</option>
					<option value="bakery">Bakery</option>
					<option value="beauty_salon">Beauty salon</option>
					<option value="bicycle_store">Bicycle store</option>
					<option value="book_store">Book store</option>
					<option value="cafe">Cafe</option>
					<option value="car_dealer">Car Dealer</option>
					<option value="car_rental">Car Dental</option>
					<option value="car_repair">Car repair</option>
					<option value="car_wash">Car wash</option>
					<option value="clothing_store">Clothing store</option>
					<option value="convenience_store">Convenience store</option>
					<option value="department_store">Department store</option>
					<option value="electrician">Electrician</option>
					<option value="electronics_store">Electronics store</option>
					<option value="moving_company">Moving Company</option>
					<option value="painter">Painter</option>
					<option value="pet_store">Pet Store</option>
					<option value="pharmacy">Pharmacy</option>
					<option value="plumber">Plumber</option>
					<option value="real_estate_agency">Real estate agency</option>
					<option value="restaurant">Restaurant</option>
					<option value="roofing_contractor">Roofing contractor</option>
					<option value="shoe_store">Shoe store</option>
					<option value="shopping_mall">Shopping mall</option>
					<option value="spa">Spa</option>
					<option value="travel_agency">Travel agency</option>
					<option value="veterinary_care">Veterinary care</option>
					<option value="finance">Finance</option>
					<option value="florist">Florist</option>
					<option value="food">Food</option>
					<option value="furniture_store">Furniture store</option>
					<option value="grocery_or_supermarket">Grocery or supermarket</option>
					<option value="gym">Gym</option>
					<option value="hair_care">Hair care</option>
					<option value="hardware_store">Hardware store</option>
					<option value="home_goods_store">Home goods store</option>
					<option value="jewelry_store">Jewelry store</option>
					<option value="library">Library</option>
					<option value="liquor_store">Liquor store</option>
					<option value="local_government_office">Local government office</option>
					<option value="locksmith">Locksmith</option>
					<option value="meal_delivery">Meal delivery</option>
					<option value="meal_takeaway">Meal takeaway</option>
					<option value="movie_rental">Movie rental</option>
					<option value="movie_theater">Movie theater</option>				
	</select>		
		</td>
			<input type="submit" name="submit"/>
	</tr>

	</table>
	<input id="types" type="hidden"/>
	<input id="place_id" type="hidden"/>
	<input id="address" type="hidden"/>
	
	</form>
	</div>
	
	
    </div>
     </body>
</html>	

