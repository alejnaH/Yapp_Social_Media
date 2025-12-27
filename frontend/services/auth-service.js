var AuthService = {
    register: function() {
        // Initialize jQuery Validation
        $("#signupForm").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3,
                    maxlength: 6
                },
                fullname: {
                    required: true,
                    minlength: 2,
                    maxlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
                password2: {
                    required: true,
                    minlength: 6,
                    equalTo: "#form3Example4c"  // matches password field
                }
            },
            messages: {
                username: {
                    required: "Please enter a username",
                    minlength: "Username must be at least 3 characters",
                    maxlength: "Username cannot exceed 6 characters"
                },
                fullname: {
                    required: "Please enter your full name",
                    minlength: "Full name must be at least 2 characters",
                    maxlength: "Full name cannot exceed 10 characters"
                },
                email: {
                    required: "Please enter your email",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please enter a password",
                    minlength: "Password must be at least 6 characters"
                },
                password2: {
                    required: "Please confirm your password",
                    minlength: "Password must be at least 6 characters",
                    equalTo: "Passwords do not match"
                }
            },
            // Only called if form is valid
            submitHandler: function(form) {
                var formData = $(form).serializeArray();
                var formDataObject = {};
                $.each(formData, function(i, field){
                    formDataObject[field.name] = field.value;
                });

                // Show loading overlay
                $.blockUI({ message: '<h3>Creating account...</h3>' });

                $.ajax({
                    url: PROJECT_BASE_URL + "auth/register",
                    type: "POST",
                    data: formDataObject,
                    dataType: "json",
                    success: function(response){
                        console.log("Register response:", response);
                        if(response.success){
                            toastr.success("Successfully registered! Please login.");
                            setTimeout(function() {
                                window.location.hash = "#login";
                            }, 1000);
                        } else {
                            toastr.error(response.message || "Registration failed");
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        console.error("Register error:", XMLHttpRequest);
                        var msg =
                            (XMLHttpRequest.responseJSON && XMLHttpRequest.responseJSON.message) ||
                            XMLHttpRequest.responseText ||
                            "Registration failed";
                        toastr.error(msg);
                    },
                    complete: function() {
                        $.unblockUI(); // Remove loading overlay
                    }
                });
            }
        });
    }
};
