import { ref, onMounted } from 'vue';
import axios from 'axios';
const username = ref('');
const password = ref('');

const login = async () => {
    try {
      const response = await axios.post('http://localhost:8085/api/v1/login', {
        username: username.value,
        password: password.value,
      }, {
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin' : '*'
        },
      });

      // const axios = require('axios');
      // let data = JSON.stringify({
      //   "username": "hug-3220",
      //   "password": "123456"
      // });

      // let config = {
      //   method: 'post',
      //   maxBodyLength: Infinity,
      //   url: 'http://localhost:8085/api/v1/login',
      //   headers: { 
      //     'Content-Type': 'application/json',
      //     'Access-Control-Allow-Origin' : '*'
      //   },
      //   data : data
      // };

      // axios.request(config)
      // .then((response) => {
      //   console.log(JSON.stringify(response.data));
      // })
      // .catch((error) => {
      //   console.log(error);
      // });

  
      // Handle the response
      console.log('Response:', response.data);
  
      // You might want to redirect or perform other actions based on the response
    } catch (error) {
      // Handle errors
      console.error('Error:', error.message);
    }
  };
  

const initializeLoginView = () => {
  onMounted(() => {
    console.log(import.meta.env.VITE_API_HOST);
  });
};



export { username, password, login, initializeLoginView };