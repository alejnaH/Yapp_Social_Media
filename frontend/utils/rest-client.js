let RestClient = {
   get: function (url, callback, error_callback) {
     $.ajax({
       url: Constants.PROJECT_BASE_URL + url,
       type: "GET",
       beforeSend: function (xhr) {
         const token = localStorage.getItem("user_token");
         if (token) {
           xhr.setRequestHeader("Authentication", token);
         }
       },
       success: function (response) {
         if (callback) callback(response);
       },
       error: function (jqXHR, textStatus, errorThrown) {
         if (error_callback) {
           error_callback(jqXHR);
         } else {
           var msg = (jqXHR.responseJSON && jqXHR.responseJSON.message) || 
                     textStatus || 
                     "Request failed";
           toastr.error(msg);
         }
       },
     });
   },
   
  request: function (url, method, data, callback, error_callback) {
      const isJsonData = typeof data === 'string' && (data.startsWith('{') || data.startsWith('['));
      
      $.ajax({
          url: Constants.PROJECT_BASE_URL + url,
          type: method,
          beforeSend: function (xhr) {
              const token = localStorage.getItem("user_token");
              if (token) {
                  xhr.setRequestHeader("Authentication", token);
              }
              if (isJsonData) {
                  xhr.setRequestHeader("Content-Type", "application/json");
              }
          },
          data: data,
          dataType: 'text'
      })
      .done(function (responseText, status, jqXHR) {
          let response;
          try {
              response = JSON.parse(responseText.trim());
          } catch (e) {
              response = responseText;
          }
          if (callback) callback(response);
      })
      .fail(function (jqXHR, textStatus, errorThrown) {
          if (error_callback) {
              error_callback(jqXHR);
          } else {
              var msg = (jqXHR.responseJSON && jqXHR.responseJSON.message) || 
                        textStatus || 
                        "Request failed";
              toastr.error(msg);
          }
      });
  },
   
   post: function (url, data, callback, error_callback) {
     RestClient.request(url, "POST", data, callback, error_callback);
   },
   
   delete: function (url, data, callback, error_callback) {
     RestClient.request(url, "DELETE", data, callback, error_callback);
   },
   
   patch: function (url, data, callback, error_callback) {
     RestClient.request(url, "PATCH", data, callback, error_callback);
   },
   
   put: function (url, data, callback, error_callback) {
     RestClient.request(url, "PUT", data, callback, error_callback);
   },
 };
