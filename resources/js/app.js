import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import { createApp } from 'vue';
import CreateInstance from './components/CreateInstance.vue';
import TestApi from './components/TestApi.vue';

const app = createApp({});
app.component('create-instance', CreateInstance);
app.component('test-api', TestApi);
app.mount('#app');
