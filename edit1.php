<?php 
require __DIR__ . '../db.php';

$evCode=intval($_GET['evCode']);
if($_SERVER['REQUEST_METHOD']==='POST'){
    $evName=$_POST['evName'];
    $evDate=$_POST['evDate'];
    $evVenue=$_POST['evVenue'];
    $evRFee=$_POST['evRFee'];
    $stmt=$conn->prepare("UPDATE events SET evName=?, evDate=?, evVenue=?, evRFee=? WHERE evCode=?");
    $stmt->bind_param('sssdi', $evName, $evDate, $evVenue, $evRFee, $evCode);
    $stmt->execute();
    header('Location: event.php');
}

$stmt=$conn->prepare("SELECT *FROM events where evCode=?");
$stmt->bind_param('i', $evCode);
$stmt->execute();
$result=$stmt->get_result();
$even=$result->fetch_assoc();
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
        <input type="text" name="evName" id="evDate" required value="<?php echo htmlspecialchars($even['evName']); ?>">
        <input type="date" name="evDate" id="evDate" required value="<?php echo htmlspecialchars($even['evDate']); ?>">
        <input type="text" name="evVenue" id="evVenue" required value="<?php echo htmlspecialchars($even['evVenue']); ?>">
        <input type="number" name="evRFee" id="evRFee" required value="<?php echo htmlspecialchars($even['evRFee']); ?>">
        <button type="submit">Submit</button>
    </form>
</body>
</html>