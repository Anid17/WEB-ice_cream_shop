let UserService = {
  isLoggedIn: function () {
    const payload = Utils.parseJwt(localStorage.getItem("user_token"));
    return !!payload && payload.exp * 1000 > Date.now();
  },
  getUser: function () {
    return UserService.isLoggedIn()
      ? Utils.parseJwt(localStorage.getItem("user_token")).user
      : null;
  },
  login: function (entity) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/login",
      type: "POST",
      data: JSON.stringify(entity),
      contentType: "application/json",
      success: function (result) { UserService.saveSession(result.token); },
      error: function (jqXHR) { toastr.error(jqXHR.responseJSON?.error ?? "Error"); },
    });
  },
  register: function (entity) {
    RestClient.post("auth/register", entity, function (result) {
      UserService.saveSession(result.token);
    });
  },
  saveSession: function (token) {
    localStorage.setItem("user_token", token);
    window.location.hash = "#dashboard";
    window.location.reload();   // views are cached, so reload to start clean for this user
  },
  logout: function () {
    localStorage.removeItem("user_token");
    window.location.hash = "#login";
    window.location.reload();
  },
  refreshNav: function () {
    const user = UserService.getUser();
    $(".guest-only").toggleClass("d-none", !!user);
    $(".auth-only").toggleClass("d-none", !user);
    $(".admin-only").toggleClass("d-none", !(user && user.role === Constants.ADMIN_ROLE));
  },
};

// Views are injected by app.js, so the handlers are attached to document
$(document).on("submit", "#login-form", function (e) {
  e.preventDefault();
  UserService.login(Object.fromEntries(new FormData(this).entries()));
});
$(document).on("submit", "#register-form", function (e) {
  e.preventDefault();
  UserService.register(Object.fromEntries(new FormData(this).entries()));
});
$(document).on("click", "#logout-link", function (e) {
  e.preventDefault();
  UserService.logout();
});
$(document).ready(UserService.refreshNav);