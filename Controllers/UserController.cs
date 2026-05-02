using Microsoft.AspNetCore.Mvc;
using OrderingSystem.API.Models;

namespace OrderingSystem.API.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    public class UserController : ControllerBase
    {
        private static List<User> users = new List<User>
        {
            new User { Id = 1, Username = "admin", Password = "1234" }
        };

        [HttpPost("login")]
        public IActionResult Login([FromBody] User request)
        {
            Console.WriteLine($"LOGIN TRY: {request.Username} / {request.Password}");

            var user = users.FirstOrDefault(u =>
                u.Username == request.Username &&
                u.Password == request.Password);

            if (user == null)
            {
                Console.WriteLine("FAILED LOGIN");
                return Unauthorized(new { message = "Invalid login" });
            }

            Console.WriteLine("SUCCESS LOGIN");

            return Ok(new { message = "success", user });
        }
    }
}