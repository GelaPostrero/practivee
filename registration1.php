<?php 
require __DIR__ . '../db.php';

$search='';
$filter='';
if(isset($_GET['search'])){
    $search=trim($_GET['search']);
    $sql='SELECT r.regcode, r.partID, r.regDate, r.regFPaid, r.regPMode, p.partID, p.partFName, p.partLName FROM registration r JOIN participants p ON r.partID =p.partID WHERE p.partFName LIKE ? OR p.partLName LIKE ? ORDER BY r.regDate Desc';
    $stmt=$conn->prepare($sql);
    $param="%".$search."%";
    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();
    $result=$stmt->get_result();
}elseif (isset($_GET['filter'])) {
    $filter = trim($_GET['filter']);
    $sql = 'SELECT p.partFName, p.partLName, r.regFPaid, e.evCode, e.evName, e.evRFee
            FROM registration r 
            JOIN participants p ON r.partID = p.partID 
            JOIN events e ON p.evCode = e.evCode 
            WHERE e.evName LIKE ? 
            ORDER BY e.evName ASC';
    $stmt = $conn->prepare($sql);
    $param = "%".$filter."%";
    $stmt->bind_param('s', $param);
    $stmt->execute();
    $result = $stmt->get_result();

}else{
    $sql='SELECT r.regcode, r.partID, r.regDate, r.regFPaid, r.regPMode, p.partID, p.partFName, p.partLName FROM registration r JOIN participants p ON r.partID =p.partID ORDER BY r.regDate DESC';
    $result=$conn->query($sql);
}

$eventData=[];

if($result && $result ->num_rows>0){
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
    <form action="registration1.php" method="get">
        <input type="text" name="search" id="search" palceholder="search event">
        <button type="submit">search</button>
    </form>
        <form action="registration1.php" method="get">
        <input type="text" name="filter" id="filter" palceholder="search event">
        <button type="submit">filter</button>
    </form>
    <table>
        <tbody>
            <?php if(!empty($eventData)): ?>
                <?php if (isset($_GET ['filter'])): ?>
                    <?php
                    $group = [];
                    foreach($eventData as$row){
                        $group[$row['evName']][]=$row;
                    }

                    ?>
                    <?php foreach($group as $eventName => $participants): ?>
                        <tr><th colspan="4"><?php echo htmlspecialchars($eventName); ?></th></tr>
                        <tr>
                            <th>first name</th>
                            <th>last name</th>
                            <th>paid fee</th>
                        </tr>
                        <?php foreach($participants as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['partFName']);?></td>
                                <td><?php echo htmlspecialchars($p['partLName']);?></td>
                                <td><?php echo htmlspecialchars($p['regFPaid']);?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php 
                            $count = count($participants);
                            $sumFees = array_sum(array_column($participants, 'regFPaid'));
                            $evRFee = $participants[0]['evRFee']; 
                            $totalDiscount = ($count * $evRFee) - $sumFees;
                        ?>
                        <tr>
                            <td>COUNT: <?php echo $count; ?></td>
                            <td>SUM of FEES: <?php echo number_format($sumFees, 2); ?></td>
                            <td>TOTAL DISCOUNT: <?php echo number_format($totalDiscount, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <thead>
                        <th>date</th>
                        <th>paid</th>
                        <th>mode</th>
                        <th>first</th>
                        <th>last</th>
                    </thead>
                    <?php foreach($eventData as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars ($event['regDate']); ?></td>
                            <td><?php echo htmlspecialchars ($event['regFPaid']); ?></td>
                            <td><?php echo htmlspecialchars ($event['regPMode']); ?></td>
                            <td><?php echo htmlspecialchars ($event['partFName']); ?></td>
                            <td><?php echo htmlspecialchars ($event['partLName']); ?></td>  
                        </tr>
                    <?php endforeach; ?>
                <?php endif;?>
            <?php else: ?>
                <tr>
                    <td>no data found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

