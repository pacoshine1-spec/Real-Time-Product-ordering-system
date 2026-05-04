using Microsoft.EntityFrameworkCore;
using OrderingSystem.API.Data;

var builder = WebApplication.CreateBuilder(args);

// =========================
// ADD SERVICES
// =========================
builder.Services.AddControllers();

// 👉 IBUTANG DIRI (IMPORTANT)
builder.Services.AddDbContext<AppDbContext>(options =>
    options.UseSqlServer(builder.Configuration.GetConnectionString("DefaultConnection")));

builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

// =========================
// BUILD APP
// =========================
var app = builder.Build();

if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();

app.UseAuthorization();
app.UseDefaultFiles();
app.UseStaticFiles();
app.MapControllers();

app.Run();