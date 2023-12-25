const app = Vue.createApp({
    data() {
        return {
            usersData: '',
            moviesData: '',
            token: '',
            username: '',
            activePage: '',
            editedUser: {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    birth_date: '',
                },
        };
    },
    methods: {
        async getUsers() {
            this.setActivePage('users');
            try {
                let config = {
                    method: 'get',
                    maxBodyLength: Infinity,
                    url: 'http://localhost:8085/api/v1/user/users',
                    headers: {
                        'Authorization': this.token
                    }
                };

                const response = await axios.request(config);
                this.usersData = response.data;
            } catch (error) {
                window.location.href = '/';
            }

        
        },

        async addUser() {
            this.setActivePage('addUser');
        },

        async editUser(userId) {
            this.setActivePage('editUser');
    
                const user = this.usersData.find((user) => user.id === userId);
                this.editedUser.id = user.id;
                this.editedUser.name = user.name;
                this.editedUser.email = user.email;
                this.editedUser.phone = user.phone;
                this.editedUser.birth_date = user.birth_date;
            
        },

        async createUser() {

            const password = document.getElementById('pass').value;
            const confirmPassword = document.getElementById('pass2').value;

            if (password !== confirmPassword) {
                // Display an error message or take appropriate action
                console.error('Passwords do not match');
                alert('Passwords do not match');
                return;
            }

            try{
                let data = JSON.stringify({
                    "name": document.getElementById('name').value,
                    "password": document.getElementById('pass').value,
                    "email": document.getElementById('email').value,
                    "phone": document.getElementById('phone').value,
                    "birth_date": document.getElementById('birth_date').value
                });

                console.log(data);

                let config = {
                    method: 'post',
                    maxBodyLength: Infinity,
                    url: 'http://localhost:8085/api/v1/user/register',
                headers: 
                    {
                    'Authorization': this.token
                },
                    data : data
                };

                const response = axios.request(config)
                    .then((response) => {
                    console.log(JSON.stringify(response.data));
                    // window.location.href = 'dashboard.html';
                    alert(JSON.stringify(response.data));
                })
                    .catch((error) => {
                    alert(JSON.stringify(response.data));
                    console.log(error);
                });

            } catch (error) {
                console.log(error);
            }


        },
        
        async getToken() {
            this.setActivePage('token');
        },

        async getSettings() {
            this.setActivePage('settings');
        },

        async updateUser() {


            try {
            
                const updatedUserData = {
                    name: this.editedUser.name,
                    email: this.editedUser.email,
                    phone: this.editedUser.phone,
                    birth_date: this.editedUser.birth_date,
                
                };

                
                const response = await axios.put(`http://localhost:8085/api/v1/user/${this.editedUser.id}`, updatedUserData, {
                    headers: {
                        'Authorization': this.token,
                    },
                });

            
                alert((response.data));
    
                this.resetForm();
                this.getUsers();
            } catch (error) {
                // Handle errors, e.g., display an error message
                console.error('Error updating user:', error);
                alert('Error updating user. Please try again.');
            }
        },

        async getMovies() {
            this.setActivePage('movies');

            try {
                let config = {
                    method: 'get',
                    maxBodyLength: Infinity,
                    url: 'http://localhost:8085/api/v1/movie/trendings',
                    headers: {
                        'Authorization': this.token
                    }
                };

                const response = await axios.request(config);
                this.moviesData = response.data.results;
                this.moviesData = response.data.results.map(movie => {
                    return {
                        ...movie,
                        showFullOverview: false 
                    };
                });
                // console.log(this.moviesData)
            } catch (error) {
                window.location.href = '/';
            }
        },

        async getMoviesByGenre(genreId) {
            // this.setActivePage('movies');
            try {
                let config = {
                    method: 'get',
                    maxBodyLength: Infinity,
                    url: 'http://localhost:8085/api/v1/movie/genre?genre_id=' + genreId,
                    headers: {
                        'Authorization': this.token
                    }
                };

                const response = await axios.request(config);
                console.log(response);
                this.moviesData = response.data;
            } catch (error) {
                console.log(error)
                window.location.href = '/';
            }
        },

        async nowPlaying() {

            try {
                let config = {
                    method: 'get',
                    maxBodyLength: Infinity,
                    url: 'http://localhost:8085/api/v1/movie/playing',
                    headers: {
                        'Authorization': this.token
                    }
                };

                const response = await axios.request(config);
                this.moviesData = response.data;
            } catch (error) {
                console.log(error)
                window.location.href = '/';
            }
        },


        setActivePage(page) {
            this.activePage = page;
        },

        resetForm() {
        
            this.editedUser = {
                    id: null,
                    name: '',
                    email: '',
                    phone: '',
                    birth_date: '',
                }
        },

        deleteUser(userId) {
            console.log(`Delete user with ID ${userId}`);

            if (this.user_id == userId) {
                alert('Can not delete login user !');
                return ;
            }
            try{
                

                
                let config = {
                    method: 'delete',
                    maxBodyLength: Infinity,
                    url: 'http://localhost:8085/api/v1/user/'+ userId,
                headers: 
                    {
                        'Authorization': this.token
                    },
                };
                

                const response = axios.request(config)
                    .then((response) => {
                    console.log(JSON.stringify(response.data));
                    this.getUsers();
                    alert(JSON.stringify(response.data));
                })
                    .catch((error) => {
                    alert(JSON.stringify(response.data));
                    console.log(error);
                });

            } catch (error) {
                console.log(error);
            }
        },


        logout() {
            Cookies.remove(`token_${this.username}`);
            Cookies.remove(`token_created_${this.username}`);
            Cookies.remove(`token_expires_${this.username}`);
            Cookies.remove(`user_id`);
            Cookies.remove('username');
            window.location.href = 'index.html';
        },

        movieOverview(overview, title) {
            if (!alertify.myAlert) {
                // Define a new dialog
                alertify.dialog('myAlert', function () {
                    return {
                        main: function (message, title) {
                            this.message = message;
                            this.title = title;
                        },
                        setup: function () {
                            return {
                                buttons: [{ text: "OK", key: 27 /*Esc*/ }],
                                focus: { element: 0 }
                            };
                        },
                        prepare: function () {
                            this.setContent(this.message);
                            this.setHeader(this.title); // Set the title
                        }
                    };
                });
            }
            // Launch it with both the overview and title
            alertify.myAlert(overview, title);
        }
    },
    created() {
        this.user_id = Cookies.get(`user_id`);
        this.username = Cookies.get(`username`);
        this.token = Cookies.get(`token_${this.username}`);
        this.token_created_at = Cookies.get(`token_created_${this.username}`);
        this.token_expires_at = Cookies.get(`token_expires_${this.username}`);
    },
    computed: {
        isUsersPage() {
            return this.activePage === 'users';
        },
        isAddUserPage() {
            return this.activePage === 'addUser';
        },
        isTokenPage() {
            return this.activePage === 'token';
        },
        isMoviesPage() {
            return this.activePage === 'movies';
        },
        isSettingsPage() {
            return this.activePage === 'settings';
        },
    }
});

app.mount('#app');