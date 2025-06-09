<?php 
require __DIR__ . '../db.php';

$search='';

if(isset($_GET['search'])){
    $search=trim($_GET['search']);
    $sql='SELECT r.regcode, r.partID, r.regDate, r.regFPaid, r.regPMode, p.partID, p.partFName, p.partLName FROM registration r JOIN participants p ON r.partID =p.partID WHERE p.partFName LIKE ? OR p.partLName LIKE ? ORDER BY r.regDate Desc';
    $stmt=$conn->prepare($sql);
    $param="%".$search."%";
    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();
    $result=$stmt->get_result();
}else{
    $sql='SELECT r.regcode, r.partID, r.regDate, r.regFPaid, r.regPMode, p.partID, p.partFName, p.partLName FROM registration r JOIN participants p ON r.partID =p.partID ORDER BY r.regDate DESC';
    $result=$conn->query($sql);
}

$eventData=[];

if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        $eventData[]=$row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="registration.php" method="get">
        <input type="text" name="search" id="search" palceholder="search event">
        <button type="submit">search</button>
    </form>
    <table>
        <thead>
            <th>date</th>
            <th>paid</th>
            <th>mode</th>
            <th>first</th>
            <th>last</th>
        </thead>
        <tbody>
            <?php if(!empty($eventData)): ?>
                <?php foreach($eventData as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars ($event['regDate']); ?></td>
                        <td><?php echo htmlspecialchars ($event['regFPaid']); ?></td>
                        <td><?php echo htmlspecialchars ($event['regPMode']); ?></td>
                        <td><?php echo htmlspecialchars ($event['partFName']); ?></td>
                        <td><?php echo htmlspecialchars ($event['partLName']); ?></td>  
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td>no data found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>