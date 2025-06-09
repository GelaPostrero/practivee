<?php 
require __DIR__ . '../db.php';

$partID=$_GET['partID'] ?? null;
$evCode=$_POST['evCode'] ?? $_GET['evCode'] ?? NULL;
$regPMode=$_POST['regPMode'] ?? $_GET['regPMode'] ?? 'cash';

if(!$partID){
    echo "invalid  ";
    exit;
}

$partistmt=$conn->prepare("SELECT partFName, partLName, partDRate FROm participants WHERE partID=?");
$partistmt->bind_param('i', $partID);
$partistmt->execute();
$partiResult=$partistmt->get_result();
$parti=$partiResult->fetch_assoc();

$events=[];
$events=$conn->query('SELECT evCode, evName, evRFee FROM events')->fetch_all(MYSQLI_ASSOC);

$evRFee='';
$regFPaid='';
$partDRate=$parti['partDRate']??0;

if($_SERVER['REQUEST_METHOD']==='POST' && $evCode){
    $eventstmt=$conn->prepare("SELECT evRFee From events WHERE evCode=?");
    $eventstmt->bind_param('i', $evCode);
    $eventstmt->execute();
    $eventresult=$eventstmt->get_result();
    $event=$eventresult->fetch_assoc();

    if($event){
        $evRFee=$event['evRFee'];
        $regFPaid=$evRFee-$partDRate;
    }
}
?>

<form method="post" action=""  style="display: column">
    <input type="hidden" name="partID" id="partID" value="<?php echo htmlspecialchars($partID); ?>">
    <input type="text" name="partFName" id="partFName" readonly value="<?php echo htmlspecialchars($parti['partFName']); ?>">
    <input type="text" name="partLName" id="partLName" readonly value="<?php echo htmlspecialchars($parti['partLName']); ?>">
    <select name="evCode" id="evCode" required>
        <option value="">choose event</option>
        <?php foreach($events as $event):?>
            <option value="<?php echo $event['evCode'];?>"<?php echo ($evCode==$event['evCode']) ? 'selected': '';?>>
                <?php echo htmlspecialchars($event['evName']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="text" readonly value="<?php echo htmlspecialchars($evRFee); ?>">
    <input type="text" readonly value="<?php echo htmlspecialchars($partDRate);?>">
    <input type="text" readonly value="<?php echo htmlspecialchars($regFPaid);?>">
    
    <select name="regPMode" id="regPMode" required>
        <option value="">Choose payment method</option>
        <option value="cash" <?php if($regPMode == 'cash') echo 'selected';?>>Cash</option>
        <option value="gcash" <?php if($regPMode == 'gcash') echo 'selected';?>>GCash</option>
        <option value="paymaya" <?php if($regPMode == 'paymaya') echo 'selected';?>>paymaya</option>
    </select>

    <button type="submit">Preview Fee</button>
</form>

<?php if($regFPaid !== ''): ?>
    <form action="assign.php" method="get">
        <input type="hidden" name="evCode" value="<?php echo htmlspecialchars($evCode); ?>">
        <input type="hidden" name="partID" value="<?php echo htmlspecialchars($partID); ?>">
        <input type="hidden" name="regFPaid" value="<?php echo htmlspecialchars($regFPaid); ?>">
        <button type="submit">confirm registration</button>
    </form>
<?php endif; ?>

