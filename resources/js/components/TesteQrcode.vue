<template>
    <div>
      <img :src="qrCodeUrl" alt="QR Code" />
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        qrCodeBase64: '', // Armazenar o código Base64 recebido
      };
    },
    computed: {
      qrCodeUrl() {
        return this.qrCodeBase64;
      },
    },
    methods: {
      fetchQRCode() {
        // Faça a requisição para obter o QR Code
        axios.get('http://localhost:8002/instance/connect/Teste', {
          headers: {
            apikey: 'J6P756FCDA4D4FD5936555990E718741',
          },
        })
        .then(response => {
          this.qrCodeBase64 = response.data.base64;
        })
        .catch(error => {
          console.error('Erro ao buscar o QR Code:', error);
        });
      },
    },
    mounted() {
      this.fetchQRCode();
    },
  };
  </script>
