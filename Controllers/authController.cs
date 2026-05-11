using Microsoft.AspNetCore.Mvc;
using OrderingSystem.API.Models;

namespace OrderingSystem.API.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class AuthController : ControllerBase
    {
        // TEMP USERS (later mahimo ni SQL)
        private static List<User> users = new List<User>
        {
            new User { Id = 1, Username = "admin", Password = "1234" },
            new User { Id = 2, Username = "cashier", Password = "1234" }
        };

        [HttpPost("login")]
        public IActionResult Login(User login)
        {
            var user = users.FirstOrDefault(u =>
                u.Username == login.Username &&
                u.Password == login.Password);

            if (user == null)
                return Unauthorized("Invalid credentials");

            return Ok(new
            {
                message = "Login successful",
                userId = user.Id,
                username = user.Username
            });
        }
    }
}