<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SHINE API Tester</title>
<style>
body{font-family:Segoe UI,Arial,sans-serif;background:#f1f5f9;margin:0;color:#0f172a}.wrap{max-width:1100px;margin:30px auto;padding:0 18px}.card{background:white;border-radius:16px;box-shadow:0 10px 30px rgba(15,23,42,.08);padding:20px;margin-bottom:18px}h1{margin:0 0 8px}.grid{display:grid;grid-template-columns:180px 1fr;gap:10px}select,input,textarea,button{font:inherit;padding:10px;border:1px solid #cbd5e1;border-radius:10px}textarea{min-height:150px;font-family:Consolas,monospace}button{background:#2563eb;color:white;border:none;cursor:pointer}.links a{display:inline-block;margin:5px 8px 5px 0;color:#2563eb}pre{background:#0f172a;color:#e2e8f0;padding:16px;border-radius:14px;overflow:auto;min-height:180px}</style>
</head>
<body><div class="wrap">
<div class="card"><h1>SHINE Ordering System API Tester</h1><p>Use this page in your browser to test GET, POST, PUT, and DELETE.</p><div class="links">
<a href="index.php">API Home</a><a href="products.php">View Products JSON</a><a href="orders.php">View Orders JSON</a><a href="users.php">View Users JSON</a>
</div></div>
<div class="card grid">
<label>Method</label><select id="method"><option>GET</option><option>POST</option><option>PUT</option><option>PATCH</option><option>DELETE</option></select>
<label>Endpoint</label><input id="url" value="products.php">
<label>JSON Body</label><textarea id="body">{
  "name": "Sample Product",
  "description": "Created from browser tester",
  "category": "Meals",
  "price": 99,
  "stock": 10,
  "status": "available",
  "image": "default-food.png"
}</textarea>
<label></label><button onclick="sendReq()">Send Request</button>
</div>
<div class="card"><b>Response</b><pre id="out">Waiting...</pre></div>
</div>
<script>
async function sendReq(){
  const method=document.getElementById('method').value;
  const url=document.getElementById('url').value;
  const body=document.getElementById('body').value.trim();
  const opt={method,headers:{'Content-Type':'application/json'}};
  if(method!=='GET' && method!=='DELETE' && body) opt.body=body;
  try{const res=await fetch(url,opt); const txt=await res.text(); try{document.getElementById('out').textContent=JSON.stringify(JSON.parse(txt),null,2)}catch(e){document.getElementById('out').textContent=txt}}
  catch(e){document.getElementById('out').textContent=e.message}
}
</script></body></html>
