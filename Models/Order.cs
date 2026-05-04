namespace OrderingSystem.API.Models
{
    public class Order
    {
        public int Id { get; set; }
        public decimal Total { get; set; }
        public decimal Cash { get; set; }
        public decimal Change { get; set; }
        public DateTime CreatedAt { get; set; }
    }
}