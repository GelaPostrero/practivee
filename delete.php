<?php 
require __DIR__ . '../db.php';

if(isset($_GET['partID'])){
    $partID=intval($_GET['partID']);
    $sql="DELETE from participants WHERE partID=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param('i', $partID);
    $stmt->execute();  
}
header('Location: participant.php');
exit;
?>