<?php 
require __DIR__ . '../db.php';

$partID=intval($_GET['partID']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    $partFName=$_POST['partFName'];
    $partLName=$_POST['partLName'];
    $partDRate=$_POST['partDRate'];
    $stmt=$conn->prepare("UPDATE participants SET partFName=?, partLName=?, partDRate=? WHERE partID=?");
    $stmt->bind_param('ssdi', $partFName, $partLName, $partDRate, $partID);
    $stmt->execute();
    header('Location: participant.php');
}

$stmt=$conn->prepare("SELECT *FROM participants where partID=?");
$stmt->bind_param('i', $partID);
$stmt->execute();
$result=$stmt->get_result();
$part=$result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="post">
        <input type="text" name="partFName" id="partLName" required value="<?php echo htmlspecialchars($part['partFName']); ?>">
        <input type="text" name="partLName" id="partLName" required value="<?php echo htmlspecialchars($part['partLName']); ?>">
        <input type="number" name="partDRate" id="partDRate" required value="<?php echo htmlspecialchars($part['partDRate']); ?>">
        <button type="submit">Submit</button>
    </form>
</body>
</html>