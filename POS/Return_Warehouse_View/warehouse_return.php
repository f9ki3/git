<?php
// Assuming $conn is your database connection object

$query = 'SELECT 
            mt.id,
            mt.material_invoice,
            mt.material_date,
            mt.material_cashier,
            mt.material_recieved_by,
            mt.material_inspected_by,
            mt.material_verified_by
          FROM 
            material_transfer mt';

$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->execute();
    $stmt->bind_result($id, $material_invoice, $material_date, $material_cashier, $material_received_by, $material_inspected_by, $material_verified_by);

    while ($stmt->fetch()) {
        echo '<tr onclick="redirectToMaterialTransfer(\'' . htmlspecialchars($material_invoice) . '\');">';
        echo '<td class="invoice align-middle ps-2">' . htmlspecialchars($material_invoice) . '</td>';
        echo '<td class="material_date align-middle">' . htmlspecialchars($material_date) . '</td>';
        echo '<td class="material_cashier align-middle">' . htmlspecialchars($material_cashier) . '</td>';
        echo '<td class="material_received_by align-middle text-start fw-semi-bold">' . htmlspecialchars($material_received_by) . '</td>';
        echo '<td class="material_inspected_by align-middle">' . htmlspecialchars($material_inspected_by) . '</td>';
        echo '<td class="material_verified_by align-middle">' . htmlspecialchars($material_verified_by) . '</td>';
        echo '</tr>';
    }
    $stmt->close(); // Close the statement after use
} else {
    // Handle prepare error
    echo 'Error in preparing SQL statement.';
}
?>

<script>
function redirectToMaterialTransfer(materialInvoice) {
    var materialTransaction = encodeURIComponent(materialInvoice);
    window.location.href = '../Return_Warehouse_View/?material_transaction=' + materialTransaction;
}
</script>



<?php include 'session.php'?>
<html lang="en">
<?php include 'header.php'?>
<body>
<div style="display: flex; flex-direction: row">
<?php include 'navigation_bar.php'?>
<?php
include '../config/config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the transaction code from the URL parameter and decode it
$transactionID = $_GET['transaction_code'];

// SQL query to retrieve transaction details
$sql = "SELECT * FROM purchase_transactions WHERE material_invoice_id = '$transactionID'";
$result = $conn->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Fetch the first row (assuming there's only one row for a given transaction ID)
    $transactionDetails = $result->fetch_assoc();
} else {
    echo "0 results";
}

?>


<?php include 'footer.php'?>
<script>
    function printDocument() {
    window.print();
}

</script>

</body>
</html>