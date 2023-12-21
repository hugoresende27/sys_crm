
// console.log( this.username.value, document.getElementById('pass').value)
const app = Vue.createApp({
  
    data(){
        return{
            username: '',
            pass: '',
        }
    },
    methods: {
        async login() {
         
            try {
                var data = JSON.stringify({
                    "username": this.username,
                    "password": this.pass
                  });
                  
                  var xhr = new XMLHttpRequest();
                  xhr.withCredentials = true;
                  
                  xhr.addEventListener("readystatechange", function() {
                    if(this.readyState === 4) {
                      console.log(this.responseText);
                    }
                  });
                  
                  xhr.open("POST", "http://localhost:8085/api/v1/login");
                  xhr.setRequestHeader("Content-Type", "application/json");
                  
                  xhr.send(data);
            } catch (error) {
                // Handle errors (e.g., network issues, server errors)
                console.error('Error:', error.message);
            }
        }
    },
    computed: {
        
    }
})

app.mount('#app')