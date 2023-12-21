<template>
  <div>
    <aside>
      <nav>
        <ul>
          <li>
            <router-link to="/">Home</router-link>
          </li>
          <li>
            <a @click="logout">Logout</a>
          </li>
          <li>
            <router-link to="/dashboard/users">Users</router-link>
          </li>
          <li>
            <router-link to="/dashboard/users">Users</router-link>
          </li>
          <li>
            <router-link to="/dashboard/users">Users</router-link>
          </li>
          <!-- Add more menu items as needed -->
        </ul>
      </nav>
    </aside>
    <main>
      <!-- Your dashboard content goes here -->
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { responseStatus } from '@/utils/login';

function logout() {
  responseStatus.value = 0;
  window.location.href = '/';
  console.log('here');
}


responseStatus.value = 200;

</script>

<style scoped>
  /* Add your styling for the dashboard here */
  aside {
    width: 200px;
    background-color: #f0f0f0;
    padding: 10px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
  }

  nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  nav li {
    margin-bottom: 10px;
  }

  nav a {
    text-decoration: none;
    color: #333;
  }
</style>
