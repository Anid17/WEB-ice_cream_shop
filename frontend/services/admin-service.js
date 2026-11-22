let AdminService = {
  products: {},
  categories: {},
  button: function (label, cls, handler) {
    return $('<button type="button" class="btn btn-sm ' + cls + '"></button>').text(label).on("click", handler);
  },
  fail: function (jqXHR) {
    toastr.error(jqXHR.responseJSON?.error ?? "Error");
  },
  init: function () {
    RestClient.get("products", function (products) {
      const select = $("#link-product").empty();
      products.forEach(function (p) {
        AdminService.products[p.id] = p.name;
        select.append($("<option></option>").val(p.id).text(p.name));
      });
      RestClient.get("categories", function (categories) {
        const select2 = $("#link-category").empty();
        categories.forEach(function (c) {
          AdminService.categories[c.id] = c.name;
          select2.append($("<option></option>").val(c.id).text(c.name));
        });
        AdminService.loadUsers();
        AdminService.loadLinks();
        AdminService.loadItems();
      });
    });
  },

  // ---- users ----
  loadUsers: function () {
    RestClient.get("users", function (users) {
      const body = $("#admin-users-list").empty();
      users.forEach(function (u) {
        const row = $("<tr></tr>");
        row.append($("<td></td>").text(u.id), $("<td></td>").text(u.name),
                   $("<td></td>").text(u.email), $("<td></td>").text(u.role));
        const cell = $('<td class="text-end"></td>').appendTo(row);
        cell.append(
          AdminService.button("Edit", "btn-outline-secondary me-2", function () { AdminService.openUserModal(u); }),
          AdminService.button("Delete", "btn-outline-danger", function () { AdminService.removeUser(u); })
        );
        body.append(row);
      });
    });
  },
  openUserModal: function (user) {
    $("#user-form")[0].reset();
    $("#user-id").val(user ? user.id : "");
    $("#user-modal-title").text(user ? "Edit user" : "Add user");
    $("#user-password").prop("required", !user);
    if (user) {
      $("#user-name").val(user.name);
      $("#user-email").val(user.email);
      $("#user-role").val(user.role);
    }
    bootstrap.Modal.getOrCreateInstance($("#user-modal")[0]).show();
  },
  saveUser: function () {
    const id = $("#user-id").val();
    const data = {
      name: $("#user-name").val(),
      email: $("#user-email").val(),
      role: $("#user-role").val(),
    };
    const password = $("#user-password").val();
    if (password) data.password = password;
    const done = function () {
      bootstrap.Modal.getOrCreateInstance($("#user-modal")[0]).hide();
      toastr.success("User saved.");
      AdminService.loadUsers();
    };
    if (id) RestClient.put("users/" + id, data, done);
    else RestClient.post("users", data, done);
  },
  removeUser: function (u) {
    if (!confirm('Delete user "' + u.name + '"?')) return;
    RestClient.delete("users/" + u.id, null, function () {
      toastr.success("User deleted.");
      AdminService.loadUsers();
    });
  },

  // ---- product categories (links between products and categories) ----
  loadLinks: function () {
    RestClient.get("product-categories", function (links) {
      const body = $("#admin-links-list").empty();
      links.forEach(function (l) {
        const row = $("<tr></tr>");
        row.append($("<td></td>").text(AdminService.products[l.product_id] ?? "Product #" + l.product_id),
                   $("<td></td>").text(AdminService.categories[l.category_id] ?? "Category #" + l.category_id));
        row.append($('<td class="text-end"></td>').append(
          AdminService.button("Remove", "btn-outline-danger", function () { AdminService.removeLink(l); })
        ));
        body.append(row);
      });
    });
  },
  addLink: function () {
    const data = { product_id: $("#link-product").val(), category_id: $("#link-category").val() };
    RestClient.post("product-categories", data, function () {
      toastr.success("Product added to the category.");
      AdminService.loadLinks();
    });
  },
  removeLink: function (l) {
    if (!confirm("Remove this product from the category?")) return;
    RestClient.delete("product-categories/" + l.id, null, function () {
      toastr.success("Removed.");
      AdminService.loadLinks();
    });
  },

  // ---- order items ----
  loadItems: function () {
    RestClient.get("order-items", function (items) {
      const body = $("#admin-items-list").empty();
      items.forEach(function (item) {
        const row = $("<tr></tr>");
        row.append($("<td></td>").text("#" + item.order_id),
                   $("<td></td>").text(AdminService.products[item.product_id] ?? "Product #" + item.product_id));
        row.append($("<td></td>").append(
          $('<input type="number" class="form-control form-control-sm w-50" min="1" max="100">').val(item.quantity)
            .on("change", function () { AdminService.changeItem(item, $(this).val()); })
        ));
        row.append($("<td></td>").text(Number(item.unit_price).toFixed(2) + " KM"),
                   $("<td></td>").text((item.quantity * item.unit_price).toFixed(2) + " KM"));
        row.append($('<td class="text-end"></td>').append(
          AdminService.button("Delete", "btn-outline-danger", function () { AdminService.removeItem(item); })
        ));
        body.append(row);
      });
    });
  },
  changeItem: function (item, quantity) {
    RestClient.put("order-items/" + item.id, { quantity: quantity }, function () {
      toastr.success("Quantity saved.");
      AdminService.loadItems();
    }, function (jqXHR) {
      AdminService.fail(jqXHR);
      AdminService.loadItems();
    });
  },
  removeItem: function (item) {
    if (!confirm("Delete this order item?")) return;
    RestClient.delete("order-items/" + item.id, null, function () {
      toastr.success("Order item deleted.");
      AdminService.loadItems();
    });
  },
};

$(document).on("click", "#add-user-btn", function () { AdminService.openUserModal(null); });
$(document).on("submit", "#user-form", function (e) {
  e.preventDefault();
  AdminService.saveUser();
});
$(document).on("submit", "#link-form", function (e) {
  e.preventDefault();
  AdminService.addLink();
});