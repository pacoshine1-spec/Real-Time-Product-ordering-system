using Microsoft.AspNetCore.Mvc;
using OrderingSystem.API.Models;

namespace OrderingSystem.API.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class ProductController : ControllerBase
    {
        // TEMP DATABASE (in-memory)
       private static List<Product> products = new List<Product>
        {
             new Product { Id = 1, Name = "French fries", Price = 50.00m, Stock = 100 },
             new Product { Id = 2, Name = "Yum burger", Price = 60.00m, Stock = 100 },
             new Product { Id = 3, Name = "Pepperoni pizza", Price = 150.00m, Stock = 50 },
             new Product { Id = 4, Name = "Onion pizza", Price = 140.00m, Stock = 50 },
             new Product { Id = 5, Name = "Fruit juice", Price = 40.00m, Stock = 120 },
             new Product { Id = 6, Name = "Colaaa", Price = 30.00m, Stock = 150 },
             new Product { Id = 7, Name = "Redsi", Price = 30.00m, Stock = 150 },
             new Product { Id = 8, Name = "Boba tea", Price = 70.00m, Stock = 80 },
             new Product { Id = 9, Name = "Mushroom pizza", Price = 160.00m, Stock = 40 },
             new Product { Id = 10, Name = "Hawaii pizza", Price = 170.00m, Stock = 40 },
             new Product { Id = 11, Name = "Vegetable salad", Price = 90.00m, Stock = 60 }
        };

        // =========================
        // GET ALL PRODUCTS
        // =========================
        [HttpGet]
        public IActionResult GetAll()
        {
            return Ok(products);
        }

        // =========================
        // GET BY ID
        // =========================
        [HttpGet("{id}")]
        public IActionResult GetById(int id)
        {
            var product = products.FirstOrDefault(p => p.Id == id);
            if (product == null) return NotFound();

            return Ok(product);
        }

        // =========================
        // CREATE PRODUCT (POST)
        // =========================
        [HttpPost]
        public IActionResult Add(Product product)
        {
            product.Id = products.Count + 1;
            products.Add(product);

            return Ok(product);
        }

        // =========================
        // UPDATE PRODUCT (PUT)
        // =========================
        [HttpPut("{id}")]
        public IActionResult Update(int id, Product updated)
        {
            var product = products.FirstOrDefault(p => p.Id == id);
            if (product == null) return NotFound();

            product.Name = updated.Name;
            product.Price = updated.Price;
            product.Stock = updated.Stock;

            return Ok(product);
        }

        // =========================
        // DELETE PRODUCT
        // =========================
        [HttpDelete("{id}")]
        public IActionResult Delete(int id)
        {
            var product = products.FirstOrDefault(p => p.Id == id);
            if (product == null) return NotFound();

            products.Remove(product);

            return Ok("Product deleted successfully");
        }
    }
}