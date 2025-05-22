<?php 
$servername = "192.168.11.10";
$username = "root";
$password = "r00t";
$dbname = "hospital_dbo";

//create connection

$conn = new mysqli($servername, $username, $password, $dbname);
//check connection

if($conn->connect_error)

{
    die("Connection failed:   ".$conn->connect_error);
}


$sql = "SELECT 
               *
        FROM
                    `hospital_dbo`.`hphicclaimstatus`
                INNER JOIN `hospital_dbo`.`hphicclaimmap` 
                ON (`hphicclaimstatus`.`pClaimSeriesLhio` = `hphicclaimmap`.`pClaimSeriesLhio`)
       
                LEFT JOIN `hospital_dbo`.`hphiclog` 
                ON (`hphicclaimstatus`.`pPIN` = `hphiclog`.`phicnum`)
        
                INNER JOIN `hospital_dbo`.`phictypemem` 
                ON (`hphiclog`.`typemem` = `phictypemem`.`typemem`)" ;
                
    $result = $conn->query($sql);

    if ($result->num_rows > 0 )
{
    //output data of each row

        while($row = $result ->fetch_assoc()){

            echo "<br> ISA GWAPA:".$row["phicnum"]. "Lastname" . $row["memlast"] . " Firstname" . $row["memfirst"] . "<br>";

        }
}
        else
        
        {
            echo "0 results";
        }
    

?>