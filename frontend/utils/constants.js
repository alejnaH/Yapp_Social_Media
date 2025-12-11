let Constants = {
   PROJECT_BASE_URL: (() => {
       const hostname = window.location.hostname;
       const port = window.location.port;

       if (port && port !== '80' && port !== '443') {
           return `http://${hostname}:8000/`;
       }

       return 'http://localhost/Yapp/backend/';
   })(),
   
   USER_ROLE: "user",
   ADMIN_ROLE: "admin"
}