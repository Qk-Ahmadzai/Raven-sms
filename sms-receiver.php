
<!DOCTYPE html>
<html>
    <head>
        <title>Page Title</title>


    <script type="text/javascript">

        function sendDatatoSever(data){
           // alert('In test Function');
            console.log(data)
        }

    </script>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    </head>
<body>


<?php


date_default_timezone_set('UTC');

require_once __DIR__.'/hilink.class.php';

$url1=$_SERVER['REQUEST_URI'];
header("Refresh: 3; URL=$url1");

$hilink = AMWD\HiLink::create();

echo "<div class='container'>";
echo "<div class='btn btn-info'> <strong>Host : </strong>".$hilink->getHost().PHP_EOL."</div>";
echo "</br>";

//if (!$hilink->online()) {
	//echo "not online".PHP_EOL;
	//exit;
//}
echo "</br></br>";



$unread = $hilink->listUnreadSms();

echo "<div class='alert alert-warning'> <strong>Unread sms</strong> </div>";
echo "<table class='table table-striped'> <tr>
<th>SMS ID</th>
<th>Number</th>
<th>Message</th>
<th>Status</th>
<th>Time</th>
 </tr>";

 for($i=0; $i< count($unread); ++$i){

     echo "<tr>
         <td>" .$unread[$i]['idx'] ."</td>
         <td>" .$unread[$i]['number'] ."</td>
         <td>" .$unread[$i]['msg'] ."</td>
         <td>" .$unread[$i]['read'] ."</td>
         <td>" .$unread[$i]['time'] ."</td>
      </tr>";

    
 }

echo "</table>";


echo "<hr></br></br>";



$url = 'http://127.0.0.1:8000/api/insms?api_token=FkOHFBvf30COPSJtOupL9vk3heH51hvG5BjbYgRf2AeWYR6cU4isYZQSOMSF';
///////////////////////////////////////////////////////////////////////////////////
/////////////////// * Send Data to datbase * //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

   static $servername = "localhost";
   static $username = "root";
   static $password = "";
   static $conn;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=raven", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='btn btn-success'> <strong>Database : </strong>  Connected successfully! </div>";
    }catch(PDOException $e){
        echo $sql . "<br>" . $e->getMessage();
    }

    try{
        $sel_query = "SELECT * FROM contacts";
        $contacts = $conn->query($sel_query);
        
    }catch(PDOException $e){
        echo "Error in Select Query";
    }



    for($i=0; $i< count($unread); ++$i){

            $id = $unread[$i]['idx'];
            $number = $unread[$i]['number'];

            $msg = $unread[$i]['msg'];
            $time = $unread[$i]['time'];

            $count = count( explode(":", $msg));
            
            if($count >= 3){
                echo "Count : ". $count;
                $arr = explode(":", $msg);
                $latlng = explode(",", $arr[0] );
                print_r ( $arr );
                echo "<br>";
                print_r ( $latlng );
                echo "<br>";

                if (count( (array)$contacts ) > 0) {
                    // output data of each row
                    while($row = $contacts->fetch()) {
                        if($number ==  $row["number"]){
                            echo "<br>id: " . $row["id"]. " - Name: " . $row["name"]. " Number:" . $row["number"]. "<br>";
                            $sql = "INSERT INTO sms(contact_id, status, priority, message, lat, lng, time)
                            VALUES ('" . $row["id"] ."', '" . $arr[1] ."', '" . $arr[2] ."', '" . $arr[3] ."', '" . $latlng[1] ."','".  $latlng[0] ."','". $time ."')";
                            $conn->exec($sql);
                            $hilink->setSmsRead( $id );
                        }
                     
                    }
                }
               // $hilink->setSmsRead( $id );

                $data = array( $arr[1],  $arr[2], $arr[3], $latlng[0],  $latlng[1],  $time );
                PHPFunction($data);
               
            }
        
    }





///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////





echo "</br> </br> </br>";


$out =  $hilink->getSmsForDb(1, true);

//for($i=0; $i< count($out); ++$i){
//    PHPFunction($out);
    //echo '<script type="text/javascript">' .  'sendDatatoSever();' . '</script>';
//}

echo "<div class='alert alert-info'> <strong>Inbox</strong> </div>";
echo "<table  class='table table-striped'> <tr>
<th>SMS ID</th>
<th>Number</th>
<th>Message</th>
<th>Status</th>
<th>Time</th>
 </tr>";

 for($i=0; $i< count($out); ++$i){

     echo "<tr>
         <td>" .$out[$i]['idx'] ."</td>
         <td>" .$out[$i]['number'] ."</td>
         <td>" .$out[$i]['msg'] ."</td>
         <td>" .$out[$i]['read'] ."</td>
         <td>" .$out[$i]['time'] ."</td>
      </tr>";
 }

echo "</table></div>";


function PHPFunction($out)
{
        echo '<script type="text/javascript"> sendDatatoSever('. json_encode($out) .'); </script>'; 
}



?>



  </body>
</html>