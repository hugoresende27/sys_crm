<template>
  <main>
    <form class="login-form" @submit.prevent="login">
      <label for="username">Username:</label>
      <input type="text" v-model="username" id="username" required />

      <label for="password">Password:</label>
      <input type="password" v-model="password" id="password" required />

      <button type="submit">Login</button>
    </form>
    
    <p v-if="responseStatus == 401" class="error-message">Wrong Credentials</p>


  
  </main>
</template>

<style scoped>

.error-message {
    color: red;
    font-weight: bolder;
    margin-top: 10px;
    text-align: center;
  }

.main {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100vh;
}

.login-form {
  max-width: 300px;
  margin: auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

label {
  display: block;
  margin-bottom: 8px;
}

input {
  width: 100%;
  padding: 8px;
  margin-bottom: 16px;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}

button:hover {
  background-color: #45a049;
}
</style>

<script setup lang="ts">
import { username, password, login, initializeLoginView, responseStatus } from '@/utils/login';
console.log(responseStatus)


initializeLoginView();
</script>
