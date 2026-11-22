let OrderService = {
  users: {},
  current: null,
  isAdmin: function () {
    return UserService.getUser()?.role === Constants.ADMIN_ROLE;
  },
  isOpen: function (order) {
    return order.status === "pending" || order.status === "preparing";
  },
  init: function () {
    const admin = OrderService.isAdmin();
    $("#orders-title").text(admin ? "All Orders" : "My Orders");
    $("#new-order-btn").toggleClass("d-none", admin);
    $("#orders-user-col").toggleClass("d-none", !admin);
    if (admin) {
      RestClient.get("users", function (users) {
        users.forEach(function (u) { OrderService.users[u.id] = u.name; });
        OrderService.load();
      });
    } else {
      OrderService.load();
    }
  },
  badge: function (status) {
    const colors = { pending: "warning", preparing: "info", delivered: "success", cancelled: "secondary" };
    return $('<span class="badge"></span>').addClass("text-bg-" + (colors[status] ?? "secondary")).text(status);
  },
  load: function () {
    RestClient.get("orders", function (orders) {
      const body = $("#orders-list").empty();
      if (orders.length === 0) {
        body.append('<tr><td colspan="6" class="text-muted">No orders yet.</td></tr>');
        return;
      }
      orders.forEach(function (o) { body.append(OrderService.row(o)); });
    });
  },
  row: function (o) {
    const row = $("<tr></tr>");
    row.append($("<td></td>").text("#" + o.id));
    row.append($("<td></td>").text(String(o.created_at).substring(0, 10)));
    const userCell = $("<td></td>").text(OrderService.users[o.user_id] ?? "User #" + o.user_id);
    if (!OrderService.isAdmin()) userCell.addClass("d-none");
    row.append(userCell);
    row.append($("<td></td>").append(OrderService.badge(o.status)));
    row.append($("<td></td>").text(Number(o.total_amount).toFixed(2) + " KM"));
    const actions = $('<td class="text-end"></td>').appendTo(row);
    actions.append($('<button type="button" class="btn btn-outline-secondary btn-sm me-2"></button>').text("Details")
      .on("click", function () { OrderService.refreshDetails(o.id, true); }));
    if (OrderService.isAdmin()) {
      actions.append($('<button type="button" class="btn btn-outline-danger btn-sm"></button>').text("Delete")
        .on("click", function () { OrderService.removeOrder(o); }));
    }
    return row;
  },
  refreshDetails: function (id, show) {
    RestClient.get("orders/" + id, function (order) {
      OrderService.current = order;
      const open = OrderService.isOpen(order);
      $("#order-modal-title").text("Order #" + order.id);
      $("#order-total").text(Number(order.total_amount).toFixed(2) + " KM");
      $("#order-status-box").toggleClass("d-none", !OrderService.isAdmin());
      $("#order-status").val(order.status).prop("disabled", !open);
      $("#order-status-save").prop("disabled", !open);
      $("#order-add-box").toggleClass("d-none", !open);
      $("#order-closed-note").toggleClass("d-none", open);
      if (open) OrderService.loadProducts();
      RestClient.get("orders/" + id + "/items", function (items) {
        OrderService.renderItems(items, open);
        if (show) bootstrap.Modal.getOrCreateInstance($("#order-modal")[0]).show();
      });
    });
  },
  loadProducts: function () {
    RestClient.get("products", function (products) {
      const select = $("#order-product").empty();
      products.forEach(function (p) {
        select.append($("<option></option>").val(p.id)
          .text(p.name + " (" + Number(p.price).toFixed(2) + " KM, " + p.stock + " left)")
          .prop("disabled", Number(p.stock) < 1));
      });
    });
  },
  renderItems: function (items, open) {
    const body = $("#order-items-list").empty();
    if (items.length === 0) {
      body.append('<tr><td colspan="5" class="text-muted">No products in this order yet.</td></tr>');
      return;
    }
    items.forEach(function (item) {
      const row = $("<tr></tr>");
      row.append($("<td></td>").text(item.product_name));
      const qtyCell = $("<td></td>").appendTo(row);
      if (open) {
        qtyCell.append($('<input type="number" class="form-control form-control-sm w-75" min="1" max="100">')
          .val(item.quantity)
          .on("change", function () { OrderService.changeQuantity(item, $(this).val()); }));
      } else {
        qtyCell.text(item.quantity);
      }
      row.append($("<td></td>").text(Number(item.unit_price).toFixed(2) + " KM"));
      row.append($("<td></td>").text((item.quantity * item.unit_price).toFixed(2) + " KM"));
      const actions = $('<td class="text-end"></td>').appendTo(row);
      if (open) {
        actions.append($('<button type="button" class="btn btn-outline-danger btn-sm"></button>').text("Remove")
          .on("click", function () { OrderService.removeItem(item); }));
      }
      body.append(row);
    });
  },
  afterChange: function () {
    OrderService.refreshDetails(OrderService.current.id, false);
    OrderService.load();
  },
  changeQuantity: function (item, quantity) {
    RestClient.put("order-items/" + item.id, { quantity: quantity }, OrderService.afterChange, function (jqXHR) {
      toastr.error(jqXHR.responseJSON?.error ?? "Error");
      OrderService.afterChange();   // puts the old quantity back
    });
  },
  removeItem: function (item) {
    if (!confirm('Remove "' + item.product_name + '" from this order?')) return;
    RestClient.delete("order-items/" + item.id, null, OrderService.afterChange);
  },
  addItem: function () {
    const data = {
      order_id: OrderService.current.id,
      product_id: $("#order-product").val(),
      quantity: $("#order-quantity").val(),
    };
    RestClient.post("order-items", data, function () {
      toastr.success("Product added.");
      OrderService.afterChange();
    });
  },
  saveStatus: function () {
    const data = { user_id: OrderService.current.user_id, status: $("#order-status").val() };
    RestClient.put("orders/" + OrderService.current.id, data, function () {
      toastr.success("Status saved.");
      OrderService.afterChange();
    });
  },
  removeOrder: function (o) {
    if (!confirm("Delete order #" + o.id + "?")) return;
    RestClient.delete("orders/" + o.id, null, function () {
      toastr.success("Order deleted.");
      OrderService.load();
    });
  },
};

$(document).on("click", "#new-order-btn", function () {
  RestClient.post("orders", {}, function (order) {
    toastr.success("Order #" + order.id + " created. Add your products.");
    OrderService.load();
    OrderService.refreshDetails(order.id, true);
  });
});
$(document).on("submit", "#order-add-form", function (e) {
  e.preventDefault();
  OrderService.addItem();
});
$(document).on("click", "#order-status-save", function () { OrderService.saveStatus(); });