<?php
    include("../../config/dbconfig.php");
    session_start();

    if (!isset($_SESSION['user'])) {
        header("Location: ../index.php");
        exit;
    }

    $user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cafe Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #f8d9da;
      overflow-y: auto;
    }
    .sidebar a {
      color: #0b0b0a;
      padding: 15px;
      display: block;
      text-decoration: none;
    }
    .sidebar a:hover {
      background:radial-gradient(circle at left, #f8d9da, #F6C2C4, #f8d9da);
      
      width: 100%;
    }
    .menu-card img {
      height: 150px;
      object-fit: cover;
    }
    @font-face {
      font-family: 'FeelingPassionate';
      src: url(../../FeelingPassionateRegular-gxp34.ttf);
    }
    .feeling-passionate {
      font-family: 'FeelingPassionate', cursive;
    }
  </style>
</head>
<body class="bg-s">

<div class="d-flex">
  <div class="sidebar d-flex flex-column p-3">
    <h4 class="text-black text-center mt-3 mb-5 ms-2 me-2 feeling-passionate" >the meeting place</h4>
    <h6 class="text-black mb-4">Welcome, <?= $user['name'] ?> </h6>
    <h6 class="text-black-50">Home</h6>
        <a href="../user/dashboard.php">Dashboard</a>
    <h6 class="text-black-50">Product Management</h6>
        <a href="#">Inventory</a>
    <h6 class="text-black-50">Costumer Management</h6>
        <a href="#">Transactions</a>
    <h6 class="text-black-50">Control Center</h6>
        <a href="#">Settings</a>
        <a href="../../root/logout.php">Logout</a>
  </div>

  <div class="flex-grow-1 p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Customer's Order</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">+ Add Order</button>
    </div>
    <div id="order-list" class="mt-4">
      <p class="text-muted">No items yet.</p>
    </div>
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Select Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row" id="product-list">

                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mt-4">
      <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    </div>
  </div>
</div>
<script>
    $('#orderModal').on('show.bs.modal', function () {
        $.ajax({
            url: '../user/products.php',
            method: 'GET',
            success: function (response) {
                $('#product-list').html(response);
            },
            error: function () {
                alert("Error loading products.");
            }
        });
    });
    $(document).on('click', '.add-to-cart', function () {
        var productId = $(this).data('id');
        var card = $(this).closest('.card');
        var name = card.find('.card-title').text();
        var priceText = card.find('.card-text strong:contains("Price")').parent().text();
        var price = parseFloat(priceText.replace(/[^0-9\.]+/g,""));
        var quantityInput = card.find('.quantity-input');
        var quantity = quantityInput.length ? parseInt(quantityInput.val()) : 1;
        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "addorder.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        console.log(response);

                        var toastElement = document.getElementById('cartToast');
                        var cartToast = new bootstrap.Toast(toastElement);
                        cartToast.show();

                        loadOrderList();
                    } catch (e) {
                        console.error("Failed to parse JSON response:", e);
                        alert("Failed to add product: Invalid server response.");
                    }
                } else {
                    console.error("Request failed with status", xhr.status, xhr.responseText);
                    alert("Failed to add product. Server error.");
                }
            }
        };

        var params = "product_id=" + encodeURIComponent(productId) +
                     "&name=" + encodeURIComponent(name) +
                     "&price=" + encodeURIComponent(price) +
                     "&quantity=" + encodeURIComponent(quantity);

        xhr.send(params);
    });
    function loadOrderList() {
        $.ajax({
            url: 'loadorder.php',
            method: 'GET',
            success: function (response) {
                $('#order-list').html(response);
            },
            error: function () {
                $('#order-list').html("<p class='text-danger'>Failed to load order list.</p>");
            }
        });
    }
    $(document).ready(function() {
        loadOrderList();
    });
</script>
<?php if (isset($_GET['modal']) && $_GET['modal'] === 'open'): ?>
  <script>
      var myModal = new bootstrap.Modal(document.getElementById('orderModal'));
      myModal.show();
  </script>
<?php endif; ?>
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
  <div id="cartToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        Product added to cart!
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
</body>
</html>
