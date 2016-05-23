<?php
error_reporting(0);
$username = "";//enter your db username
$password = "";//enter your db password
$hostname = "localhost"; 

//connection to the database
$dbhandle = mysqli_connect($hostname, $username, $password) or die("Unable to connect to MySQL");

//select a database to work with
$selected = mysqli_select_db($dbhandle,"<database_name>") or die("Please change the Database name from the code");//enter your database name


$lat = $_POST['latitude'];
$lng = $_POST['longitude'];
$radius = $_POST['radius'];
$key = $_POST['key'];
$store_name = $_POST['store_name'];
$type = $_POST['type'];


// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$store_name.'".csv');

// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');


//Radar search google api url 
$url="https://maps.googleapis.com/maps/api/place/radarsearch/json?location=".$lat.",".$lng."&radius=".$radius."&types=".$type."&key=".$key;

//Php curl code
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec($ch);
curl_close($ch);

$response = json_decode($response);

$result = count($response->results);
if($result == 0)
{
	echo "The API key has expired please enter a new key.";
}
else
{
for($i=0;$i<$result;$i++)
{
$place_id_url="https://maps.googleapis.com/maps/api/place/details/json?placeid=".$response->results[$i]->place_id."&key=".$key;
//Php curl code
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $place_id_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$response_detail = curl_exec($ch);
curl_close($ch);

$response_detail = json_decode($response_detail);

$name = $response_detail->result->name;
$address = $response_detail->result->formatted_address;
$phone = $response_detail->result->formatted_phone_number;
$lng = $response_detail->result->geometry->location->lng;
$lat = $response_detail->result->geometry->location->lat;
$place_id = $response_detail->result->place_id;
$user_ratings_total = $response_detail->result->rating;
$types = implode(",",$response_detail->result->types);
$website = $response_detail->result->website;

$sql ='INSERT INTO `store_details`(`title`, `address`, `phone`, `lat`, `lng`,  `rating`, `type`, `website`) VALUES ("'.$name.'","'.$address.'","'.$phone.'","'.$lat.'","'.$lng.'","'.$user_ratings_total.'","'.$types.'","'.$website.'")';

$query =mysqli_query($dbhandle, $sql);

}


// output the column headings
fputcsv($output, array('Sr.No','Store Name', 'Store Address', 'Store Phone','Store Latitude','Store Longitude','Store Rating','Store Type',' Store Website'));

$sql ='SELECT `id`, `title`, `address`, `phone`, `lat`, `lng`, `rating`, `type`, `website` FROM `store_details`';

$rows =mysqli_query($dbhandle, $sql);


// loop over the rows, outputting them
while ($row = mysqli_fetch_assoc($rows)) fputcsv($output, $row);

$sql1 ='TRUNCATE TABLE `store_details`';

mysqli_query($dbhandle, $sql1);
		
mysqli_close($dbhandle);

}

?>