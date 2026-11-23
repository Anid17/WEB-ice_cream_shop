let CategoryService = {
  isAdmin: function () {
    return UserService.getUser()?.role === Constants.ADMIN_ROLE;
  },
  init: function () {
    UserService.refreshNav();
    CategoryService.load();
  },
  load: function () {
    RestClient.get("categories", function (categories) {
      const body = $("#categories-list").empty();
      categories.forEach(function (c) {
        const row = $("<tr></tr>");
        row.append($("<td></td>").text(c.id));
        row.append($("<td></td>").text(c.name));
        if (CategoryService.isAdmin()) {
          const cell = $('<td class="text-end"></td>').appendTo(row);
          cell.append($('<button type="button" class="btn btn-outline-secondary btn-sm me-2"></button>').text("Edit")
            .on("click", function () { CategoryService.openModal(c); }));
          cell.append($('<button type="button" class="btn btn-outline-danger btn-sm"></button>').text("Delete")
            .on("click", function () { CategoryService.remove(c); }));
        }
        body.append(row);
      });
    });
  },
  openModal: function (category) {
    $("#category-form")[0].reset();
    $("#category-id").val(category ? category.id : "");
    $("#category-modal-title").text(category ? "Edit category" : "Add category");
    if (category) $("#category-name").val(category.name);
    bootstrap.Modal.getOrCreateInstance($("#category-modal")[0]).show();
  },
  save: function () {
    const id = $("#category-id").val();
    const data = { name: $("#category-name").val() };
    const done = function () {
      bootstrap.Modal.getOrCreateInstance($("#category-modal")[0]).hide();
      toastr.success("Category saved.");
      CategoryService.load();
    };
    if (id) RestClient.put("categories/" + id, data, done);
    else RestClient.post("categories", data, done);
  },
  remove: function (c) {
    if (!confirm('Delete "' + c.name + '"?')) return;
    RestClient.delete("categories/" + c.id, null, function () {
      toastr.success("Category deleted.");
      CategoryService.load();
    });
  },
};

$(document).on("click", "#add-category-btn", function () { CategoryService.openModal(null); });
$(document).on("submit", "#category-form", function (e) {
  e.preventDefault();
  CategoryService.save();
});