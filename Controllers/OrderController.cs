using Microsoft.AspNetCore.Mvc;
using OrderingSystem.API.Models;

namespace OrderingSystem.API.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class OrderController : ControllerBase
    {
        // TEMP STORAGE (no database yet)
        private static List<Order> orders = new List<Order>();
        private static List<OrderItem> orderItems = new List<OrderItem>();

        // =========================
        // GET ALL ORDERS
        // =========================
        [HttpGet]
        public IActionResult GetAll()
        {
            return Ok(orders);
        }

        // =========================
        // CREATE ORDER (CHECKOUT)
        // =========================
        [HttpPost("checkout")]
        public IActionResult Checkout([FromBody] OrderRequest request)
        {
            // 1. Compute total
            decimal total = 0;

            foreach (var item in request.Items)
            {
                total += item.Price * item.Quantity;
            }

            // 2. Compute change
            decimal change = request.Cash - total;

            if (change < 0)
            {
                return BadRequest("Insufficient cash!");
            }

            // 3. Create Order
            var order = new Order
            {
                Id = orders.Count + 1,
                Total = total,
                Cash = request.Cash,
                Change = change,
                CreatedAt = DateTime.Now
            };

            orders.Add(order);

            // 4. Save Items
            foreach (var item in request.Items)
            {
                orderItems.Add(new OrderItem
                {
                    Id = orderItems.Count + 1,
                    OrderId = order.Id,
                    ProductId = item.ProductId,
                    Quantity = item.Quantity,
                    Price = item.Price
                });
            }

            return Ok(new
            {
                Message = "Order successful",
                Order = order,
                Items = request.Items
            });
        }
    }

    // =========================
    // REQUEST MODEL (FOR CHECKOUT)
    // =========================
    public class OrderRequest
    {
        public decimal Cash { get; set; }
        public List<OrderItemRequest> Items { get; set; }
    }

    public class OrderItemRequest
    {
        public int ProductId { get; set; }
        public int Quantity { get; set; }
        public decimal Price { get; set; }
    }
}