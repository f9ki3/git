<html lang="en">
<body>
<div style="display: flex; flex-direction: row">
<?php include '../../config/config.php'; ?>
<?php
// Start output buffering
ob_start();

// Check if the material_transaction parameter is set and not empty
if(isset($_GET['material_transaction']) && !empty($_GET['material_transaction'])) {
    // Retrieve the value of material_transaction
    $material_transaction = $_GET['material_transaction'];
    
    // Prepare and execute SQL query (using prepared statement to prevent SQL injection)
    $sql = "SELECT id, material_invoice, material_date, material_cashier, material_recieved_by, 
    material_inspected_by, material_verified_by, active, totalSellingPrice FROM material_transfer WHERE material_invoice = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $material_transaction);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any rows are returned
    if ($result->num_rows > 0) {
        // Fetch associative array
        $row = $result->fetch_assoc();

        // Assign each field to a variable
        $id = $row['id'];
        $material_invoice = $row['material_invoice'];
        $material_date = $row['material_date'];
        $material_cashier = $row['material_cashier'];
        $material_received_by = $row['material_recieved_by'];
        $material_inspected_by = $row['material_inspected_by'];
        $material_verified_by = $row['material_verified_by'];
        $active = $row['active'];
        $totalSellingPrice = $row['totalSellingPrice'];;

        // Now you can use these variables anywhere in your PHP page
    } else {
        echo "No records found";
    }
    
} else {
    // If material_transaction parameter is not set or empty, redirect
    header("Location: /Material_Transfer"); // Redirect to error page
    exit(); // Stop further execution
}

?>

<?php
$invoice_id = $_GET['material_transaction'];
$material_transfer_sql = "SELECT * FROM material_transfer WHERE material_invoice = '$invoice_id' LIMIT 1";
$material_transfer_res = $conn->query($material_transfer_sql);
if($material_transfer_res -> num_rows > 0){
    $row=$material_transfer_res->fetch_assoc();
    $material_date = $row['material_date'];
    $material_cashier = $row['material_cashier'];
    $material_received_by = $row['material_recieved_by'];
    $material_inspected_by = $row['material_inspected_by'];
    $material_verified_by = $row['material_verified_by'];
    $total_selling_price = $row['totalSellingPrice'];

    $check_status_sql = "SELECT status FROM material_transaction WHERE material_invoice_id = '$invoice_id' AND (status = '1' OR status='2')";
    $check_status_res = $conn->query($check_status_sql);
    if($check_status_res->num_rows> 0 ){
        $status = '<span class="text-primary">Pending</span>';
        $footer = "pending";
    } else {
        $check_verified_status = "SELECT status FROM material_transaction WHERE material_invoice_id = '$invoice_id' AND status = '3'";
        $check_verified_status_res = $conn->query($check_verified_status);
        if($check_verified_status_res -> num_rows > 0){
            $status = '<span class="text-success">Verified</span>';
            $footer = "verified";
        } else {
            $status = '<span class="text-success">Transaction Complete</span>';
            $footer="complete";
        }
    }
}
?>
    

