<?php
// Include the database connection file
include "../database/database.php";

// Set the default timezone to Asia/Manila (Philippines)
date_default_timezone_set('Asia/Manila');

// Get the current timestamp
$currentTimestamp = time();

// Get the timestamp for yesterday (subtract 24 hours)
$yesterdayTimestamp = $currentTimestamp - (24 * 3600); // 24 hours * 3600 seconds per hour

// Format the current timestamp to display date and time
$dateWithTimestampFormat = date('Y-m-d H:i:s', $currentTimestamp);

// Format the timestamp for yesterday to display only date in year-month-date format
$dateYesterdayFormat = date('Y-m-d', $yesterdayTimestamp);

// Format the current timestamp to display only date in year-month-date format
$dateTodayFormat = date('Y-m-d', $currentTimestamp);

// Check if the current time is 1:30 in the afternoon
$timecheck = date('H:i', $currentTimestamp) >= '02:00';

// Output whether it's 1:30 PM or not
if ($timecheck) {
    // SQL query to select branch codes
    $branch_Sql = "SELECT brn_code FROM branch";
    $branch_result = $conn->query($branch_Sql);

    if($branch_result->num_rows>0){
        // Fetch branch code
        $branch = $branch_result->fetch_assoc();
        $brn_code = $branch['brn_code'];

        // Check if a report with specified conditions already exists
        $check_report_sql = "SELECT id FROM reports WHERE report_name = 'daily_income' AND report_type = 'daily' AND date_generated = '$dateYesterdayFormat' AND branch_code = '$brn_code'";
        $check_report_result = $conn->query($check_report_sql);

        if($check_report_result->num_rows<1){
            // Insert a new report if it doesn't exist
            $insert_to_reports = "INSERT INTO reports SET report_name = 'daily_income', report_type = 'daily', date_generated = '$dateYesterdayFormat', branch_code = '$brn_code', publish_by = '0'";

            if($conn->query($insert_to_reports) === TRUE ){
                echo "successfully inserted to reports table<br>";
                // Get the ID of the inserted report
                $report_id = $conn->insert_id;

                $pt_sql = "SELECT * FROM purchase_transactions WHERE DATE(TransactionDate) = '$dateYesterdayFormat' ORDER BY TransactionDate ASC";
                $pt_res = $conn -> query($pt_sql);
                if($pt_res ->num_rows>0){
                    while($pt = $pt_res -> fetch_assoc()){
                        $id = $pt['TransactionID'];
                        $customer_name = $pt['CustomerName'];
                        $transaction_date = $pt['TransactionDate'];
                        $transaction_address = $pt['TransactionAddress'];
                        $transaction_verified_by = $pt['TransactionVerifiedBy'];
                        $transaction_inspected_by = $pt['TransactionInspectedBy'];
                        $transaction_receive_by = $pt['TransactionReceivedBy'];
                        $transaction_payment_method = $pt['TransactionPaymentMethod'];
                        $transaction_type = $pt['TransactionType'];
                        $transaction_subtotal = $pt['Subtotal'];
                        $transaction_tax = $pt['Tax'];
                        $transaction_discount = $pt['Discount'];
                        $transaction_total = $pt['Total'];
                        $transaction_payment = $pt['Payment'];
                        $transaction_ChangeAmount = $pt['ChangeAmount'];
                        $transaction_status = $pt['status'];

                        $total_totals = 0;
                        $insert_to_rpt = "INSERT INTO report_purchase_transactions 
                                            SET TransactionID = '$id',
                                             report_id = '$report_id',
                                             CustomerName = '$customer_name',
                                             TransactionDate='$transaction_date',
                                             TransactionAddress = '$transaction_address',
                                             TransactionVerifiedBy = '$transaction_verified_by',
                                             TransactionInspectedBy = '$transaction_inspected_by',
                                             TransactionReceivedBy = '$transaction_receive_by',
                                             TransactionPaymentMethod = '$transaction_payment_method',
                                             TransactionType = '$transaction_type',
                                             Subtotal = '$transaction_subtotal',
                                             Tax = '$transaction_tax',
                                             Discount = '$transaction_discount',
                                             Total = '$transaction_total',
                                             Payment = '$transaction_payment',
                                             ChangeAmount = '$transaction_ChangeAmount',
                                             `status` = '$transaction_status'";
                        $conn->query($insert_to_rpt);

                        // SQL query to retrieve purchase transactions for yesterday
                        $cx_receipt_sql = "SELECT * FROM purchase_cart WHERE TransactionID = '$id'";
                        $cx_receipt_res = $conn -> query($cx_receipt_sql);
                        if($cx_receipt_res->num_rows>0){
                            while($resibo = $cx_receipt_res->fetch_assoc()){
                                // Retrieve data for each transaction
                                $receipt_product_id = $resibo['ProductID'];
                                $receipt_qty = $resibo['Quantity'];
                                $receipt_total_amount = $resibo['TotalAmount'];
                                $receipt_discount = $resibo['Discount'];
                                $receipt_discount_type = $resibo['DiscountType'];
                                $receipt_computation_status = $resibo['computation_status'];
                                $receipt_computed_qty = $resibo['computed_qty'];
                                $receipt_transaction_id = $resibo['TransactionID'];
                                $per_product_computation = $receipt_total_amount / $receipt_qty;
                                echo "<br> transaction_id = " . $receipt_transaction_id . "<br> product_id = " . $receipt_product_id . "<br>" . $receipt_qty . "<br>" . $receipt_total_amount . "<br>";
                                echo "--------" . $per_product_computation . "---<br>";
                                
                                // Calculate final amount per product considering discounts
                                if($receipt_discount_type == "%"){
                                    
                                    $final_per_product_computation = ($receipt_discount / 100) * $per_product_computation;
                                    echo "sold price per product: " . $final_per_product_computation . "<br>";
                                } else {
                                    $final_per_product_computation = $per_product_computation - $receipt_discount;
                                    echo "sold price per product: " . $final_per_product_computation . "<br>";
                                }
                                $amount_per_product = $final_per_product_computation;
                                $total_puhunan_per_product = 0;
                                $total_tubo_per_product = 0;
                                
                                
                                // Check if computation status is not OK
                                if($receipt_computation_status !== "OK"){

                                    // Using a for loop to print numbers from 1 to 10
                                    for ( $i = $receipt_computed_qty; $i <= $receipt_qty; $i++) {
                                        // Retrieve data from delivery receipt for further computation
                                        $dr_sql = "SELECT dr.*, drc.*
                                        FROM delivery_receipt_content AS drc
                                        JOIN delivery_receipt AS dr ON dr.id = drc.delivery_receipt_id
                                        WHERE drc.product_id = '$receipt_product_id' AND drc.computation_status != 'OK'
                                        ORDER BY dr.received_date ASC
                                        LIMIT 1";
                                        $dr_res = $conn->query($dr_sql);
                                        if($dr_res->num_rows>0){
                                            // Fetch data from delivery receipt
                                            $drc = $dr_res->fetch_assoc();
                                            $drc_id = $drc['delivery_receipt_id'];
                                            $drc_product_id = $drc['product_id'];
                                            $drc_qty = $drc['quantity'];
                                            $drc_puhunan = $drc['price'];
                                            $drc_computed_qty = $drc['computed_qty'];
                                            $drc_computed_status = $drc['computation_status'];
                                            $drc_new_computed_qty = $drc_computed_qty + 1;
                                            echo "drc new computed qty :" . $drc_new_computed_qty . "<br>";
                                            if($drc_new_computed_qty == $drc_qty){
                                                $update_drc = "UPDATE delivery_receipt_content SET computation_status = 'OK' WHERE delivery_receipt_id = '$drc_id' AND product_id = '$drc_product_id'";
                                                if($conn->query($update_drc) === TRUE ){
                                                    echo "product :" . $drc_id . " successfuly updated! <br>";
                                                } else {
                                                    echo $conn->error;
                                                }
                                            }
                                            if($drc_computed_qty !== $drc_new_computed_qty){
                                                $update_dr_qty = "UPDATE delivery_receipt_content SET computed_qty = '$drc_new_computed_qty' WHERE delivery_receipt_id = '$drc_id' AND product_id = '$drc_product_id'";
                                                if($conn->query($update_dr_qty) === TRUE ){
                                                    echo "computed qty successfully updated! product_id : " . $drc_product_id . " <br>";
                                                } else {
                                                    echo "no more dr for this product id <br>";
                                                }
                                            } 

                                            // Calculate profit per product
                                            $puhunan_per_product = $amount_per_product - $drc_puhunan;
                        
                                            $total_puhunan_per_product += $puhunan_per_product;
                                            $total_tubo_per_product += $puhunan_per_product;

                                            echo "<br>amount per product: " . $amount_per_product . "<br>tubo per piece: " . $puhunan_per_product . "<br>";
                                        } else {
                                            // Handle scenario when there's no delivery receipt for the product
                                            echo "<br>no product on delivery_receipt<br>";
                                            //if wala nang pagbebase ng puhunan sa dr
                                            // Retrieve data from delivery receipt for further computation
                                            $est_qty = 0;
                                            $dr_sql_2 = "SELECT dr.*, drc.*
                                            FROM delivery_receipt_content AS drc
                                            JOIN delivery_receipt AS dr ON dr.id = drc.delivery_receipt_id
                                            WHERE drc.product_id = '$receipt_product_id' AND drc.computation_status = 'OK'
                                            ORDER BY dr.received_date DESC
                                            LIMIT 1";
                                            $dr_res_2 = $conn->query($dr_sql_2);
                                            if($dr_res_2 -> num_rows >0 ){
                                                // Fetch data from delivery receipt
                                                $drc = $dr_res_2->fetch_assoc();
                                                $drc_id = $drc['delivery_receipt_id'];
                                                $drc_product_id = $drc['product_id'];
                                                $drc_qty = $drc['quantity'];
                                                $drc_puhunan = $drc['price'];
                                                $drc_computed_qty = $drc['computed_qty'];
                                                $drc_computed_status = $drc['computation_status'];
                                                // Calculate profit per product
                                                $puhunan_per_product = $amount_per_product - $drc_puhunan;
                            
                                                $total_puhunan_per_product += $puhunan_per_product;
                                                $total_tubo_per_product += $puhunan_per_product;
                                                $est_qty += 1;
                                            }

                                        }

                                        if($i == $receipt_qty){
                                            $update_receipt = "UPDATE purchase_cart SET computation_status = 'OK' WHERE TransactionID = '$receipt_transaction_id' AND ProductID = '$receipt_product_id'";
                                            if($conn->query($update_receipt) === TRUE ){
                                                echo "computation status successfully updated to OK!";
                                            }
                                        }
                                    }
                                    // ---------------------
                                    

                                } else {
                                    // Handle scenario when computation status is OK
                                }
                                $estimated_qty = $est_qty;

                                echo "<br> total_tubo per product = " . $total_tubo_per_product . "<br> ----------------------------";
                                echo "estimated qty of estimated prices: " . $est_qty;
                                $total_totals += $total_tubo_per_product;

                                $insert_data = "INSERT INTO report_purchase_cart SET id = '', ProductID = '$receipt_product_id', TransactionID = '$receipt_transaction_id', ProductName = '', Brand = '', Model = '', Quantity = '$receipt_qty', Unit = '', SRP = '', Discount = '$receipt_discount', DiscountType = '$receipt_discount_type', TotalAmount ='$receipt_total_amount', total_income = '$total_tubo_per_product', est_amt_qty = '$est_qty'";
                                $conn -> query($insert_data);
                                // dito iinsert sa reports.
                            }
                        } else {    
                            // Handle error when there are no purchase transactions
                        }

                        $total = $total_totals;
                        $last_update = "UPDATE report_purchase_transactions SET total_est_income = '$total'";
                        $conn->query($last_update);
                    }
                } else {
                    $drop_report = "DELETE FROM reports WHERE id = '$report_id'";
                    $conn->query($drop_report);
                }
            } else {
                echo "error inserting the report<br>";
            } // End of inserting the report
        } else {
            echo "report already exist<br>";
        }
    } else {
        echo "no data on branch table<br>";
    }
} else {
    echo "not yet greater that 02:00 am<br>";
}

// Close the database connection
$conn->close();

// Exit the script
exit();
?>
