let ReviewService = {
  isAdmin: function () {
    return UserService.getUser()?.role === Constants.ADMIN_ROLE;
  },
  init: function () {
    RestClient.get("products", function (products) {
      const select = $("#review-product").empty();
      products.forEach(function (p) { select.append($("<option></option>").val(p.id).text(p.name)); });
      ReviewService.loadReviews(products);
    });
  },
  // GET /reviews has no user names, so the reviews of each product are loaded and merged
  loadReviews: function (products) {
    const all = [];
    let waiting = products.length;
    if (waiting === 0) { ReviewService.render(all); return; }
    const finish = function () {
      waiting--;
      if (waiting === 0) ReviewService.render(all);
    };
    products.forEach(function (p) {
      RestClient.get("products/" + p.id + "/reviews", function (reviews) {
        reviews.forEach(function (r) { r.product_name = p.name; all.push(r); });
        finish();
      }, finish);
    });
  },
  render: function (reviews) {
    const me = UserService.getUser();
    reviews.sort(function (a, b) {
      return String(b.created_at).localeCompare(String(a.created_at)) || b.id - a.id;
    });
    const list = $("#reviews-list").empty();
    if (reviews.length === 0) {
      list.append('<p class="text-muted">No reviews yet.</p>');
      return;
    }
    reviews.forEach(function (r) {
      const card = $('<div class="card p-3 mb-3"></div>');
      const head = $('<div class="d-flex justify-content-between"></div>').appendTo(card);
      head.append($("<strong></strong>").text(r.product_name));
      head.append($("<span></span>").text("\u2605".repeat(r.rating) + "\u2606".repeat(5 - r.rating)));
      const text = $('<p class="mb-0"></p>').appendTo(card);
      text.append(document.createTextNode((r.comment ?? "") + " "));
      text.append($('<span class="text-muted"></span>').text("\u2014 " + r.user_name));
      if (ReviewService.isAdmin() || Number(r.user_id) === Number(me.id)) {
        const actions = $('<div class="mt-2 d-flex gap-2"></div>').appendTo(card);
        actions.append($('<button type="button" class="btn btn-outline-secondary btn-sm"></button>').text("Edit")
          .on("click", function () { ReviewService.edit(r); }));
        actions.append($('<button type="button" class="btn btn-outline-danger btn-sm"></button>').text("Delete")
          .on("click", function () { ReviewService.remove(r); }));
      }
      list.append(card);
    });
  },
  edit: function (r) {
    $("#review-id").val(r.id);
    $("#review-product").val(r.product_id).prop("disabled", true);
    $("#review-rating").val(r.rating);
    $("#review-comment").val(r.comment ?? "");
    $("#review-form-title").text("Edit review");
    $("#review-submit").text("Save changes");
    $("#review-cancel").removeClass("d-none");
  },
  resetForm: function () {
    $("#review-form")[0].reset();
    $("#review-id").val("");
    $("#review-product").prop("disabled", false);
    $("#review-form-title").text("Write a review");
    $("#review-submit").text("Submit review");
    $("#review-cancel").addClass("d-none");
  },
  save: function () {
    const id = $("#review-id").val();
    const data = {
      product_id: $("#review-product").val(),
      rating: $("#review-rating").val(),
      comment: $("#review-comment").val(),
    };
    const done = function () {
      toastr.success("Review saved.");
      ReviewService.resetForm();
      ReviewService.init();
    };
    if (id) RestClient.put("reviews/" + id, data, done);
    else RestClient.post("reviews", data, done);
  },
  remove: function (r) {
    if (!confirm("Delete this review?")) return;
    RestClient.delete("reviews/" + r.id, null, function () {
      toastr.success("Review deleted.");
      ReviewService.init();
    });
  },
};

$(document).on("submit", "#review-form", function (e) {
  e.preventDefault();
  ReviewService.save();
});
$(document).on("click", "#review-cancel", function () { ReviewService.resetForm(); });