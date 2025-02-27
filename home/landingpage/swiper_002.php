<?php
// Assuming $conn is your database connection
$found_category = false;

while (!$found_category) {
    // Fetch a random category with more than 10 products
    $random_category_query = "SELECT 
                                category.category_name
                            FROM 
                                category
                            JOIN 
                                product ON category.id = product.category_id
                            GROUP BY 
                                category.category_name
                            HAVING 
                                COUNT(*) > 10
                            ORDER BY 
                                RAND()
                            LIMIT 1";

    $random_category_result = $conn->query($random_category_query);

    if ($random_category_result === false) {
        echo "Error executing query: " . $conn->error;
    } else {
        if ($random_category_result->num_rows > 0) {
            $random_category_row = $random_category_result->fetch_assoc();
            $category_name = $random_category_row["category_name"];

            // Fetch products related to the random category
            $result = $conn->query("SELECT 
                                        product.name,
                                        product.code,
                                        product.supplier_code,
                                        product.barcode,
                                        product.image,
                                        product.models,
                                        price_list.srp,
                                        product.category_id
                                    FROM 
                                        product
                                    JOIN 
                                        price_list ON product.id = price_list.product_id
                                    JOIN 
                                        category ON product.category_id = category.id
                                    WHERE
                                        category.category_name = '$category_name'
                                    ORDER BY
                                        RAND()
                                    LIMIT 10");

            if ($result === false) {
                echo "Error executing query: " . $conn->error;
            } else {
                if ($result->num_rows > 0) {
                    // Display label with dynamic category name
                    echo '<div class="d-flex flex-between-center mb-3">';
                    echo '<h3>Best Offers in ' . $category_name . '</h3>';
                    echo '<a class="fw-bold d-none d-md-block" href="#!">Explore more<span class="fas fa-chevron-right fs--1 ms-1"></span></a>';
                    echo '</div>';

                    // Display products
                    echo '<div class="swiper-theme-container products-slider">';
                    echo '<div class="swiper swiper-container theme-slider" data-swiper=\'{"slidesPerView":1,"spaceBetween":16,"breakpoints":{"450":{"slidesPerView":2,"spaceBetween":16},"576":{"slidesPerView":3,"spaceBetween":20},"768":{"slidesPerView":4,"spaceBetween":20},"992":{"slidesPerView":5,"spaceBetween":20},"1200":{"slidesPerView":6,"spaceBetween":16}}}\'>';
                    echo '<div class="swiper-wrapper">';

                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="swiper-slide">';
                        echo '<div class="product-card">'; // Updated class
                        echo '<div class="product-card-inner">'; // Added inner container
                        echo '<div class="border border-1 rounded-3 position-relative mb-3">';
                        echo '<button class="btn rounded-circle p-0 d-flex flex-center btn-wish z-index-2 d-toggle-container btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Add to wishlist">';
                        echo '<span class="fas fa-heart d-block-hover"></span>';
                        echo '<span class="far fa-heart d-none-hover"></span>';
                        echo '</button>';
                        echo '<div style="height: 250px; width: 100%;">
                                <img style="object-fit: cover; height: 100%; width: 100%;" class="rounded" src="../../uploads/' . basename($row["image"]) . '" alt="' . $row["name"] . '" />
                             </div>';
                        echo '</div>';
                        echo '<a class="stretched-link" href="product-details.html">';
                        echo '<h6 class="mb-2 lh-sm line-clamp-3 product-name">' . $row["name"] . '</h6>';
                        echo '</a>';
                        echo '<p class="fs--1">';
                        echo '<span class="fa fa-star text-warning"></span>';
                        echo '<span class="fa fa-star text-warning"></span>';
                        echo '<span class="fa fa-star text-warning"></span>';
                        echo '<span class="fa fa-star text-warning"></span>';
                        echo '<span class="fa fa-star text-warning"></span>';
                        echo '</p>';
                        echo '<div>';
                        echo '<h6 class="text-success lh-1 mb-0">35% off</h6>';
                        echo '</div>';
                        // Add "Add to Cart" button
                        echo '<button class="btn btn-primary btn-add-to-cart" data-product-id="' . $row["code"] . '">Add to Cart</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';

                    echo '<div class="swiper-nav">';
                    // echo '<div class="swiper-button-next"><span class="fas fa-chevron-right nav-icon"></span></div>';
                    // echo '<div class="swiper-button-prev"><span class="fas fa-chevron-left nav-icon"></span></div>';
                    // echo '</div>';

                    echo '<a class="fw-bold d-md-none" href="#!">Explore more<span class="fas fa-chevron-right fs--1 ms-1"></span></a>';
                    $found_category = true; // Set flag to true to exit the loop
                }
            }
        }
    }
}
?>

<!-- JavaScript to handle "Add to Cart" button click -->
<script>
    // JavaScript to handle "Add to Cart" button click
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-add-to-cart').forEach(function(button) {
        button.addEventListener('click', function() {
            var productId = this.getAttribute('data-product-id');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/danielle_motors/home/cart/add_to_cart.php', true);
 // Adjust the URL as needed
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Product added to cart successfully!');
                    } else {
                        console.error('Error adding product to cart');
                    }
                }
            };
            xhr.send('product_id=' + productId);
        });
    });
});


</script>

