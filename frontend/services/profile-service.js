let ProfileService = {
  init: function () {
    RestClient.get("auth/me", function (me) {
      $("#profile-name").val(me.name);
      $("#profile-email").val(me.email);
      $("#profile-role").val(me.role);
    });
  },
  save: function () {
    const data = { name: $("#profile-name").val(), email: $("#profile-email").val() };
    const password = $("#profile-password").val();
    if (password) data.password = password;
    RestClient.put("auth/me", data, function () {
      toastr.success("Profile saved.");
      $("#profile-password").val("");
    });
  },
};

$(document).on("submit", "#profile-form", function (e) {
  e.preventDefault();
  ProfileService.save();
});