<div style="width: 100%"  id="printContent">
    <div class="row">
        <div class="col text-end mb-3">
            <button class="btn border btn-lg rounded" id="printButton">Print</button>
        </div>
    </div>
    <div>
        <div style="background-color: white; height: auto;" class="rounded border p-3 mb-3 w-100">
                <div class="mt-2">
                    <div style="display: flex; flex-direction: row; justify-content: space-between">
                        <div>
                            <?php
                            // Output the Material Invoice, Date, and Cashier using PHP
                            echo "<h4>Material Invoice: ".$row['material_invoice']."</h4>";
                            echo "<h4>Date: ".$row['material_date']."</h4>";
                            ?>
                        </div>
                        <div class="col-lg-3" id="status_refresh">
                                    <div class="row">
                                        <?php echo $status;?>
                                    </div>
                                </div>
                        <input type="hidden" id="sessionID" value="<?php echo $user_id; ?>">
                        <input type="hidden" id="material_invoice" value="<?php echo $material_invoice; ?>">
                        <input type="hidden" id="user_brn_code" value="<?php echo $branch_code; ?>">
                     <div>
                            <!-- <button class="btn border btn-sm rounded" data-bs-toggle="modal" data-bs-target="#add_stocks">+ Add Stocks</button> -->
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: row; justify-content: space-between" class="mb-3">
                        <div class="form-floating" style="width: 32%; margin: 0px 5px 0px 5px" >
                            <input type="text" id="cashierName" value="<?php echo $row['material_cashier']; ?>" class="form-control" placeholder="" readonly>
                            <label for="cashierName">Cashier Name</label>
                        </div>
                        <div class="form-floating" style="width: 32%; margin: 0px 5px 0px 5px" >
                            <input type="text" id="receivedBy" value="<?php echo $row['material_recieved_by']; ?>" class="form-control" placeholder="" readonly>
                            <label for="receivedBy">Received by:</label>
                        </div>
                        <div class="form-floating" style="width: 32%; margin: 0px 5px 0px 5px" >
                        <input type="text" id="InspectedBy" value="<?php echo !empty($row['material_inspected_by']) ? $row['material_inspected_by'] : 'Pending'; ?>" class="form-control" placeholder="" readonly>
                        <label for="InspectedBy">Inspected by:</label>
                    </div>
                    <div class="form-floating" style="width: 32%; margin: 0px 5px 0px 5px" >
                        <input type="text" id="VerifiedBy" value="<?php echo !empty($row['material_verified_by']) ? $row['material_verified_by'] : 'Pending'; ?>" class="form-control" placeholder="" readonly>
                        <label for="VerifiedBy">Verified by:</label>
                    </div>
                    </div>

                    <div>
                        <!-- <input type="text" class="form-control mb-2 form-control-sm w-25" placeholder="Search"> -->
                    </div>
             </div>
        </div>
    </div>
             
<div style=" background-color: white;" class="p-3 rounded">
    <div style="height: 450px; overflow: auto">
    <table class="table">
    <thead>
        <tr> 
            <th>Checkbox</th>
            <th>Image</th>
            <th>Name</th>
            <th>Models</th>
            <th>Code</th>
            <th>SRP</th>
            <th>Quantity Request</th>
            <th>Quantity Receive</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php 
        $totalSellingPrice = 0;
        $material_invoice_id = $material_transaction; // replace with your material_invoice_id

        $sql = "SELECT mt.product_id, mt.input_srp, mt.qty_added,mt.qty_receive,mt.qty_warehouse, mt.created_at, mt.status, p.name, p.models, p.code, p.image
                    FROM material_transaction mt
                    JOIN product p ON mt.product_id = p.id
                    WHERE material_invoice_id = ?";
                    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $material_invoice_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                if ($row['status'] == 3) {
                    echo "<td><input type='checkbox' name='product_checkbox[]' value='{$row['product_id']}' style='max-width: 50px; height: 50px'></td>";
                } else {
                    echo "<td></td>"; // Empty cell if status is 4, 5, or 6
                }
                echo "<input type='hidden' name='product_id[]' value='{$row['product_id']}'>";
                echo "<td><img src='../../uploads/{$row['image']}' alt='Product Image' style='max-width: 50px; height: 50px'></td>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['models']}</td>";
                echo "<td>{$row['code']}</td>";
                echo "<td>{$row['input_srp']}</td>";
                echo "<td>{$row['qty_added']}</td>";
                echo "<td>{$row['qty_receive']}</td>";
                $status_text = '';
                switch ($row['status']) {
                    case 1:
                        $status_text = 'Pending';
                        break;
                    case 2:
                        $status_text = 'Reviewed';
                        break;
                    case 3:
                        $status_text = 'Approved';
                        break;
                    case 4:
                        $status_text = 'Received';
                        break;
                    case 5:
                        $status_text = 'Request Return';
                        break;
                    case 6:
                        $status_text = 'Returned';
                        break;
                    default:
                        $status_text = 'Unknown';
                        break;
                }
                echo "<td>{$status_text}</td>";
                echo "</tr>";

                // Only include rows with status other than 5 in the calculation
                // if ($row['status'] == 3 || $row['status'] == 4) {
                // if ($row['status'] == 3 || $row['status'] == 4 || $row['status'] == 6) {
                //     // Calculate totalSellingPrice and totalCostPrice
                //     $totalSellingPrice += $row['input_srp'] * $row['qty_receive'];
                //     $qty_receivetotal = $row['qty_receive'];
                // }
            }
        } else {
            echo "0 results";
        }
    ?>
    </tbody>
