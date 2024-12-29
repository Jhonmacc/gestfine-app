<template>
    <select
      v-model="selectedInstance"
      @change="emitSelectedInstance"
      class="w-full p-2 border border-gray-300 text-black rounded-md"
      required
    >
      <!-- Placeholder como a primeira opção -->
      <option value="" disabled>Selecione</option>
      <option v-for="instance in openInstances" :key="instance.instanceId" :value="instance.instanceName">
        {{ instance.instanceName }}
      </option>
    </select>
  </template>

  <script>
  export default {
    props: {
      initialValue: {
        type: String,
        default: ''
      },
      paramId: {
        type: Number,
        required: true
      }
    },
    data() {
      return {
        selectedInstance: this.initialValue,
        instances: [],
      };
    },
    computed: {
      openInstances() {
        return this.instances.filter(instance => instance.status === 'open');
      },
    },
    methods: {
      async fetchInstances() {
        try {
          const response = await axios.get('/instance/fetchInstances');
          this.instances = response.data.data.map(item => item.instance);
        } catch (error) {
          this.instances = [];
          console.error('Erro ao listar instâncias:', error);
        }
      },
      emitSelectedInstance() {
        this.$emit('instance-selected', { paramId: this.paramId, instance: this.selectedInstance });
      }
    },
    created() {
      this.fetchInstances();
    }
  };
  </script>
