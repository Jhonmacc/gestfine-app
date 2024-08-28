import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import { createApp } from 'vue';
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import CreateInstance from './components/CreateInstance.vue';
import SendMessageApi from './components/SendMessageApi.vue';
import SelectInstance from './components/SelectInstance.vue';
import MultiSelect from './components/MultiSelect.vue';
import TestApi from './components/TestApi.vue';
import '@mdi/font/css/materialdesignicons.min.css';

// Inicializando Alpine.js
window.Alpine = Alpine;
Alpine.plugin(persist);
Alpine.start();

// Inicializando Vue.js
const app = createApp({
    methods: {
      async updateInstance({ paramId, instance }) {
        // Recupera o token CSRF
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) {
          console.error('Token CSRF não encontrado.');
          return;
        }
        const csrfToken = csrfTokenMeta.getAttribute('content');

        try {
          const response = await axios.post('/parametros/update', {
            _token: csrfToken,
            parametros: {
              [paramId]: {
                instancia: instance
              }
            }
          });

          if (response.data.success) {
            await swal({
              title: "Sucesso!",
              text: response.data.message,
              icon: "success",
              timer: 2000,
              buttons: false
            });
            location.reload();
          } else {
            await swal({
              title: "Erro!",
              text: response.data.message,
              icon: "error",
            });
          }
        } catch (error) {
          console.error('Erro ao atualizar instância:', error);
          await swal({
            title: "Erro!",
            text: "Ocorreu um erro ao atualizar a instância.",
            icon: "error",
          });
        }
      }
    }
  });

// >>Modo EstiloModo<<
//estilo fornece componentes pré-skinned, tema padrão é Aura com esmeralda como a cor primária. Veja o modo estilo documentação para detalhes.
  app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});
// >>Sem estilo do primevue<<
//No modo sem estilo, os componentes não incluem nenhum CSS, então você precisa estilizar os componentes do seu lado, isso é especialmente útil ao criar sua própria biblioteca de UI no topo do PrimeVue. Visite o Modo sem estilo documentação para mais informações e exemplos.

// app.use(PrimeVue, {
//     unstyled: true
// });

app.component('create-instance', CreateInstance);
app.component('send-message-api', SendMessageApi);
app.component('select-instance', SelectInstance);
app.component('multiselect', MultiSelect);
app.component('test-api', TestApi);

app.mount('#app');