</table>

</div>

    <div>
        <div style="display: flex; flex-direction: row; justify-content: space-between" class="border rounded p-2 mt-3">      
                
                    <div>
                        <div style="display: flex; flex-direction: row; width: 100%; justify-content: space-between">
                        <h4 id="totalSellingPrice" class="p-2 mt-5">Total Product Amount ₱<?php echo number_format($totalSellingPrice, 2); ?></h4>
                        </div>
                    </div>
                    <div style="width: 30%">
                        <button type="button" id="acceptMaterialTransfer" class="btn w-100 btn-primary mb-2">Accept</button>
                        <button type="button" id="returnMaterialTransfer" class="btn w-100 btn-outline-primary mb-2">Request Return</button>
                    </div>
                
        </div>
    </div>
</div>


</div>
<?php include 'footer.php'?>
</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/v/dt/dt-2.0.2/datatables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script>

// Calculate total selling amount
var totalSellingPrice = 0;
$('tr').each(function() {
    var status = $(this).find('td:eq(8)').text().trim(); // Get status from the table cell
    if (status === 'Approved' ||status === 'Received' || status === 'Returned') {
        var inputSrp = parseFloat($(this).find('td:eq(5)').text()); // Get input SRP
        var qtyReceive = parseFloat($(this).find('td:eq(7)').text()); // Get quantity receive
        var productTotalSellingPrice = inputSrp * qtyReceive;
        totalSellingPrice += productTotalSellingPrice;
    }
});

console.log('Total Selling Price:', totalSellingPrice); // Log the total selling price

