<!--
* Access Url - http://localhost/nbproject/top_five_cus.php.
* Extracts customer's order information using dolibarr api and sorts the customer based on total order amount
* Displays top five customers using google chart api.
-->
 <?php
 //Given a Uri, returns a json object.
function get_order($resource)
{
  $out = @file_get_contents($resource);
  $result=@json_decode($out,true);
  return $result;
}
//stores an array of objects each containing customer name and total order amount
function con_arr($arr)
{
    $temp = array();
    $p=0;
  foreach ($arr as $key => $value) 
  {
    # code...
      $temp[$p][0]=$key;
      $temp[$p][1]=$value;
      $p++;
  }
  return $temp;
}

$data= array();
  //create uri for dolibarr api
  $resource="http://localhost/dolibarr/api/index.php/customer/list?api_key=f74ed53dc0bab4c48e5b01ba6d421df7bf3f812a";
  //receive the response in json format.
  $result=get_order($resource);
  //if no response from dolibarr server, then raise an error.
  if($result[0] === null )
  {
    echo "No response from Dolibarr(There is no customer list available)";  
  }
   //after receiving response.
  else
  {
    $arr = array('xyz' => 0);
    //for each customer in the customer list extract his order details using dolibarr api
    for($i=0;$i<sizeof($result);$i++)
    {
        //create uri for dolibarr api
        $k=$i+1;
        $resource="http://localhost/dolibarr/api/index.php/customer/".$k."/order/list?api_key=f74ed53dc0bab4c48e5b01ba6d421df7bf3f812a";
        //receive the response in json format.
        $result2=get_order($resource);
       
          //if no response from dolibarr server, then raise an error.
          if($result2[0]===null )
        {
          //echo "There are no orders for customer :: ".$result[$i]["nom"];
          //echo "<br/>";
        }
        //after receiving orders list for a particular customer sum all his order amounts
        else
        {
          $amt=0;
          for ($j=0; $j <sizeof($result2) ; $j++)
           { 
          # code...
            $amt+= $result2[$j]["total_ttc"];
          }
          //store the total amount for a customer i with key as customer name and amoun as value.
          $arr[$result[$i]["nom"]]=$amt;


        }
 
    }
    //sort the map in descending order based on total order amount.
    arsort($arr);
    //convert the map to list of array objects.
    //each object containing customer name and toal order amount.
    $final_arr=con_arr($arr);
  }


 ?>

<!DOCTYPE html>
<html>
<head>
  <title>Top 5 customers</title>

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
  <h3 class="margin">Top Five Customers</h3><br>

   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <div id="chart_div"></div>

 <footer class="container-fluid bg-4 text-center"; style= "position: fixed; bottom: 0px; width: 100%; height: 50px;">
  <p>@<strong>Bhargav, Lokesh, Mohan.</strong></p> 
</footer>

     <script> 

      google.charts.load('current', {packages: ['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawBasic);

function drawBasic() {

      var data = google.visualization.arrayToDataTable([
        ['Customer', 'amount',],
        [<?php echo json_encode($final_arr[0][0])?>, <?php echo json_encode($final_arr[0][1])?>],
        [<?php echo json_encode($final_arr[1][0])?>, <?php echo json_encode($final_arr[1][1])?>],
        [<?php echo json_encode($final_arr[2][0])?>, <?php echo json_encode($final_arr[2][1])?>],
        [<?php echo json_encode($final_arr[3][0])?>, <?php echo json_encode($final_arr[3][1])?>],
        [<?php echo json_encode($final_arr[4][0])?>, <?php echo json_encode($final_arr[4][1])?>],
      ]);

      var options = {
        title: 'Total amount of orders places by customers',
        chartArea: {width: '50%'},
        hAxis: {
          //title: 'Total Population',
          minValue: 0
        },
        vAxis: {
          //title: 'City'
        }
      };

      var chart = new google.visualization.BarChart(document.getElementById('chart_div'));

      chart.draw(data, options);
    }

    </script>
  </body>
  </html>