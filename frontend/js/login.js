

const app = Vue.createApp({
  
    data(){
        return{
            username: '',
            pass: '',
            error: null, 
            user_id: ''
        }
    },
    methods: {
        async login() {
            try {
                const response = await axios.post('http://localhost:8085/api/v1/login', {
                  username: this.username,
                  password: this.pass,
                });
           
  
                if (response.status == 200) {
            
                    Cookies.set(`token_${this.username}`, response.data.token, {
                      expires: new Date(response.data.expires_at),
                      path: '/',
                    });
          
                
                    Cookies.set(`username`, this.username, {
                      expires: new Date(response.data.expires_at),
                      path: '/',
                    });
          
                   
                    Cookies.set(`user_id`, response.data.user_id, {
                      expires: new Date(response.data.expires_at),
                      path: '/',
                    });
          
       
                    Cookies.set(`token_created_${this.username}`, response.data.created_at, {
                      expires: new Date(response.data.expires_at),
                      path: '/',
                    });
                   
                    Cookies.set(`token_expires_${this.username}`, response.data.expires_at, {
                      expires: new Date(response.data.expires_at),
                      path: '/',
                    });
          
                  window.location.href = 'dashboard.html';
                } 
              } catch (error) {
                // Handle errors (e.g., network issues, server errors)
                this.error = 'Invalid Credentials';
                console.error('Error:', error.message);
              }
        }
    },
    computed: {
        
    }
})

app.mount('#app')