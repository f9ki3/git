<?php
$delivery_reciept_Sql = "SELECT * FROM delivery_receipt WHERE branch_code = '$branch_code' ORDER BY id DESC";
$delivery_reciept_res = $conn->query($delivery_reciept_Sql);
if($delivery_reciept_res -> num_rows > 0){
    while($row = $delivery_reciept_res -> fetch_assoc()){
        $dr_id = $row['id'];
        $checked_by = $row['checked_by'];
        $approved_by = $row['approved_by'];
        $delivered_by = $row['delivered_by'];
        $published_by = $row['publish_by'];
        $published_date = $row['publish_date'];
        $supplier_id = $row['supplier_id'];
        // $receive_date = $row['received_date'];
        $receive_date = date("F j, Y", strtotime(str_replace('/', '-', $row['received_date'])));
        if($row['status'] == '1'){
            $status = '<span class="badge badge-phoenix fs--2 badge-phoenix-success"><span class="badge-label">Success</span><span class="ms-1" data-feather="check" style="height:12.8px;width:12.8px;"></span></span>';
        } else {
            $status = '<span class="badge badge-phoenix fs--2 badge-phoenix-warning"><span class="badge-label">Pending</span> <div class="spinner-border" role="status" style="height: 10px; width: 10px;"><span class="visually-hidden">Loading...</span></div></span>';
        }
        // $note = $row['note'];
        $supplier_sql = "SELECT * FROM supplier WHERE id = '$supplier_id'";
        $supplier_res = $conn->query($supplier_sql);
        if($supplier_res -> num_rows > 0 ){
            $row = $supplier_res -> fetch_assoc();
            $supplier_name = $row['supplier_name'];
        }
?>
    <tr class="position-static">
        <!-- <td class="align-middle">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="dr_id[]" name="dr_id[]" value="<?php //echo $dr_id; ?>"/>
            </div>
        </td> -->
        <td class="align-middle white-space-nowrap py-0 text-success">#<?php echo $dr_id; ?></td>
        <td class="tags align-middle text-center review pb-2 ps-3"><?php echo $status;?></td>
        <td class="product align-middle ps-4"><a class="fw-semi-bold line-clamp-3 mb-0" href="redirect.php?id=<?php echo $dr_id; ?>"><?php echo $supplier_name; ?></a></td>
        <td class="price white-space-nowrap text-start fw-bold text-700 ps-4"><?php echo $checked_by; ?></td>
        <td class="category align-middle white-space-nowrap text-600 ps-4 fw-semi-bold"><?php echo $approved_by; ?></td>
        <td class="tags align-middle text-center review pb-2 ps-3"><?php echo $delivered_by; ?></td>
        <td class="vendor align-middle text-start fw-semi-bold ps-4"><?php echo $published_by; ?></td>
        <td class="vendor align-middle text-start fw-semi-bold ps-4"><?php echo $receive_date; ?></td>
        <td class="align-middle white-space-nowrap text-end pe-0 ps-4 btn-reveal-trigger">
            <div class="font-sans-serif btn-reveal-trigger position-static">
                <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs--2" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs--2"></span></button>
                <div class="dropdown-menu dropdown-menu-end py-2">
                    <a class="dropdown-item" href="#!">View</a>
                    <a class="dropdown-item" href="#!">Export</a>
                <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#!">Remove</a>
                </div>
            </div>
        </td>
    </tr>
<?php
    }
} else {
    echo '<tr>
        <td class="text-danger text-center" colspan="10"><b>EMPTY DATA</b></td>
    </tr>';
}
?>
