var AuthService = {
    register: function() {
        $("#signupForm").on("submit", function(event) {
        event.preventDefault();
        var formData = $(this).serializeArray();
        var formDataObject = {};
        $.each(formData, function(i, field){
            formDataObject[field.name] = field.value;
        });

        var jsonString = JSON.stringify(formDataObject);
        RestClient.post("auth/register", formData, function(response){
            if(response.success){
                alert("Successfully registered!");
                window.location.replace("http://localhost/Yapp/frontend/index.html#login");
            }
        })
    });
    }
}