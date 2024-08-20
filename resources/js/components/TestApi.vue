<template>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
      <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Teste de API</h1>

      <button
        @click="fetchInstances"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        Buscar Instâncias
      </button>

      <div class="mt-6" v-if="instances.length">
        <h2 class="text-xl font-semibold mb-2">Instâncias:</h2>
        <ul>
          <li v-for="instance in instances" :key="instance.instanceId" class="mb-4 p-4 border border-gray-200 rounded-md">
            <h3 class="text-lg font-semibold">{{ instance.instanceName }}</h3>
            <p><strong>ID:</strong> {{ instance.instanceId }}</p>
            <p><strong>Status:</strong> {{ instance.status }}</p>
            <p><strong>Server URL:</strong> {{ instance.serverUrl }}</p>
            <p><strong>API Key:</strong> {{ instance.apikey }}</p>
            <p><strong>Integration Token:</strong> {{ instance.integration.token }}</p>
            <p><strong>Webhook URL:</strong> {{ instance.integration.webhook_wa_business }}</p>
          </li>
        </ul>
      </div>

      <div v-if="errorMessage" class="mt-6 p-4 bg-red-100 text-red-800 border border-red-300 rounded-md">
        <strong>Erro:</strong> {{ errorMessage }}
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        instances: [],
        errorMessage: null,
      };
    },
    methods: {
      async fetchInstances() {
        try {
          const response = await axios.get('http://localhost:8002/instance/fetchInstances', {
            headers: {
              'apikey': 'J6P756FCDA4D4FD5936555990E718741'
            }
          });
          this.instances = response.data.map(item => item.instance);
          this.errorMessage = null;
        } catch (error) {
          this.instances = [];
          this.errorMessage = error.response ? error.response.data.message : 'Erro desconhecido.';
        }
      }
    }
  };
  </script>

  <style scoped>
  /* Adicione aqui os estilos específicos para este componente, se necessário */
  </style>
