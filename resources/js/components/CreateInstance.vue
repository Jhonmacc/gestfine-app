<template>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
      <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Criar Instância</h1>

      <form @submit.prevent="createInstance">
        <!-- Campos para criar uma nova instância -->
        <div class="mb-4">
          <label for="instanceName" class="block text-gray-700 font-semibold mb-2">Nome da Instância:</label>
          <input
            type="text"
            id="instanceName"
            v-model="instanceName"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <div class="mb-4">
          <label for="token" class="block text-gray-700 font-semibold mb-2">Chave de API:</label>
          <input
            type="text"
            id="token"
            v-model="token"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
        </div>
        <button
          type="submit"
          class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Criar Instância
        </button>
      </form>

      <!-- Campo de busca para filtrar instâncias -->
      <div class="mt-6">
        <label for="searchInstanceName" class="block text-gray-700 font-semibold mb-2">Buscar por Nome:</label>
        <input
          type="text"
          id="searchInstanceName"
          v-model="searchInstanceName"
          @input="fetchInstances"
          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        />
      </div>

      <div class="mt-10">
        <h1 class="text-2xl font-bold mb-4">Lista de Instâncias</h1>
        <ul v-if="instances.length">
          <li v-for="(instance, index) in instances" :key="index" class="mb-2 p-4 bg-gray-100 border border-gray-300 rounded-md">
            <p><strong>Nome:</strong> {{ instance.instanceName }}</p>
            <p><strong>Status:</strong> {{ instance.status }}</p>
          </li>
        </ul>
        <p v-else>Não há instâncias disponíveis.</p>
      </div>

      <!-- Exibição de resposta ou erro -->
      <div v-if="response" class="mt-6 p-4 bg-green-100 text-green-800 border border-green-300 rounded-md">
        <strong>Sucesso:</strong> Instância criada com sucesso!
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
        instanceName: '',
        token: '',
        searchInstanceName: '', // Armazena o valor para o filtro de busca
        response: null,
        instances: [],
        errorMessage: null
      };
    },
    methods: {
      async createInstance() {
        try {
          const formData = new FormData();
          formData.append('instanceName', this.instanceName);
          formData.append('token', this.token);

          const response = await axios.post(
            `${window.baseUrl}/create-instance`, // URL correta
            formData
          );

          this.response = response.data;
          this.fetchInstances(); // Atualize a lista de instâncias após a criação
          this.instanceName = ''; // Limpar campos após envio
          this.token = '';
        } catch (error) {
          this.errorMessage = error.response ? error.response.data.message : error.message;
          this.response = null;
        }
      },
      async fetchInstances() {
        try {
          const response = await axios.get('http://localhost:8002/instance/fetchInstances', {
            headers: {
              'apikey': 'J6P756FCDA4D4FD5936555990E718741'
            }
          });
          this.instances = response.data.map(item => item.instance)
            .filter(instance => instance.instanceName.toLowerCase().includes(this.searchInstanceName.toLowerCase()));
          this.errorMessage = null;
        } catch (error) {
          this.instances = [];
          this.errorMessage = error.response ? error.response.data.message : 'Erro desconhecido.';
        }
      }
    },
    mounted() {
      this.fetchInstances(); // Carregar a lista de instâncias quando o componente for montado
    },
  };
  </script>

  <style scoped>
  /* Adicione aqui os estilos específicos para este componente, se necessário */
  </style>
