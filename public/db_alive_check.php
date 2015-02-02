<?php
if( ! $conn = mysql_connect( 'localhost', 'root', '') ){
    echo "ng";
    exit;
}
mysql_select_db('mysql', $conn);

$query  = 'select count(*) as cnt from user';
$result = mysql_query($query);

$row = mysql_fetch_assoc($result);
$count = $row['cnt'];

if ($count > 0) {
    echo "ok";
} else {
    echo "ng";
}

mysql_close($conn);

