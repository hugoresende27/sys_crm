
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
const username = ref('');
const password = ref('');
const responseStatus = ref(0);


const login = async () => {

  try {
    const response = await axios.post(`${import.meta.env.VITE_API_HOST}/login`, {
      username: username.value,
      password: password.value,
    });

    
    if (response.status === 200) {
     
      window.location.href = '/dashboard';

    } 
    responseStatus.value = 200;
  } catch (error) {
    responseStatus.value = 401;
    console.error('Error:', error, responseStatus);
  }

    
};


const initializeLoginView = () => {
  onMounted(() => {
    // console.log(import.meta.env.VITE_API_HOST);
  });

  watch(responseStatus, (newValue) => {
    // console.log('Response Status Updated:', newValue);
    // Additional UI updates or actions based on newValue
  });

  
};



export { username, password, login, initializeLoginView, responseStatus};