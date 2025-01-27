<template>
    <div class="max-w-4xl mx-auto mt-8">
      <h1 class="text-2xl font-bold mb-4">Logs de Monitoramento de Certificados</h1>
      <div v-if="logs.length">
        <div v-for="log in logs" :key="log.id" class="border-b border-gray-200 py-4">
          <div class="text-gray-600 text-sm">{{ formatDate(log.created_at) }}</div>
          <div class="text-lg">{{ log.message }}</div>
          <div v-if="log.email" class="text-blue-600">Email: {{ log.email }}</div>
          <div v-if="log.certificado" class="text-green-600">Certificado: {{ log.certificado }}</div>
        </div>
      </div>
      <div v-else class="text-center text-gray-500">
        Nenhum log encontrado.
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        logs: [],
      };
    },
    mounted() {
      this.fetchLogs();
    },
    methods: {
      fetchLogs() {
        fetch('/api/job-logs')
          .then(response => response.json())
          .then(data => {
            this.logs = data;
          });
      },
      formatDate(date) {
        return new Date(date).toLocaleString();
      },
    },
  };
  </script>

  <style>
  </style>
