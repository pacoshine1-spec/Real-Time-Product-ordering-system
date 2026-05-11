using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Drawing;
using System.IO;
using System.Linq;
using System.Windows.Forms;
using MySql.Data.MySqlClient;
using System.Net.Http;
namespace WinFormsOrderingSystem_Framework
{
    static class Program
    {
        [STAThread]
        static void Main()
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            Application.Run(new LoginForm());
        }
    }

    public class CurrentUser
    {
        public int Id;
        public string Name;
        public string Email;
        public string Role;
    }

    public class CartItem
    {
        public int ProductId;
        public string Name;
        public decimal Price;
        public int Qty;
    }

    public static class Db
    {
        public static string Conn
        {
            get { return ConfigurationManager.AppSettings["ConnectionString"] ?? "Server=localhost;Database=ordering_system;Uid=root;Pwd=;SslMode=none;"; }
        }

        public static DataTable Table(string sql, params MySqlParameter[] p)
        {
            using (var con = new MySqlConnection(Conn))
            using (var cmd = new MySqlCommand(sql, con))
            using (var da = new MySqlDataAdapter(cmd))
            {
                if (p != null) cmd.Parameters.AddRange(p);
                var dt = new DataTable();
                con.Open();
                da.Fill(dt);
                return dt;
            }
        }

        public static int Exec(string sql, params MySqlParameter[] p)
        {
            using (var con = new MySqlConnection(Conn))
            using (var cmd = new MySqlCommand(sql, con))
            {
                if (p != null) cmd.Parameters.AddRange(p);
                con.Open();
                return cmd.ExecuteNonQuery();
            }
        }

        public static object Scalar(string sql, params MySqlParameter[] p)
        {
            using (var con = new MySqlConnection(Conn))
            using (var cmd = new MySqlCommand(sql, con))
            {
                if (p != null) cmd.Parameters.AddRange(p);
                con.Open();
                return cmd.ExecuteScalar();
            }
        }
    }

    public static class Ui
    {
        public static readonly Font Title = new Font("Segoe UI", 18, FontStyle.Bold);
        public static readonly Font Header = new Font("Segoe UI", 12, FontStyle.Bold);
        public static readonly Font Normal = new Font("Segoe UI", 10);
        public static readonly Color Dark = Color.FromArgb(15, 23, 42);
        public static readonly Color Primary = Color.FromArgb(37, 99, 235);
        public static readonly Color Green = Color.FromArgb(22, 163, 74);
        public static readonly Color Bg = Color.FromArgb(248, 250, 252);

        public static Button Btn(string text, Color? color = null)
        {
            var b = new Button();
            b.Text = text;
            b.Height = 40;
            b.BackColor = color ?? Primary;
            b.ForeColor = Color.White;
            b.FlatStyle = FlatStyle.Flat;
            b.FlatAppearance.BorderSize = 0;
            b.Font = Normal;
            return b;
        }

        public static TextBox Txt(string text = "")
        {
            var t = new TextBox();
            t.Text = text;
            t.Height = 32;
            t.Font = Normal;
            return t;
        }

        public static Label Lbl(string text, int x, int y, int w, int h)
        {
            return new Label { Text = text, Left = x, Top = y, Width = w, Height = h, Font = Normal };
        }

        public static string ImagePath(string file)
        {
            if (string.IsNullOrWhiteSpace(file)) file = "default-food.png";
            var php = ConfigurationManager.AppSettings["PhpUploadsFolder"] ?? "";
            var p1 = Path.Combine(php, file);
            if (File.Exists(p1)) return p1;
            var p2 = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "Assets", "ProductImages", file);
            if (File.Exists(p2)) return p2;
            return Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "Assets", "ProductImages", "default-food.png");
        }
    }

    public class LoginForm : Form
    {
        TextBox email = Ui.Txt();
        TextBox pass = Ui.Txt();

        public LoginForm()
        {
            Text = "Ordering System - Login";
            Size = new Size(470, 360);
            StartPosition = FormStartPosition.CenterScreen;
            BackColor = Color.White;
            FormBorderStyle = FormBorderStyle.FixedSingle;
            MaximizeBox = false;

            pass.UseSystemPasswordChar = true;

            var title = new Label { Text = "Ordering System", Font = Ui.Title, Left = 40, Top = 35, Width = 380, Height = 38 };
            var sub = new Label { Text = "Login as admin, staff, or customer", Font = Ui.Normal, Left = 42, Top = 72, Width = 380, Height = 25, ForeColor = Color.Gray };
            var le = Ui.Lbl("Email", 40, 112, 390, 24);
            email.SetBounds(40, 138, 380, 34);
            var lp = Ui.Lbl("Password", 40, 180, 390, 24);
            pass.SetBounds(40, 206, 380, 34);
            var login = Ui.Btn("Login", Ui.Dark); login.SetBounds(40, 260, 180, 42);
            var reg = Ui.Btn("Register Customer", Ui.Green); reg.SetBounds(240, 260, 180, 42);
            login.Click += delegate { DoLogin(); };
            reg.Click += delegate { Register(); };
            Controls.AddRange(new Control[] { title, sub, le, email, lp, pass, login, reg });
        }

        void DoLogin()
        {
            try
            {
                var dt = Db.Table("SELECT * FROM users WHERE email=@e LIMIT 1", new MySqlParameter("@e", email.Text.Trim()));
                if (dt.Rows.Count == 0) { MessageBox.Show("Account not found."); return; }
                var r = dt.Rows[0];
                var hash = Convert.ToString(r["password"]);
                bool ok;
                if (!string.IsNullOrEmpty(hash) && hash.StartsWith("$2y$")) ok = BCrypt.Net.BCrypt.Verify(pass.Text, hash.Replace("$2y$", "$2a$"));
                else ok = pass.Text == hash;
                if (!ok) { MessageBox.Show("Wrong password."); return; }

                var u = new CurrentUser
                {
                    Id = Convert.ToInt32(r["id"]),
                    Name = Convert.ToString(r["name"]),
                    Email = Convert.ToString(r["email"]),
                    Role = Convert.ToString(r["role"])
                };
                Hide();
                new MainForm(u).ShowDialog();
                Show();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Database/Login error:\n" + ex.Message);
            }
        }

        void Register()
        {
            if (string.IsNullOrWhiteSpace(email.Text) || string.IsNullOrWhiteSpace(pass.Text)) { MessageBox.Show("Enter email and password first."); return; }
            string name = Prompt.Show("Full name:", "Register Customer", "Customer");
            if (string.IsNullOrWhiteSpace(name)) return;
            try
            {
                string hash = BCrypt.Net.BCrypt.HashPassword(pass.Text);
                Db.Exec("INSERT INTO users(name,email,password,role) VALUES(@n,@e,@p,'customer')",
                    new MySqlParameter("@n", name), new MySqlParameter("@e", email.Text.Trim()), new MySqlParameter("@p", hash));
                MessageBox.Show("Account created. You can login now.");
            }
            catch (Exception ex) { MessageBox.Show(ex.Message); }
        }
    }

    public static class Prompt
    {
        public static string Show(string text, string caption, string value)
        {
            var form = new Form { Width = 420, Height = 170, Text = caption, StartPosition = FormStartPosition.CenterParent, FormBorderStyle = FormBorderStyle.FixedDialog };
            var lbl = new Label { Left = 18, Top = 18, Width = 360, Text = text };
            var box = new TextBox { Left = 18, Top = 45, Width = 360, Text = value };
            var ok = new Button { Text = "OK", Left = 218, Width = 75, Top = 82, DialogResult = DialogResult.OK };
            var cancel = new Button { Text = "Cancel", Left = 303, Width = 75, Top = 82, DialogResult = DialogResult.Cancel };
            form.Controls.AddRange(new Control[] { lbl, box, ok, cancel });
            form.AcceptButton = ok; form.CancelButton = cancel;
            return form.ShowDialog() == DialogResult.OK ? box.Text : "";
        }
    }

    public class MainForm : Form
    {
        CurrentUser user;
        List<CartItem> cart = new List<CartItem>();
        FlowLayoutPanel productList = new FlowLayoutPanel();
        DataGridView productGrid = new DataGridView();
        ComboBox categoryFilter = new ComboBox();
        TextBox searchBox = Ui.Txt();

        public MainForm(CurrentUser u)
        {
            user = u;
            Text = "Ordering System - " + u.Name + " (" + u.Role + ")";
            WindowState = FormWindowState.Maximized;
            Font = Ui.Normal;
            BackColor = Ui.Bg;

            var tabs = new TabControl { Dock = DockStyle.Fill, Font = Ui.Normal };
            Controls.Add(tabs);
            tabs.TabPages.Add(Page("Products", ProductsPage()));
            tabs.TabPages.Add(Page("Cart / Checkout", CartPage()));
            tabs.TabPages.Add(Page("My Orders", OrdersPage(false)));
            if (user.Role == "admin" || user.Role == "staff")
            {
                tabs.TabPages.Add(Page("Admin Products", AdminProductsPage()));
                tabs.TabPages.Add(Page("Admin Orders", OrdersPage(true)));
            }
            Load += delegate { LoadProducts(); };
        }

        TabPage Page(string title, Control c)
        {
            var p = new TabPage(title);
            p.BackColor = Ui.Bg;
            p.Controls.Add(c);
            return p;
        }

        Control ProductsPage()
        {
            var p = new Panel { Dock = DockStyle.Fill, Padding = new Padding(12), BackColor = Ui.Bg };
            var top = new Panel { Dock = DockStyle.Top, Height = 70, BackColor = Color.White };
            var title = new Label { Text = "Product Catalog", Left = 15, Top = 12, Width = 220, Height = 28, Font = Ui.Header };
            searchBox.SetBounds(250, 18, 250, 32);
            categoryFilter.SetBounds(515, 18, 180, 32);
            categoryFilter.DropDownStyle = ComboBoxStyle.DropDownList;
            var load = Ui.Btn("Refresh", Ui.Dark); load.SetBounds(710, 14, 120, 40);
            load.Click += delegate { LoadProducts(); };
            searchBox.TextChanged += delegate { LoadProducts(); };
            categoryFilter.SelectedIndexChanged += delegate { LoadProducts(); };
            top.Controls.AddRange(new Control[] { title, searchBox, categoryFilter, load });
            productList.Dock = DockStyle.Fill;
            productList.AutoScroll = true;
            productList.Padding = new Padding(10);
            p.Controls.Add(productList);
            p.Controls.Add(top);
            return p;
        }

        void LoadProducts()
        {
            try
            {
                string cat = categoryFilter.SelectedItem == null ? "All" : Convert.ToString(categoryFilter.SelectedItem);
                var cats = Db.Table("SELECT DISTINCT category FROM products ORDER BY category");
                if (categoryFilter.Items.Count == 0)
                {
                    categoryFilter.Items.Add("All");
                    foreach (DataRow cr in cats.Rows) categoryFilter.Items.Add(Convert.ToString(cr["category"]));
                    categoryFilter.SelectedIndex = 0;
                }
                var sql = "SELECT * FROM products WHERE status='available'";
                var ps = new List<MySqlParameter>();
                if (!string.IsNullOrWhiteSpace(searchBox.Text)) { sql += " AND (name LIKE @q OR description LIKE @q OR category LIKE @q)"; ps.Add(new MySqlParameter("@q", "%" + searchBox.Text.Trim() + "%")); }
                if (!string.IsNullOrWhiteSpace(cat) && cat != "All") { sql += " AND category=@c"; ps.Add(new MySqlParameter("@c", cat)); }
                sql += " ORDER BY category,name";
                var dt = Db.Table(sql, ps.ToArray());
                productList.Controls.Clear();
                foreach (DataRow r in dt.Rows) productList.Controls.Add(ProductCard(r));
            }
            catch (Exception ex) { MessageBox.Show("Product loading error:\n" + ex.Message); }
        }

        Control ProductCard(DataRow r)
        {
            var card = new Panel { Width = 260, Height = 355, Margin = new Padding(12), BackColor = Color.White, BorderStyle = BorderStyle.FixedSingle };
            var img = new PictureBox { Width = 238, Height = 135, Left = 10, Top = 10, SizeMode = PictureBoxSizeMode.Zoom, ImageLocation = Ui.ImagePath(Convert.ToString(r["image"])) };
            var name = new Label { Text = Convert.ToString(r["name"]), Left = 12, Top = 155, Width = 235, Height = 28, Font = Ui.Header };
            var desc = new Label { Text = Convert.ToString(r["description"]), Left = 12, Top = 186, Width = 235, Height = 50, ForeColor = Color.DimGray };
            decimal price = Convert.ToDecimal(r["price"]);
            var meta = new Label { Text = "PHP " + price.ToString("N2") + "  |  Stock: " + Convert.ToString(r["stock"]), Left = 12, Top = 240, Width = 235, Height = 25, ForeColor = Ui.Green, Font = Ui.Header };
            var qty = new NumericUpDown { Left = 12, Top = 282, Width = 70, Minimum = 1, Maximum = 99, Value = 1 };
            var add = Ui.Btn("Add to Cart", Ui.Primary); add.SetBounds(95, 274, 150, 40);
            int id = Convert.ToInt32(r["id"]); string n = Convert.ToString(r["name"]);
            add.Click += delegate
            {
                var existing = cart.FirstOrDefault(x => x.ProductId == id);
                if (existing == null) cart.Add(new CartItem { ProductId = id, Name = n, Price = price, Qty = (int)qty.Value });
                else existing.Qty += (int)qty.Value;
                MessageBox.Show("Added to cart.");
            };
            card.Controls.AddRange(new Control[] { img, name, desc, meta, qty, add });
            return card;
        }

        Control CartPage()
        {
            var p = new Panel { Dock = DockStyle.Fill, Padding = new Padding(12), BackColor = Ui.Bg };
            var list = new ListBox { Dock = DockStyle.Fill, Font = new Font("Consolas", 11) };
            var top = new Panel { Dock = DockStyle.Top, Height = 60, BackColor = Color.White };
            var refresh = Ui.Btn("Refresh Cart", Ui.Dark); refresh.SetBounds(15, 10, 140, 40);
            var clear = Ui.Btn("Clear", Color.FromArgb(220, 38, 38)); clear.SetBounds(170, 10, 100, 40);
            var checkout = Ui.Btn("Checkout", Ui.Green); checkout.SetBounds(285, 10, 140, 40);
            refresh.Click += delegate { RefreshCart(list); };
            clear.Click += delegate { cart.Clear(); RefreshCart(list); };
            checkout.Click += delegate
            {
                if (cart.Count == 0) { MessageBox.Show("Cart is empty."); return; }
                Checkout();
                cart.Clear();
                RefreshCart(list);
                LoadProducts();
                MessageBox.Show("Order placed successfully.");
            };
            top.Controls.AddRange(new Control[] { refresh, clear, checkout });
            p.Controls.Add(list); p.Controls.Add(top);
            return p;
        }

        void RefreshCart(ListBox list)
        {
            list.Items.Clear();
            foreach (var i in cart) list.Items.Add(i.Qty + " x " + i.Name + " = PHP " + (i.Price * i.Qty).ToString("N2"));
            list.Items.Add("------------------------------------------------");
            list.Items.Add("TOTAL: PHP " + cart.Sum(x => x.Price * x.Qty).ToString("N2"));
        }

        void Checkout()
        {
            using (var con = new MySqlConnection(Db.Conn))
            {
                con.Open();
                using (var tx = con.BeginTransaction())
                {
                    var cmd = new MySqlCommand("INSERT INTO orders(user_id,total_amount,status) VALUES(@u,@t,'pending'); SELECT LAST_INSERT_ID();", con, tx);
                    cmd.Parameters.AddWithValue("@u", user.Id);
                    cmd.Parameters.AddWithValue("@t", cart.Sum(x => x.Price * x.Qty));
                    int oid = Convert.ToInt32(cmd.ExecuteScalar());
                    foreach (var i in cart)
                    {
                        var c = new MySqlCommand("INSERT INTO order_items(order_id,product_id,quantity,price) VALUES(@o,@p,@q,@pr); UPDATE products SET stock=GREATEST(stock-@q,0) WHERE id=@p;", con, tx);
                        c.Parameters.AddWithValue("@o", oid); c.Parameters.AddWithValue("@p", i.ProductId); c.Parameters.AddWithValue("@q", i.Qty); c.Parameters.AddWithValue("@pr", i.Price);
                        c.ExecuteNonQuery();
                    }
                    tx.Commit();
                }
            }
        }

        Control OrdersPage(bool admin)
        {
            var p = new Panel { Dock = DockStyle.Fill, Padding = new Padding(12), BackColor = Ui.Bg };
            var top = new Panel { Dock = DockStyle.Top, Height = 60, BackColor = Color.White };
            var load = Ui.Btn("Load Orders", Ui.Dark); load.SetBounds(15, 10, 130, 40);
            var status = new ComboBox { Left = 160, Top = 14, Width = 160, DropDownStyle = ComboBoxStyle.DropDownList };
            status.Items.AddRange(new object[] { "pending", "preparing", "ready", "completed", "cancelled" });
            var upd = Ui.Btn("Update Status", Ui.Primary); upd.SetBounds(335, 10, 145, 40);
            var details = Ui.Btn("View Details", Ui.Green); details.SetBounds(495, 10, 130, 40);
            top.Controls.AddRange(new Control[] { load, status, upd, details });
            var gv = new DataGridView { Dock = DockStyle.Fill, AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill, ReadOnly = true, SelectionMode = DataGridViewSelectionMode.FullRowSelect };
            load.Click += delegate
            {
                gv.DataSource = admin
                    ? Db.Table("SELECT o.id,u.name customer,o.total_amount,o.status,o.created_at FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC")
                    : Db.Table("SELECT id,total_amount,status,created_at FROM orders WHERE user_id=@u ORDER BY id DESC", new MySqlParameter("@u", user.Id));
            };
            upd.Click += delegate
            {
                if (!admin || gv.CurrentRow == null || status.SelectedItem == null) return;
                Db.Exec("UPDATE orders SET status=@s WHERE id=@id", new MySqlParameter("@s", status.SelectedItem), new MySqlParameter("@id", gv.CurrentRow.Cells["id"].Value));
                load.PerformClick();
            };
            details.Click += delegate
            {
                if (gv.CurrentRow == null) return;
                new OrderDetailsForm(Convert.ToInt32(gv.CurrentRow.Cells["id"].Value)).ShowDialog();
            };
            p.Controls.Add(gv); p.Controls.Add(top);
            return p;
        }

        Control AdminProductsPage()
        {
            var p = new Panel { Dock = DockStyle.Fill, Padding = new Padding(12), BackColor = Ui.Bg };
            var top = new Panel { Dock = DockStyle.Top, Height = 255, BackColor = Color.White };
            var name = Ui.Txt(); var cat = Ui.Txt(); var price = Ui.Txt(); var stock = Ui.Txt(); var img = Ui.Txt(); var desc = Ui.Txt();
            top.Controls.AddRange(new Control[] {
                Ui.Lbl("Name",10,10,180,22), Ui.Lbl("Category",230,10,120,22), Ui.Lbl("Price",380,10,100,22), Ui.Lbl("Stock",500,10,100,22), Ui.Lbl("Image filename",620,10,210,22),
                name, cat, price, stock, img, Ui.Lbl("Description",10,78,180,22), desc
            });
            name.SetBounds(10,34,210,32); cat.SetBounds(230,34,140,32); price.SetBounds(380,34,110,32); stock.SetBounds(500,34,110,32); img.SetBounds(620,34,230,32); desc.SetBounds(10,104,840,32);
            var choose = Ui.Btn("Choose Image", Ui.Primary); choose.SetBounds(10,154,130,40);
            var add = Ui.Btn("Add Product", Ui.Green); add.SetBounds(150,154,130,40);
            var update = Ui.Btn("Update Selected", Ui.Dark); update.SetBounds(290,154,150,40);
            var del = Ui.Btn("Delete Selected", Color.FromArgb(220,38,38)); del.SetBounds(450,154,150,40);
            var load = Ui.Btn("Load Products", Ui.Primary); load.SetBounds(610,154,140,40);
            top.Controls.AddRange(new Control[] { choose, add, update, del, load });
            productGrid.Dock = DockStyle.Fill;
            productGrid.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill;
            productGrid.SelectionMode = DataGridViewSelectionMode.FullRowSelect;
            productGrid.ReadOnly = true;
            p.Controls.Add(productGrid); p.Controls.Add(top);

            load.Click += delegate { productGrid.DataSource = Db.Table("SELECT id,name,description,category,price,image,stock,status FROM products ORDER BY id DESC"); };
            choose.Click += delegate
            {
                using (var ofd = new OpenFileDialog { Filter = "Images|*.png;*.jpg;*.jpeg;*.webp" })
                {
                    if (ofd.ShowDialog() == DialogResult.OK)
                    {
                        string folder = ConfigurationManager.AppSettings["PhpUploadsFolder"];
                        if (string.IsNullOrWhiteSpace(folder) || !Directory.Exists(folder)) folder = Path.Combine(AppDomain.CurrentDomain.BaseDirectory, "Assets", "ProductImages");
                        Directory.CreateDirectory(folder);
                        string dest = Path.Combine(folder, Path.GetFileName(ofd.FileName));
                        File.Copy(ofd.FileName, dest, true);
                        img.Text = Path.GetFileName(ofd.FileName);
                    }
                }
            };
            productGrid.SelectionChanged += delegate
            {
                if (productGrid.CurrentRow == null) return;
                name.Text = Convert.ToString(productGrid.CurrentRow.Cells["name"].Value);
                desc.Text = Convert.ToString(productGrid.CurrentRow.Cells["description"].Value);
                cat.Text = Convert.ToString(productGrid.CurrentRow.Cells["category"].Value);
                price.Text = Convert.ToString(productGrid.CurrentRow.Cells["price"].Value);
                stock.Text = Convert.ToString(productGrid.CurrentRow.Cells["stock"].Value);
                img.Text = Convert.ToString(productGrid.CurrentRow.Cells["image"].Value);
            };
            add.Click += delegate
            {
                if (!ValidateProduct(name, price, stock, img)) return;
                Db.Exec("INSERT INTO products(name,description,category,price,image,stock,status) VALUES(@n,@d,@c,@p,@i,@s,'available')",
                    new MySqlParameter("@n", name.Text), new MySqlParameter("@d", desc.Text), new MySqlParameter("@c", cat.Text), new MySqlParameter("@p", decimal.Parse(price.Text)), new MySqlParameter("@i", img.Text), new MySqlParameter("@s", int.Parse(stock.Text)));
                load.PerformClick();
            };
            update.Click += delegate
            {
                if (productGrid.CurrentRow == null || !ValidateProduct(name, price, stock, img)) return;
                Db.Exec("UPDATE products SET name=@n,description=@d,category=@c,price=@p,image=@i,stock=@s WHERE id=@id",
                    new MySqlParameter("@n", name.Text), new MySqlParameter("@d", desc.Text), new MySqlParameter("@c", cat.Text), new MySqlParameter("@p", decimal.Parse(price.Text)), new MySqlParameter("@i", img.Text), new MySqlParameter("@s", int.Parse(stock.Text)), new MySqlParameter("@id", productGrid.CurrentRow.Cells["id"].Value));
                load.PerformClick();
            };
            del.Click += delegate
            {
                if (productGrid.CurrentRow == null) return;
                if (MessageBox.Show("Delete selected product?", "Confirm", MessageBoxButtons.YesNo, MessageBoxIcon.Warning) == DialogResult.Yes)
                {
                    Db.Exec("DELETE FROM products WHERE id=@id", new MySqlParameter("@id", productGrid.CurrentRow.Cells["id"].Value));
                    load.PerformClick();
                }
            };
            load.PerformClick();
            return p;
        }

        bool ValidateProduct(TextBox name, TextBox price, TextBox stock, TextBox image)
        {
            decimal p; int s;
            if (string.IsNullOrWhiteSpace(name.Text)) { MessageBox.Show("Product name required."); return false; }
            if (!decimal.TryParse(price.Text, out p)) { MessageBox.Show("Invalid price."); return false; }
            if (!int.TryParse(stock.Text, out s)) { MessageBox.Show("Invalid stock."); return false; }
            if (string.IsNullOrWhiteSpace(image.Text)) { MessageBox.Show("Product image is required."); return false; }
            return true;
        }
    }

    public class OrderDetailsForm : Form
    {
        public OrderDetailsForm(int orderId)
        {
            Text = "Order Details #" + orderId;
            Size = new Size(760, 420);
            StartPosition = FormStartPosition.CenterParent;
            var gv = new DataGridView { Dock = DockStyle.Fill, AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill, ReadOnly = true };
            Controls.Add(gv);
            try
            {
                gv.DataSource = Db.Table("SELECT p.name product, oi.quantity, oi.price, (oi.quantity*oi.price) subtotal FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=@id",
                    new MySqlParameter("@id", orderId));
            }
            catch (Exception ex) { MessageBox.Show(ex.Message); }
        }
    }
}
