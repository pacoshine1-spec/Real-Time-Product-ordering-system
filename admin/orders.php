<?php
require "../includes/auth.php";
require_role(["admin", "staff"]);
include "../includes/header.php";
?>
<span class="kicker">Live Monitoring</span>
<h2>Real-Time Orders</h2>
<p>This page automatically refreshes every 3 seconds.</p>
<div id="ordersTable">Loading orders...</div>

<script>
function loadOrders() {
    fetch("../ajax/fetch_orders.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("ordersTable").innerHTML = data;
        });
}
loadOrders();
setInterval(loadOrders, 3000);

function updateStatus(orderId, status) {
    const formData = new FormData();
    formData.append("order_id", orderId);
    formData.append("status", status);

    fetch("../ajax/update_order_status.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(() => loadOrders());
}
</script>
<?php include "../includes/footer.php"; ?>
