<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "books_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);

// Select the database
$conn->select_db($dbname);

// Create books table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS books (
    isbn VARCHAR(20) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    copyright YEAR,
    edition VARCHAR(50),
    price DECIMAL(10,2),
    quantity INT
)";
$conn->query($sql);

$message = "";

// Handle form submissions
if ($_POST) {
    if (isset($_POST['search'])) {
        $search_isbn = $_POST['isbn'];
        $result = $conn->query("SELECT * FROM books WHERE isbn = '$search_isbn'");
        if ($result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $message = "ITEM IS FOUND";
        } else {
            $message = "ITEM NOT FOUND";
        }
    } elseif (isset($_POST['add'])) {
        $isbn = $_POST['isbn'];
        $title = $_POST['title'];
        $copyright = $_POST['copyright'];
        $edition = $_POST['edition'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        
        if (empty($isbn)) {
            $message = "NO RECORD TO ADD";
        } else {
            $check = $conn->query("SELECT * FROM books WHERE isbn = '$isbn'");
            if ($check->num_rows > 0) {
                $message = "RECORD ALREADY EXISTS";
            } else {
                $sql = "INSERT INTO books (isbn, title, copyright, edition, price, quantity) VALUES ('$isbn', '$title', '$copyright', '$edition', '$price', '$quantity')";
                if ($conn->query($sql)) {
                    $message = "RECORD SUCCESSFULLY SAVED";
                }
            }
        }
    } elseif (isset($_POST['edit'])) {
        $isbn = $_POST['isbn'];
        $title = $_POST['title'];
        $copyright = $_POST['copyright'];
        $edition = $_POST['edition'];
        $price = $_POST['price'];
        $quantity = $_POST['quantity'];
        
        if (empty($isbn)) {
            $message = "NO RECORD TO EDIT";
        } else {
            $check = $conn->query("SELECT * FROM books WHERE isbn = '$isbn'");
            if ($check->num_rows == 0) {
                $message = "ISBN IS NOT FOUND";
            } else {
                $sql = "UPDATE books SET title='$title', copyright='$copyright', edition='$edition', price='$price', quantity='$quantity' WHERE isbn='$isbn'";
                if ($conn->query($sql)) {
                    $message = "RECORD SUCCESSFULLY UPDATED";
                }
            }
        }
    } elseif (isset($_POST['delete'])) {
        $isbn = $_POST['isbn'];
        if (empty($isbn)) {
            $message = "NO RECORD TO DELETE";
        } else {
            $check = $conn->query("SELECT * FROM books WHERE isbn = '$isbn'");
            if ($check->num_rows == 0) {
                $message = "ISBN IS NOT FOUND";
            } else {
                $sql = "DELETE FROM books WHERE isbn = '$isbn'";
                if ($conn->query($sql)) {
                    $message = "RECORD SUCCESSFULLY DELETED";
                }
            }
        }
    }
}

// Get all books for display
$all_books = $conn->query("SELECT * FROM books");
$total_books = 0;
$grand_total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Inventory System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        input[type="text"], input[type="number"] { width: 150px; padding: 5px; margin: 2px; }
        input[type="submit"] { padding: 8px 15px; margin: 5px; cursor: pointer; }
        .form-section { margin: 20px 0; }
        .prompt { background-color: #f9f9f9; padding: 10px; border: 1px solid #ccc; margin: 10px 0; }
        .buttons { margin: 10px 0; }
    </style>
</head>
<body>

<h2>Book Inventory Management System</h2>

<div class="form-section">
    <form method="POST">
        <table>
            <tr>
                <td><strong>ISBN #:</strong></td>
                <td><input type="text" name="isbn" value="<?php echo isset($book['isbn']) ? $book['isbn'] : ''; ?>" <?php echo isset($book['isbn']) ? 'readonly' : ''; ?>></td>
            </tr>
            <tr>
                <td><strong>Title:</strong></td>
                <td><input type="text" name="title" value="<?php echo isset($book['title']) ? $book['title'] : ''; ?>"></td>
            </tr>
            <tr>
                <td><strong>Copyright:</strong></td>
                <td><input type="text" name="copyright" value="<?php echo isset($book['copyright']) ? $book['copyright'] : ''; ?>"></td>
            </tr>
            <tr>
                <td><strong>Edition:</strong></td>
                <td><input type="text" name="edition" value="<?php echo isset($book['edition']) ? $book['edition'] : ''; ?>"></td>
            </tr>
            <tr>
                <td><strong>Price:</strong></td>
                <td><input type="number" step="0.01" name="price" value="<?php echo isset($book['price']) ? $book['price'] : ''; ?>"></td>
            </tr>
            <tr>
                <td><strong>Quantity:</strong></td>
                <td><input type="number" name="quantity" value="<?php echo isset($book['quantity']) ? $book['quantity'] : ''; ?>"></td>
            </tr>
        </table>
        
        <div class="buttons">
            <input type="submit" name="search" value="SEARCH">
            <input type="submit" name="edit" value="EDIT">
            <input type="submit" name="delete" value="DELETE">
            <input type="submit" name="add" value="ADD">
        </div>
    </form>
</div>

<?php if ($message): ?>
<div class="prompt">
    <strong>PROMPT:</strong> <?php echo $message; ?>
</div>
<?php endif; ?>

<h3>Book Inventory LIST</h3>
<table>
    <thead>
        <tr>
            <th>ISBN</th>
            <th>Title</th>
            <th>Copyright</th>
            <th>Edition</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($all_books->num_rows > 0): ?>
            <?php while($row = $all_books->fetch_assoc()): ?>
                <?php 
                $line_total = $row['price'] * $row['quantity'];
                $total_books += $row['quantity'];
                $grand_total += $line_total;
                ?>
                <tr>
                    <td><?php echo $row['isbn']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['copyright']; ?></td>
                    <td><?php echo $row['edition']; ?></td>
                    <td><?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo number_format($line_total, 2); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        <tr style="background-color: #f0f0f0; font-weight: bold;">
            <td colspan="5">TOTALS</td>
            <td><?php echo $total_books; ?></td>
            <td><?php echo number_format($grand_total, 2); ?></td>
        </tr>
    </tbody>
</table>

</body>
</html>
