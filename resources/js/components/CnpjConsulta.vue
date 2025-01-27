<template>
    <div class="max-w-2xl mx-auto my-8 p-4 bg-white rounded shadow">
      <h2 class="text-xl font-semibold mb-4">Consulta de CNPJ (ReceitaWS)</h2>

      <form @submit.prevent="consultarCNPJ" class="space-y-4">
        <div>
          <label for="cnpj" class="block text-sm font-medium text-gray-700">Digite o CNPJ:</label>
          <input
            v-model="cnpj"
            id="cnpj"
            type="text"
            placeholder="Digite o CNPJ"
            class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
          />
        </div>

        <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700">
          Consultar
        </button>
      </form>

      <!-- Mensagem de erro -->
      <div v-if="errorMessage" class="text-red-600 mt-4">
        {{ errorMessage }}
      </div>

      <!-- Exibir dados da empresa -->
      <div v-if="empresa" class="mt-8">
        <h3 class="text-lg font-semibold">Dados da Empresa</h3>

        <ul class="list-disc ml-4">
          <li v-if="empresa.status"><strong>Status:</strong> {{ empresa.status }}</li>
          <li v-if="empresa.ultima_atualizacao"><strong>Última Atualização:</strong> {{ formatDate(empresa.ultima_atualizacao) }}</li>
          <li v-if="empresa.cnpj"><strong>CNPJ:</strong> {{ empresa.cnpj }}</li>
          <li v-if="empresa.tipo"><strong>Tipo:</strong> {{ empresa.tipo }}</li>
          <li v-if="empresa.porte"><strong>Porte:</strong> {{ empresa.porte }}</li>
          <li v-if="empresa.nome"><strong>Nome:</strong> {{ empresa.nome }}</li>
          <li v-if="empresa.fantasia"><strong>Nome Fantasia:</strong> {{ empresa.fantasia }}</li>
          <li v-if="empresa.abertura"><strong>Abertura:</strong> {{ formatDate(empresa.abertura) }}</li>

          <!-- Atividades Principais -->
          <li v-if="empresa.atividade_principal">
            <strong>Atividade Principal:</strong>
            <ul class="ml-4 list-disc">
              <li v-for="(atividade, index) in empresa.atividade_principal" :key="index">
                {{ atividade.text }} (Código: {{ atividade.code }})
              </li>
            </ul>
          </li>

          <!-- Atividades Secundárias -->
          <li v-if="empresa.atividades_secundarias">
            <strong>Atividades Secundárias:</strong>
            <ul class="ml-4 list-disc">
              <li v-for="(atividade, index) in empresa.atividades_secundarias" :key="index">
                {{ atividade.text }} (Código: {{ atividade.code }})
              </li>
            </ul>
          </li>

          <!-- Endereço -->
          <li v-if="empresa.logradouro || empresa.numero || empresa.bairro">
            <strong>Endereço:</strong>
            {{ empresa.logradouro }},
            {{ empresa.numero }},
            {{ empresa.bairro }}
          </li>
          <li v-if="empresa.cep"><strong>CEP:</strong> {{ empresa.cep }}</li>
          <li v-if="empresa.municipio"><strong>Município:</strong> {{ empresa.municipio }}</li>
          <li v-if="empresa.uf"><strong>Estado:</strong> {{ empresa.uf }}</li>

          <!-- Outras informações -->
          <li v-if="empresa.email"><strong>Email:</strong> {{ empresa.email }}</li>
          <li v-if="empresa.telefone"><strong>Telefone:</strong> {{ empresa.telefone }}</li>
          <li v-if="empresa.efr"><strong>Ente Federativo Responsável:</strong> {{ empresa.efr }}</li>
          <li v-if="empresa.situacao"><strong>Situação:</strong> {{ empresa.situacao }}</li>
          <li v-if="empresa.data_situacao"><strong>Data da Situação:</strong> {{ formatDate(empresa.data_situacao) }}</li>
          <li v-if="empresa.capital_social"><strong>Capital Social:</strong> R$ {{ empresa.capital_social }}</li>

          <!-- Quadro Societário -->
          <li v-if="empresa.qsa">
            <strong>Quadro Societário:</strong>
            <ul class="ml-4 list-disc">
              <li v-for="(socio, index) in empresa.qsa" :key="index">
                Nome: {{ socio.nome }},
                Qualificação: {{ socio.qual }},
                País de Origem: {{ socio.pais_origem }},
                Nome do Representante Legal: {{ socio.nome_rep_legal }},
                Qualificação do Representante Legal: {{ socio.qual_rep_legal }}
              </li>
            </ul>
          </li>

          <!-- Simples Nacional -->
          <li v-if="empresa.simples">
            <strong>Simples Nacional:</strong>
            Optante: {{ empresa.simples.optante ? 'Sim' : 'Não' }},
            Data de Opção: {{ formatDate(empresa.simples.data_opcao) }},
            Data de Exclusão: {{ formatDate(empresa.simples.data_exclusao) }},
            Última Atualização: {{ formatDate(empresa.simples.ultima_atualizacao) }}
          </li>

          <!-- MEI -->
          <li v-if="empresa.simei">
            <strong>MEI:</strong>
            Optante: {{ empresa.simei.optante ? 'Sim' : 'Não' }},
            Data de Opção: {{ formatDate(empresa.simei.data_opcao) }},
            Data de Exclusão: {{ formatDate(empresa.simei.data_exclusao) }},
            Última Atualização: {{ formatDate(empresa.simei.ultima_atualizacao) }}
          </li>

          <!-- Informações sobre o uso de limites da consulta -->
          <li v-if="empresa.billing">
            <strong>Billing:</strong>
            Gratuita: {{ empresa.billing.free ? 'Sim' : 'Não' }},
            Banco de Dados: {{ empresa.billing.database ? 'Sim' : 'Não' }}
          </li>
        </ul>
      </div>
    </div>
  </template>

  <script>
  import axios from 'axios';

  export default {
    data() {
      return {
        cnpj: '',
        empresa: null,
        errorMessage: '',
      };
    },
    methods: {
      async consultarCNPJ() {
        try {
          const response = await axios.post('/consultar-receita', { cnpj: this.cnpj });
          this.empresa = response.data;
          this.errorMessage = ''; // Limpa mensagem de erro
        } catch (error) {
          console.error('Erro ao consultar o CNPJ:', error);
          this.errorMessage = error.response?.data?.error || 'Erro desconhecido';
          this.empresa = null; // Limpa dados da empresa em caso de erro
        }
      },
      formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('pt-BR');
      },
    },
  };
  </script>

  <style scoped>
  /* Adicione estilos personalizados aqui, se necessário */
  </style>
