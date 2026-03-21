import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import '../css/app.css'

// import StudentComponent from './components/StudentComponent.vue'
const app = createApp(App)

// createApp(StudentComponent).mount('#app');

app.use(router) //router register 
app.mount('#app')
