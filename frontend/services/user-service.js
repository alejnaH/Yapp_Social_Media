var UserService = {
    init: function () {
        var token = localStorage.getItem("user_token");
        if (token) {
            if (typeof app !== "undefined" && app.navigateTo) {
                app.navigateTo("dashboard");
            } else {
                window.location.replace("index.html");
            }
            return; 
        }

        $("#loginForm").validate({
            submitHandler: function (form) {
                var entity = Object.fromEntries(new FormData(form).entries());
                UserService.login(entity);
            },
        });
    },

    login: function (entity) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "auth/login",
            type: "POST",
            data: JSON.stringify(entity),
            contentType: "application/json",
            dataType: "json",
            success: function (result) {
                console.log(result);
                if (!result || !result.data || !result.data.token) {
                    toastr.error("Invalid server response.");
                    return;
                }
                localStorage.setItem("user_token", result.data.token);

                toastr.success("Successfully logged in!");
                if (typeof app !== "undefined" && app.navigateTo) {
                    app.navigateTo("dashboard");
                } else {
                    window.location.replace("index.html");
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var msg =
                    (XMLHttpRequest.responseJSON &&
                        (XMLHttpRequest.responseJSON.message ||
                         XMLHttpRequest.responseJSON.error)) ||
                    XMLHttpRequest.responseText ||
                    "Error";
                toastr.error(msg);
            },
        });
    },

    logout: function () {
        localStorage.clear();
        window.location.replace("login.html");
    }
};