$(document).ready(function () {
    $('#totalSellingPrice').text('Total Product Amount ₱' + totalSellingPrice.toFixed(2)); // compute value
    // Function to update button status based on status value
    function updateButtonStatus() {
        console.log('Function updateButtonStatus() is running.'); // Log that the function is running
        
        var allApproved = true; // Flag to track if all selected products have status "Approved"
        
        // Loop through each selected checkbox
        $('input[name="product_checkbox[]"]:checked').each(function() {
            var closestRow = $(this).closest('tr');
            var status = closestRow.find('td:eq(8)').text().trim(); // Assuming status is in the 8th column
            console.log('Status:', status); // Log the status value
            
            // If status is null or not "Approved"
            if (status === 'Approved') { 
                allApproved = false; // Set the flag to false
                return true; // Exit the loop
            }
        });
        
        console.log('All approved:', allApproved); // Log the flag value
        
        // Update button status based on the flag value
        if (allApproved) {
            $('#acceptMaterialTransfer').prop('disabled', true); // Enable accept button
            $('#returnMaterialTransfer').prop('disabled', true); // Enable decline button
        }
        else {
            $('#acceptMaterialTransfer').prop('disabled', false); // Disable accept button
            $('#returnMaterialTransfer').prop('disabled', false); // Disable decline button
        }
    }

    // Initial update of button status
    updateButtonStatus();

    // Update button status when a checkbox is clicked
    $('input[name="product_checkbox[]"]').click(function() {
        updateButtonStatus();

        // Recompute totalSellingPrice and totalCostPrice when checkbox is clicked
        var closestRow = $(this).closest('tr');
        var status = closestRow.find('td:eq(8)').text().trim();
        if (status === 'Approved' || status === 'Returned') {
            var inputSrp = parseFloat(closestRow.find('td:eq(5)').text()); // Get input SRP
            var qtyReceive = parseFloat(closestRow.find('td:eq(6)').text()); // Get quantity receive
            var totalSellingPrice = inputSrp * qtyReceive; // Calculate total selling price
            closestRow.find('td:eq(10)').text(totalSellingPrice.toFixed(2)); // Update total selling price column
        }
    });


    // Accept Material Transfer
$('#acceptMaterialTransfer').click(function () {
    // Check if the button is enabled
    if ($(this).prop('disabled')) {
        return; // Do nothing if the button is disabled
    }
    var user_brn_code = $('#user_brn_code').val();
    var materialInvoiceNo = $('#material_invoice').val();
    var sessionID = $('#sessionID').val();
    var cashierName = $('#cashierName').val();
   
    // Compute total selling price
    var totalSellingPrice = 0;

$('input[name="product_checkbox[]"]').each(function() {
    // Check if the checkbox is checked
    if ($(this).is(":checked")) {
        var closestRow = $(this).closest('tr');
        var inputSrp = parseFloat(closestRow.find('td:eq(5)').text()); // Get input SRP
        var qtyReceive = parseFloat(closestRow.find('td:eq(7)').text()); // Get quantity receive

        // Add to total selling price if inputSrp and qtyReceive are valid numbers
        if (!isNaN(inputSrp) && !isNaN(qtyReceive)) {
            totalSellingPrice += inputSrp * qtyReceive;
        }
    }
});

console.log('Total Selling Price:', totalSellingPrice);

    console.log('Total Selling Price:', totalSellingPrice); // Log the total selling price
    
    // Save Material Transfer with total values
    $.ajax({
        url: '../../php/store_stocks_recompute_product.php',
        method: 'POST',
        data: {
            materialInvoiceID: materialInvoiceNo,
            totalSellingPrice: totalSellingPrice
        },
        success: function (response) {
            console.log(response);
                // Send notification
                $.ajax({
                    url: '../../php/update_notification.php',
                    method: 'POST',
                    data: {
                        sessionID: sessionID,
                        type_id: materialInvoiceNo,
                        type: 'Material Transaction',
                        sender: cashierName,
                        message: 'The Store accepted the Material Transfer'
                    },
                    success: function (response) {
                        console.log('Notification sent successfully');
                        // Loop through checked checkboxes and update product stocks
                        $('input[name="product_checkbox[]"]:checked').each(function() {
                            var productId = $(this).closest('tr').find('input[name="product_id[]"]').val();
                            var qtySent = $(this).closest('tr').find('td:eq(6)').text(); // Assuming qty sent is in the 7th column
                            var qtyReceive = $(this).closest('tr').find('td:eq(6)').text(); // Assuming qty sent is in the 7th column
                            var status = $(this).closest('tr').find('td:eq(8)').text(); // Assuming status is in the 8th column
                            
                            console.log('Status:', status); // Log the status value
                            console.log('Branch_code:', user_brn_code); // Log the status value
                            console.log('productId:', productId); // Log the status value
                            if (status === 'Approved' || status === 'Returned') {
                                // Only update product stocks if status is 'Accepted'
                                $.ajax({
                                    url: '../../php/add_product_stocks.php',
                                    method: 'POST',
                                    data: {
                                        productId: productId,
                                        qty_sent: qtySent,
                                        qty_receive: qtyReceive,
                                        user_brn_code: user_brn_code,
                                        materialInvoiceID: materialInvoiceNo,
                                        status: status // Pass the status to the PHP script
                                    },
                                    success: function (response) {
                                        console.log('Product stocks updated successfully for product ID ' + productId);
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('Error updating product stocks for product ID ' + productId + ':', error);
                                    }
                                });
                            } else {
                                console.log('Status is not "Approved", skipping product ID ' + productId);
                                console.log('Status is not "status", status is ' + status);
                                // Handle other statuses here
                                // You can add any desired behavior for statuses other than "Accepted"
                            }
                        });

                        swal("Material Returned", "Products has been accepted", "success").then((value) => {
                            if (value) {
                                // Reload the page
                                window.location.reload();
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error sending notification:', error);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Error saving data:', error);
            }
        });
    });

    // Return Material Transfer
    $('#returnMaterialTransfer').click(function () {
        // Check if the button is enabled
        if ($(this).prop('disabled')) {
            return; // Do nothing if the button is disabled
        }
        
        var user_brn_code = $('#user_brn_code').val();
        var materialInvoiceNo = $('#material_invoice').val();
        var sessionID = $('#sessionID').val();
        var cashierName = $('#cashierName').val();
        var totalSellingPrice = '<?php echo $totalSellingPrice; ?>';
        
        // Send notification
        $.ajax({
            url: '../../php/update_notification.php',
            method: 'POST',
            data: {
                sessionID: sessionID,
                type_id: materialInvoiceNo,
                type: 'Material Transaction',
                sender: cashierName,
                message: 'The Store return the Material Transfer'
            },
            success: function (response) {
                console.log('Notification sent successfully');
                
                // Loop through checked checkboxes and update product stocks
                $('input[name="product_checkbox[]"]:checked').each(function() {
                    var productId = $(this).closest('tr').find('input[name="product_id[]"]').val();
                    var status = $(this).closest('tr').find('td:eq(8)').text(); // Assuming status is in the 9th column
                    
                    console.log('Status:', status); // Log the status value
                    console.log('Branch_code:', user_brn_code); // Log the branch_code value
                    console.log('productId:', productId); // Log the productId value
                    
                    if (status === 'Approved') {
                        // Only update product stocks if status is 'Approved'
                        $.ajax({
                            url: '../../php/store_stocks_recompute_product.php',
                            method: 'POST',
                            data: {
                                materialInvoiceID: materialInvoiceNo,
                                totalSellingPrice: totalSellingPrice
                            },
                            success: function (response) {
                                console.log(response);
                            },
                            error: function (xhr, status, error) {
                                console.error('Error recomputing store stocks:', error);
                            }
                        });

                        $.ajax({
                            url: '../../php/return_product_stocks_status.php',
                            method: 'POST',
                            data: {
                                productId: productId,
                                materialInvoiceID: materialInvoiceNo,
                                status: status // Pass the status to the PHP script
                            },
                            success: function (response) {
                                console.log('Product stocks updated successfully for product ID ' + productId);
                            },
                            error: function (xhr, status, error) {
                                console.error('Error updating product stocks for product ID ' + productId + ':', error);
                            }
                        });
                    } else {
                        console.log('Status is not "Approved", skipping product ID ' + productId);
                        console.log('Status is:', status);
                        // Handle other statuses here
                        // You can add any desired behavior for statuses other than "Approved"
                    }
                });
                
                swal({
                    title: "Material Returned",
                    text: "Products have requested to return",
                    icon: "error"
                    // buttons: true, // Show the confirm button
                })
                .then((value) => {
                    if (value) {
                        // Redirect to the specified URL
                        window.location.href = '../Return_Warehouse_View/?material_transaction=<?php echo $invoice_id; ?>';
                    }
                    else {
                        // If the user cancels, reload the page
                        window.location.reload();
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error('Error sending notification:', error);
            }
        });
    });
});

// Recompute total selling price when checkbox state inputs
$('input[name="product_checkbox[]"]').click(function () {
    var totalSellingPrice = 0;

    $('input[name="product_checkbox[]"]:checked').each(function () {
        var closestRow = $(this).closest('tr');
        var inputSrp = parseFloat(closestRow.find('td:eq(5)').text()); // Get input SRP
        var quantityRequested = parseFloat(closestRow.find('td:eq(6)').text()); // Get quantity requested

        if (!isNaN(inputSrp) && !isNaN(quantityRequested)) {
            totalSellingPrice += inputSrp * quantityRequested; // Calculate total selling price
        }
    });

    // Update total selling price display
    $('#totalSellingPrice').text('Total Selected Product: ₱' + totalSellingPrice.toFixed(2));
});

</script>