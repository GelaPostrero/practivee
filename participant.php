<?php 
require __DIR__ . '../db.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $partFName=filter_input(INPUT_POST, 'partFName');
    $partLName=filter_input(INPUT_POST, 'partFName');
    $partDRate=filter_input(INPUT_POST, 'partDRate');
    $sql='INSERT INTO participants (partFName, partLName, partDRate)
    VALUES(?,?,?)';
    $stmt=$conn->prepare($sql);

    if($stmt){
        $stmt->bind_param('ssd', $partFName, $partLName, $partDRate);
        $stmt->execute();
        $stmt->close();
        echo'participant added successfully';
    }else{
        echo'participant not added' . $conn->error;
    }
}

$search='';

if(isset($_GET['search'])){
    $search=trim($_GET['search']);
    $sql='SELECT * FROM participants WHERE partFName LIKE ? OR partLName LIKE ?';
    $stmt=$conn->prepare($sql);
    $param="%".$search."%";
    $stmt->bind_param('ss', $param, $param);
    $stmt->execute();
    $result=$stmt->get_result();
}else{
    $sql='SELECT * FROM participants';
    $result=$conn->query($sql);
}

$partiData=[];

if($result->num_rows>0){
    while($row=$result->fetch_assoc()){
        $partiData[]=$row;
    }
}

$evCode=$_GET['evCode']?? null;
$evStmt=$conn->prepare("SELECT * FROM events Where evCode=?");
$evStmt->bind_param('i', $evCode);
$evStmt->execute();
$evResult=$evStmt->get_result();
$ev=$evResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="participant.php" method="post">
        <input type="text" name="partFName" id="partLName" required placeholder="enter first name">
        <input type="text" name="partLName" id="partLName" required placeholder="enter last name">
        <input type="number" name="partDRate" id="partDRate" required placeholder="enter discount for particiapnt">
        <button type="submit">Submit</button>
    </form>
    <form action="participant.php" method="get">
        <input type="text" name="search" id="search" palceholder="search participant">
        <button type="submit">search</button>
    </form>
    <table>
        <thead>
            <th>partID</th>
            <th>evCode</th>
            <th>first name</th>
            <th>last name</th>
            <th>discount</th>
            <th>action</th>
        </thead>
        <tbody>
            <?php if(!empty($partiData)): ?>
                <?php foreach($partiData as $parti): ?>
                    <tr>
                        <td><?php echo htmlspecialchars ($parti['partID']); ?></td>
                        <td><?php echo htmlspecialchars ($parti['evCode']); ?></td>
                        <td><?php echo htmlspecialchars ($parti['partFName']); ?></td>
                        <td><?php echo htmlspecialchars ($parti['partLName']); ?></td>
                        <td><?php echo htmlspecialchars ($parti['partDRate']); ?></td>  
                        <td>
                            <a href="edit.php?partID=<?php echo $parti['partID']; ?>">EDIT</a>
                            <a href="delete.php?partID=<?php echo $parti['partID']; ?>" onclick="return confirm('delete?')">DELETE</a>
                            <a href="form.php?evCode=<?php echo $evCode; ?> & partID=<?php echo $parti['partID']; ?>">Add</a>
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