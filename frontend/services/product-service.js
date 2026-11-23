let ProductService = {
  isAdmin: function () {
    return UserService.getUser()?.role === Constants.ADMIN_ROLE;
  },
  init: function () {
    UserService.refreshNav();   // shows the admin-only button for admins
    ProductService.load();
  },
  load: function () {
    RestClient.get("products", function (products) {
      const list = $("#products-list").empty();
      if (products.length === 0) {
        list.append('<p class="text-muted">No products yet.</p>');
        return;
      }
      products.forEach(function (p) { list.append(ProductService.card(p)); });
    });
  },
    addToOrder: function (p) {
    RestClient.get("orders", function (orders) {
      const open = orders.find(function (o) { return o.status === "pending"; });
      const add = function (orderId) {
        RestClient.post("order-items", { order_id: orderId, product_id: p.id, quantity: 1 }, function () {
          toastr.success(p.name + " added to order #" + orderId + ".");
          ProductService.load();
        });
      };
      if (open) add(open.id);
      else RestClient.post("orders", {}, function (order) { add(order.id); });
    });
  },
    card: function (p) {
    const col = $('<div class="col-sm-6 col-lg-4"></div>');
    const card = $('<div class="card p-3 h-100"></div>').appendTo(col);
    card.append($("<h5></h5>").text(p.name));
    card.append($('<p class="text-muted mb-2"></p>').text(p.description ?? ""));
    const footer = $('<div class="d-flex justify-content-between align-items-center mt-auto"></div>').appendTo(card);
    footer.append($("<strong></strong>").text(Number(p.price).toFixed(2) + " KM"));
    footer.append($('<span class="text-muted small"></span>').text("In stock: " + p.stock));
    if (ProductService.isAdmin()) {
      const actions = $('<div class="mt-3 d-flex gap-2"></div>').appendTo(card);
      actions.append($('<button type="button" class="btn btn-outline-secondary btn-sm"></button>').text("Edit")
        .on("click", function () { ProductService.openModal(p); }));
      actions.append($('<button type="button" class="btn btn-outline-danger btn-sm"></button>').text("Delete")
        .on("click", function () { ProductService.remove(p); }));
    } else {
      const add = $('<button type="button" class="btn btn-specijal btn-sm mt-3"></button>').text("Add to order")
        .on("click", function () { ProductService.addToOrder(p); });
      if (Number(p.stock) < 1) add.prop("disabled", true).text("Out of stock");
      card.append(add);
    }
    return col;
  },
  openModal: function (product) {
    $("#product-form")[0].reset();
    $("#product-id").val(product ? product.id : "");
    $("#product-modal-title").text(product ? "Edit product" : "Add product");
    if (product) {
      $("#product-name").val(product.name);
      $("#product-description").val(product.description);
      $("#product-price").val(product.price);
      $("#product-stock").val(product.stock);
    }
    bootstrap.Modal.getOrCreateInstance($("#product-modal")[0]).show();
  },
  save: function () {
    const id = $("#product-id").val();
    const data = {
      name: $("#product-name").val(),
      description: $("#product-description").val(),
      price: $("#product-price").val(),
      stock: $("#product-stock").val(),
    };
    const done = function () {
      bootstrap.Modal.getOrCreateInstance($("#product-modal")[0]).hide();
      toastr.success("Product saved.");
      ProductService.load();
    };
    if (id) RestClient.put("products/" + id, data, done);
    else RestClient.post("products", data, done);
  },
  remove: function (p) {
    if (!confirm('Delete "' + p.name + '"?')) return;
    RestClient.delete("products/" + p.id, null, function () {
      toastr.success("Product deleted.");
      ProductService.load();
    });
  },
};

$(document).on("click", "#add-product-btn", function () { ProductService.openModal(null); });
$(document).on("submit", "#product-form", function (e) {
  e.preventDefault();
  ProductService.save();
});