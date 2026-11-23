let RestClient = {
  request: function (url, method, data, callback, error_callback) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + url,
      type: method,
      data: data ? JSON.stringify(data) : undefined,
      contentType: "application/json",
      beforeSend: function (xhr) {
        const token = localStorage.getItem("user_token");
        if (token) xhr.setRequestHeader("Authorization", "Bearer " + token);
      },
    })
      .done(function (response) {
        if (callback) callback(response);
      })
      .fail(function (jqXHR) {
        const message = jqXHR.responseJSON?.error ?? "Something went wrong.";
        if (jqXHR.status === 401) {
          localStorage.removeItem("user_token");
          toastr.error(message);
          window.location.hash = "#login";
          return;
        }
        if (jqXHR.status === 403) toastr.error("You are not allowed to do this.");
        if (error_callback) error_callback(jqXHR);
        else if (jqXHR.status !== 403) toastr.error(message);
      });
  },
  get:    function (url, cb, err)       { RestClient.request(url, "GET", null, cb, err); },
  post:   function (url, data, cb, err) { RestClient.request(url, "POST", data, cb, err); },
  put:    function (url, data, cb, err) { RestClient.request(url, "PUT", data, cb, err); },
  patch:  function (url, data, cb, err) { RestClient.request(url, "PATCH", data, cb, err); },
  delete: function (url, data, cb, err) { RestClient.request(url, "DELETE", data, cb, err); },
};