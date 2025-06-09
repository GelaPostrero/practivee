<?php 
require __DIR__ . '../db.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $evName=filter_input(INPUT_POST, 'evName');
    $evDate=filter_input(INPUT_POST, 'evDate');
    $evVenue=filter_input(INPUT_POST, 'evVenue');
    $evRFee=filter_input(INPUT_POST, 'evRFee');
    $sql='INSERT INTO events (evName, evDate, evVenue, evRFee)
    VALUES(?,?,?,?)';
    $stmt=$conn->prepare($sql);

    if($stmt){
        $stmt->bind_param('sssd', $evName, $evDate, $evVenue, $evRFee);
        $stmt->execute();
        $stmt->close();
        echo'event added successfully';
    }else{
        echo'event not added' . $conn->error;
    }
}

$search='';

if(isset($_GET['search'])){
    $search=trim($_GET['search']);
    $sql='SELECT * FROM events WHERE evName LIKE ? OR evVenue LIKE ?';
    $stmt=$conn->prepare($sql);
    $param="%".$search."%";
    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();
    $result=$stmt->get_result();
}else{
    $sql='SELECT * FROM events';
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
    <form action="event.php" method="post">
        <input type="text" name="evName" id="evName" required placeholder="enter event name">
        <input type="date" name="evDate" id="evDate" required placeholder="enter event date">
        <input type="text" name="evVenue" id="evVenue" required placeholder="enter venue">
        <input type="number" name="evRFee" id="evRFee" required placeholder="enter event fee">
        <button type="submit">Submit</button>
    </form>
    <form action="event.php" method="get">
        <input type="text" name="search" id="search" palceholder="search event">
        <button type="submit">search</button>
    </form>
    <table>
        <thead>
            <th>evCode</th>
            <th>evName</th>
            <th>evDate</th>
            <th>evVenue</th>
            <th>evRFee</th>
            <th>action</th>
        </thead>
        <tbody>
            <?php if(!empty($eventData)): ?>
                <?php foreach($eventData as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars ($event['evCode']); ?></td>
                        <td><?php echo htmlspecialchars ($event['evName']); ?></td>
                        <td><?php echo htmlspecialchars ($event['evDate']); ?></td>
                        <td><?php echo htmlspecialchars ($event['evVenue']); ?></td>
                        <td><?php echo htmlspecialchars ($event['evRFee']); ?></td>  
                        <td>
                            <a href="edit1.php?evCode=<?php echo $event['evCode']; ?>">EDIT</a>
                            <a href="delete1.php?evCode=<?php echo $event['evCode']; ?>" onclick="return confirm('delete?')">DELETE</a>
                        </td>  
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