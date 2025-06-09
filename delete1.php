<?php 
require __DIR__ . '../db.php';

if(isset($_GET['evCode'])){
    $evCode=intval($_GET['evCode']);
    $sql="DELETE from events WHERE evCode=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('i', $evCode);
    $stmt->execute();  
}
header('Location: event.php');
exit;
?>