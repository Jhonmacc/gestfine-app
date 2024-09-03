<!-- resources/js/components/PontoForm.vue -->
<template>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
      <h2 class="text-2xl font-bold mb-4">Controle de Folha de Ponto</h2>
      <form @submit.prevent="submitForm" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="diaSemana" class="block text-sm font-medium text-gray-700">DIA DA SEMANA:</label>
            <input
              v-model="formData.diaSemana"
              id="diaSemana"
              type="date"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label for="data" class="block text-sm font-medium text-gray-700">DATA:</label>
            <input
              v-model="formData.data"
              id="data"
              type="date"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label for="entrada" class="block text-sm font-medium text-gray-700">JORNADA DIÁRIA ENTRADA:</label>
            <input
              v-model="formData.entrada"
              id="entrada"
              type="time"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label for="saida" class="block text-sm font-medium text-gray-700">SAÍDA:</label>
            <input
              v-model="formData.saida"
              id="saida"
              type="time"
              required
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="total" class="block text-sm font-medium text-gray-700">TOTAL:</label>
            <input
              v-model="formData.total"
              id="total"
              type="text"
              disabled
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm"
            />
          </div>
          <div>
            <label for="atraso" class="block text-sm font-medium text-gray-700">ATRASO:</label>
            <input
              v-model="formData.atraso"
              id="atraso"
              type="text"
              disabled
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm"
            />
          </div>
          <div>
            <label for="extras" class="block text-sm font-medium text-gray-700">EXTRAS:</label>
            <input
              v-model="formData.extras"
              id="extras"
              type="text"
              disabled
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm"
            />
          </div>
          <div>
            <label for="horasExtrasNoturnas" class="block text-sm font-medium text-gray-700">HORAS EXTRAS NOTURNAS:</label>
            <input
              v-model="formData.horasExtrasNoturnas"
              id="horasExtrasNoturnas"
              type="text"
              disabled
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm"
            />
          </div>
          <div>
            <label for="adicionalNoturno" class="block text-sm font-medium text-gray-700">ADICIONAL NOTURNO:</label>
            <input
              v-model="formData.adicionalNoturno"
              id="adicionalNoturno"
              type="text"
              disabled
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm bg-gray-100 sm:text-sm"
            />
          </div>
        </div>
        <div>
          <label for="obs" class="block text-sm font-medium text-gray-700">OBS:</label>
          <textarea
            v-model="formData.obs"
            id="obs"
            rows="4"
            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          ></textarea>
        </div>
        <div>
          <button
            type="submit"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Enviar Form
          </button>
        </div>
      </form>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        formData: {
          diaSemana: '',
          data: '',
          entrada: '',
          saida: '',
          total: '',
          atraso: '',
          extras: '',
          horasExtrasNoturnas: '',
          adicionalNoturno: '',
          obs: '',
        },
      };
    },
    methods: {
      async submitForm() {
        // Calcular totais e valores antes de enviar
        this.calculateValues();
        // Enviar dados para o backend
        try {
          await axios.post('/api/controle-ponto', this.formData);
          alert('Formulário enviado com sucesso!');
        } catch (error) {
          console.error('Erro ao enviar formulário:', error);
          alert('Erro ao enviar formulário.');
        }
      },
      calculateValues() {
        // Implementar a lógica de cálculo de totais e valores aqui
        // Exemplo básico:
        let entrada = new Date(`1970-01-01T${this.formData.entrada}`);
        let saida = new Date(`1970-01-01T${this.formData.saida}`);
        let totalHoras = (saida - entrada) / (1000 * 60 * 60);
        this.formData.total = totalHoras.toFixed(2);

        // Calcular atraso, extras, horasExtrasNoturnas e adicionalNoturno
        // Implemente a lógica de cálculo conforme a necessidade
      },
    },
  };
  </script>
