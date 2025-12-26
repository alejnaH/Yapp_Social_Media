var AuthService = {
    register: function() {
        $("#signupForm").on("submit", function(event) {
            event.preventDefault();
            
            var formData = $(this).serializeArray();
            var formDataObject = {};
            $.each(formData, function(i, field){
                formDataObject[field.name] = field.value;
            });

            $.ajax({
                url: 'https://yapp-backend-b1a90d0d7142.herokuapp.com/' + "auth/register",
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
                        (XMLHttpRequest.responseJSON &&
                            XMLHttpRequest.responseJSON.message) ||
                        XMLHttpRequest.responseText ||
                        "Registration failed";
                    toastr.error(msg);
                }
            });
        });
    }
}