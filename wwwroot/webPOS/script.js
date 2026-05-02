const API_URL = "http://localhost:5189/api"; // change if needed

let cart = [];

// =====================
// LOGIN
// =====================
function login() {
    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    if (username === "admin" && password === "1234") {
        window.location.href = "dashboard.html";
    } else {
        alert("Invalid login");
    }
}

// =====================
// LOAD PRODUCTS
// =====================
async function loadProducts() {
    let res = await fetch(`${API_URL}/Product`);
    let products = await res.json();

    let html = "";
    products.forEach(p => {
        html += `
            <div class="product">
                <b>${p.name}</b><br>
                Price: ₱${p.price}<br>
                Stock: ${p.stock}<br>
                <button onclick="addToCart(${p.id}, '${p.name}', ${p.price})">Add</button>
            </div>
        `;
    });

    document.getElementById("products").innerHTML = html;
}

// =====================
// ADD TO CART
// =====================
function addToCart(id, name, price) {
    let item = cart.find(c => c.productId === id);

    if (item) {
        item.quantity++;
    } else {
        cart.push({ productId: id, name, price, quantity: 1 });
    }

    displayCart();
}

// =====================
// DISPLAY CART
// =====================
function displayCart() {
    let html = "";
    let total = 0;

    cart.forEach(c => {
        total += c.price * c.quantity;

        html += `
            <div>
                ${c.name} x${c.quantity} = ₱${c.price * c.quantity}
            </div>
        `;
    });

    document.getElementById("cart").innerHTML = html;
    document.getElementById("total").innerText = total;
}

// =====================
// CHECKOUT
// =====================
async function checkout() {
    let cash = parseFloat(document.getElementById("cash").value);

    let items = cart.map(c => ({
        productId: c.productId,
        quantity: c.quantity,
        price: c.price
    }));

    let res = await fetch(`${API_URL}/Order/checkout`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            cash: cash,
            items: items
        })
    });

    let data = await res.json();

    alert(`Change: ₱${data.order.change}`);

    cart = [];
    displayCart();
}

// =====================
// AUTO LOAD PRODUCTS
// =====================
if (window.location.pathname.includes("dashboard.html")) {
    loadProducts();
}