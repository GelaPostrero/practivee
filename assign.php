<?php 
require __DIR__ . '../db.php';
date_default_timezone_set('Asia/Manila');

$evCode = $_GET['evCode']??null;
$partID=$_GET['partID']??null;

if(!$evCode || !$partID){
    echo'ivalid';
    exit;
}

$part=$conn->prepare("SELECT partDRate From participants Where partID=?");
$part->bind_param('i', $partID);
$part->execute();
$partresult=$part->get_result();
$parti=$partresult->fetch_assoc();
$discount=$parti['partDRate']??0;

$eve=$conn->prepare("SELECT evRFee From events Where evCode=?");
$eve->bind_param('i', $evCode);
$eve->execute();
$everesult=$eve->get_result();
$event=$everesult->fetch_assoc();
$eventFee=$event['evRFee']??0;

$regFPaid=$eventFee-$discount;
$regDate=DATE('Y-m-d');
$regPMode=$_GET['regPMode']?? 'cash';

$stmt=$conn->prepare("INSERT INTO registration (partID, regDate, regFPaid, regPMode) VALUES (?,?,?,?)");
$stmt->bind_param('isds' , $partID, $regDate, $regFPaid, $regPMode);

if($stmt->execute()){
    $update=$conn->prepare("UPDATE participants SET evCode=? WHERE partID=?");
    $update->bind_param('ii', $evCode, $partID);
    $update->execute();
    echo'
    <p>added and updated</p>
    <button><a href="participant.php">back to participants page</a></button>';
    exit;
}else{
    echo'not added' . $conn->error;
}

$stmt->close();
$conn->close();

?>

assign