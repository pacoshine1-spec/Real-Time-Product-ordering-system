function login() {

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    console.log("Sending:", username, password);

    fetch("http://localhost:5189/api/user/login", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            username: username,
            password: password
        })
    })
    .then(async res => {
        const data = await res.json();

        if (!res.ok) {
            document.getElementById("msg").innerText = data.message;
            return;
        }

        window.location.href = "/webPOS/dashboard.html";
    })
    .catch(err => {
        document.getElementById("msg").innerText = "Server not running";
    });
}