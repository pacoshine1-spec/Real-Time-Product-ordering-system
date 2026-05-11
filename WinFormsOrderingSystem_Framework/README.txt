WINDOWS FORMS ORDERING SYSTEM (.NET FRAMEWORK 4.8)

This is the Windows Forms version of your PHP ordering system.
It uses the SAME MySQL database: ordering_system

FEATURES
- Login/Register customer
- Admin/staff/customer role support
- Product catalog with pictures
- Search and category filter
- Add to cart and checkout
- My orders page
- Admin products page: add, update, delete products
- Admin image upload for products
- Admin orders page: view and update order status
- Order details viewer

REQUIREMENTS
1. Visual Studio 2022, not VS Code only, because this is Windows Forms .NET Framework.
2. .NET Framework 4.7 Developer Pack.
3. XAMPP with Apache and MySQL running.
4. Your PHP system database imported in phpMyAdmin.

HOW TO RUN
1. Extract this ZIP.
2. Open WinFormsOrderingSystem_Framework.sln in Visual Studio 2022.
3. Right click Solution > Restore NuGet Packages.
4. Open App.config and check this line:
   ConnectionString = Server=localhost;Database=ordering_system;Uid=root;Pwd=;SslMode=none;

   If your MySQL has password, change Pwd=; to your password.

5. Also check PhpUploadsFolder:
   C:\xampp\htdocs\ordering_system\uploads

   Change it to your real PHP uploads folder, for example:
   C:\xampp\htdocs\realtime_product_ordering_system\uploads

6. Press Start / F5.

DEFAULT ACCOUNTS FROM YOUR DATABASE
Admin:
admin@example.com / admin123

Staff:
staff@example.com / staff123

Customer:
customer@example.com / customer123

IMPORTANT
- This app will connect to the SAME database used by your PHP system.
- If you add products here, they will also show in PHP.
- If you add products in PHP, they will also show here.
- Product images are loaded from the PHP uploads folder first. If not found, the app uses local Assets/ProductImages.


UPDATED COMPATIBILITY:
This version targets .NET Framework 4.7 and has the MSBuild import placed correctly at the bottom of the project file to avoid the project being unloaded in Visual Studio 2022 when 4.8 SDK is unavailable.
