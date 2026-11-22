let DashboardService = {
  init: function () {
    DashboardService.loadWeather();
    DashboardService.loadProducts();
  },
  loadWeather: function () {
    RestClient.get("weather", function (w) {
      $("#weather-temp").text(w.temperature + "°C");
      $("#weather-city").text(w.city + " · " + w.description);
      $("#weather-message").text(w.message);
    }, function () {
      $("#weather-temp").text("–");
      $("#weather-city").text("");
      $("#weather-message").text("The weather is not available right now, but you can still browse and order.");
    });
  },
  loadProducts: function () {
    RestClient.get("products", function (products) {
      const list = $("#dashboard-products").empty();
      products.slice(0, 5).forEach(p => list.append($("<li>").text(p.name)));
    });
  },
};