<div id="initialContent" class="my-9 py-9 text-center">
    <div id="spinner" class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="mb-9" id="actualContent" style="display: none;">
  <div class="row g-3 mb-4">
    <div class="col-auto">
      <h2 class="mb-0">Permissions/ Privilege</h2>
    </div>
  </div>

  <div id="products" data-list='{"valueNames":["branch_name","status","address","telephone","phone","email"],"page":10,"pagination":true}'>
    <div class="mb-4">
      <div class="d-flex flex-wrap gap-3">
        <div class="search-box">
          <form class="position-relative" data-bs-toggle="search" data-bs-display="static">
            <input class="form-control search-input search" type="search" placeholder="Search position" aria-label="Search" />
            <span class="fas fa-search search-box-icon"></span>
          </form>
        </div>

        <div class="ms-xxl-auto">
          <!-- <button class="btn btn-link text-900 me-4 px-0">
            <span class="fa-solid fa-file-export fs--1 me-2"></span>Export
          </button> -->
          <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#add_permission">
            <span class="fas fa-plus me-2"></span>Add permission
          </button>
        </div>
      </div>
    </div>
    

    <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-white border-top border-bottom border-200 position-relative top-1">
      <div class="table-responsive scrollbar mx-n1 px-1">
        <table class="table fs--1 mb-0">
          <thead>
            <tr>
              <th class="white-space-nowrap fs--1 align-middle ps-0" style="max-width:20px; width:18px;">
                <div class="form-check mb-0 fs-0">
                  <input class="form-check-input" id="checkbox-bulk-products-select" type="checkbox" data-bulk-select='{"body":"products-table-body"}' />
                </div>
              </th>
              <th class="sort" scope="col" data-sort="branch_name">PERMISSION NAME</th>
              <th class="sort text-start" scope="col" data-sort="address">PUBLISH BY</th>
              <th class="sort text-start" scope="col" data-sort="address">DATE</th>
            </tr>
          </thead>

          <tbody class="list" id="products-table-body">
            <?php include "tr.php";?>
            
          </tbody>
        </table>
      </div>

      <div class="row align-items-center justify-content-between py-2 pe-0 fs--1">
        <div class="col-auto d-flex">
          <p class="mb-0 d-none d-sm-block me-3 fw-semi-bold text-900" data-list-info="data-list-info"></p>
          <a class="fw-semi-bold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
          <a class="fw-semi-bold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
        </div>
        <div class="col-auto d-flex">
          <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
          <ul class="mb-0 pagination"></ul>
          <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include "add_position.php"; ?>
<?php
$permissions_sql = "SELECT id, permission_name FROM `groups`";
$permissions_res = $conn->query($permissions_sql);
if($permissions_res->num_rows>0){
  while($perm_row=$permissions_res->fetch_assoc()){
    $permission = $perm_row['permission_name'];
    $permission_id = $perm_row['id'];
?>
<div class="offcanvas offcanvas-end" id="inventory_offcanvas_<?php echo $permission_id;?>" tabindex="-1" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Permissions</h5><button class="btn-close text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="row"></div>
    <?php
    // Split the permission string into an array of individual permissions
    $permissionsArray = explode(', ', $permission);
    ?>
    <div class="row">
    <form action="update_permissions.php" method="post">
      
        <?php foreach ($permissionsArray as $perm) { ?>
            <div class="form-check">
              <input class="form-check-input" id="<?php echo $perm; ?>" type="checkbox" name="permission[]" value="<?php echo $perm; ?>" />
              <label class="form-check-label" for="<?php echo $perm; ?>"><?php echo ucwords(str_replace("_", " ", $perm)); ?></label>
            </div>
        <?php } ?>
          <div class="col-lg-12 text-center my-3">
            <button class="btn btn-primary" type="submit">Save</button>
          </div>
        
    </form>
    </div>  
  </div>
</div>

<div class="offcanvas offcanvas-end" id="pos_offcanvas_<?php echo $permission_id;?>" tabindex="-1" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Permissions</h5><button class="btn-close text-reset" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">...</div>
</div>
<?php
  }
}
?>