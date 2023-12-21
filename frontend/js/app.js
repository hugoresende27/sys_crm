

const app = Vue.createApp({
  
    data(){
        return{
            username: '',
            pass: '',
            error: null, 
        }
    },
    methods: {
        async login() {
            try {
                const response = await axios.post('http://localhost:8085/api/v1/login', {
                  username: this.username,
                  password: this.pass,
                });
                console.log(response.status);
                if (response.status == 200) {
                  document.cookie = `token_${this.username}=${response.data.token}; path=/`;       
                  document.cookie = `username=${this.username}; path=/`;        
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