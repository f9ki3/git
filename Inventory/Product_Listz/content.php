<div id="initialContent" class="my-9 py-9 text-center">
    <div id="spinner" class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="mb-9" id="actualContent" style="display: none;">
<!-- -------------- -->

        <div class="mb-9">
          <div class="row g-3 mb-4">
            <div class="col-auto">
              <h2 class="mb-0">Products</h2>
            </div>
          </div>
          <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
            <li class="nav-item"><a class="nav-link active" aria-current="page" href="#"><span>All </span><span class="text-700 fw-semi-bold">(68817)</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#"><span>Published </span><span class="text-700 fw-semi-bold">(70348)</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#"><span>Drafts </span><span class="text-700 fw-semi-bold">(17)</span></a></li>
            <li class="nav-item"><a class="nav-link" href="#"><span>On discount </span><span class="text-700 fw-semi-bold">(810)</span></a></li>
          </ul>
          <div id="products" data-list='{"valueNames":["product","price","category","tags","vendor","time"],"pagination":false}'>
            <div class="mb-4">
              <div class="d-flex flex-wrap gap-3">
                <div class="search-box">
                  <form class="position-relative" action="../Product_Listz/index.php" method="POST">
                    <?php 
                    if(isset($_POST['search'])){
                    ?>
                    <input class="form-control search-input search" name="search" type="search" placeholder="Search products" aria-label="Search" value="<?php echo $_POST['search'];?>"/>
                    <?php
                    } else {
                    ?>
                    <input class="form-control search-input search" name="search" type="search" placeholder="Search products" aria-label="Search" />
                    <?php 
                    }
                    ?>
                    <span class="fas fa-search search-box-icon"></span>
                  </form>
                </div>
                <div class="scrollbar overflow-hidden-y">
                  <div class="btn-group position-static" role="group">
                    <div class="btn-group position-static text-nowrap"><button class="btn btn-phoenix-secondary px-7 flex-shrink-0" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"> Category<span class="fas fa-angle-down ms-2"></span></button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                        <li>
                          <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item" href="#">Separated link</a></li>
                      </ul>
                    </div>
                    <div class="btn-group position-static text-nowrap"><button class="btn btn-sm btn-phoenix-secondary px-7 flex-shrink-0" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"> Vendor<span class="fas fa-angle-down ms-2"></span></button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                        <li>
                          <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item" href="#">Separated link</a></li>
                      </ul>
                    </div><button class="btn btn-sm btn-phoenix-secondary px-7 flex-shrink-0">More filters</button>
                  </div>
                </div>
                <div class="ms-xxl-auto"><button class="btn btn-link text-900 me-4 px-0"><span class="fa-solid fa-file-export fs--1 me-2"></span>Export</button><button class="btn btn-primary" id="addBtn"><span class="fas fa-plus me-2"></span>Add product</button></div>
              </div>
            </div>
            <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-white border-top border-bottom border-200 position-relative top-1">
              <div class="table-responsive scrollbar mx-n1 px-1">
              
                <table class="table fs--1 mb-0">
                  <thead>
                    <tr>
                      <!-- <th class="white-space-nowrap fs--1 align-middle ps-0" style="max-width:20px; width:18px;">
                        <div class="form-check mb-0 fs-0"><input class="form-check-input" id="checkbox-bulk-products-select" type="checkbox" data-bulk-select='{"body":"products-table-body"}' /></div>
                      </th> -->
                      <th class="sort white-space-nowrap align-middle fs--2" scope="col" style="width:70px;"></th>
                      <th class="sort white-space-nowrap align-middle ps-4" scope="col" style="width:350px;" data-sort="product">PRODUCT NAME</th>
                      <th class="sort align-middle text-end ps-4" scope="col" data-sort="price" style="width:150px;">WHOLESALE</th>
                      <th class="sort align-middle text-end ps-4" scope="col" data-sort="price" style="width:150px;">SRP</th>
                      <th class="sort align-middle ps-4" scope="col" data-sort="category" style="width:150px;">BRAND</th>
                      <th class="sort align-middle ps-3" scope="col" data-sort="tags" style="width:250px;">CATEGORY</th>
                      <th class="sort align-middle fs-0 text-center ps-4" scope="col" style="width:125px;">UNIT</th>
                      <th class="sort align-middle ps-4" scope="col" data-sort="vendor" style="width:200px;">MODELS</th>
                      <th class="sort align-middle ps-4" scope="col" data-sort="time" style="width:50px;">PUBLISHED BY</th>
                      <th class="sort text-end align-middle pe-0 ps-4" scope="col"></th>
                    </tr>
                  </thead>
                  <tbody class="list" id="productList">
                    
                  </tbody>
                </table>
              </div>
              <div class="row align-items-center justify-content-between py-2 pe-0 fs--1">
                <div class="col-auto d-flex">
                  <p class="mb-0 d-none d-sm-block me-3 fw-semi-bold text-900" data-list-info="data-list-info"></p><a class="fw-semi-bold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semi-bold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
                <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                  <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                </div>
              </div>
            </div>
          </div>
        </div>
<!-- -------------- -->
</div>