<template>
    <div class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="max-w-7xl mx-auto p-4">
        <h1 class=" text-white text-2xl font-bold mb-4" >Adicionar Parâmetro E-mail</h1>
        <form @submit.prevent="saveParametros" class="bg-gray-700 shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <label class="block text-white text-sm font-bold mb-2" for="titulo">
                    Título do Email
                </label>
                <input v-model="parametro.titulo" id="titulo" type="text" placeholder="Título do Email" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
            </div>
            <div class="mb-6">
                <label class="block text-white text-sm font-bold mb-2" for="mensagem">
                    Texto do Email
                </label>
                <textarea v-model="parametro.mensagem" id="mensagem" placeholder="Texto do Email" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-24"></textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-green-700 hover:bg-green-500 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Salvar
                </button>
            </div>
        </form>
        <div v-if="message" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ message }}
        </div>

        <table class="min-w-full bg-gray-700 text-white">
            <thead>
                <tr class="text-white">
                    <!-- <th class="py-2 px-4 border-b text-left text-sm font-medium text-gray-600">E-mail</th> -->
                    <th class="py-2 px-4 border-b text-left text-sm font-medium">Título</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-medium">Mensagem</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-medium">Status</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-medium">Ações</th>
                </tr>
            </thead>
            <tbody class="text-white">
                <tr v-for="param in parametros" :key="param.id">
                    <!-- <td class="py-2 px-4 border-b">{{ param.email }}</td> -->
                    <td class="py-2 px-4 border-b">{{ param.titulo }}</td>
                    <td class="py-2 px-4 border-b">{{ param.mensagem }}</td>
                    <td class="py-2 px-4 border-b">
                        <div class="flex justify-center form-checkbox ">
                            <ToggleSwitch v-model="param.status" @change="toggleStatus(param)" />
                        </div>
                    </td>
                    <td class="py-2 px-4 border-b">
                        <button @click="edit(param.id)"
                            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline">
                            Editar
                        </button>
                        <button @click="delete(param.id)"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline ml-2">
                            Excluir
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</template>

<script>
import ToggleSwitch from 'primevue/toggleswitch';

export default {
    components: {
        ToggleSwitch
    },
    data() {
        return {
            parametros: [],
            parametro: {
                titulo: '',
                mensagem: ''
            },
            message: '',
        };
    },
    mounted() {
        this.fetchParametros();
    },
    methods: {
        fetchParametros() {
            axios.get('/envia-email-parametro')
                .then(response => {
                    // Mapear os parâmetros, assegurando que status seja booleano (true/false)
                    this.parametros = response.data.map(param => ({
                        ...param,
                        status: !!param.status  // Convertendo para booleano
                    }));
                });
        },
        saveParametros() {
            axios.post('/envia-email-parametro', this.parametro)
                .then(response => {
                    this.message = response.data.message;
                    this.fetchParametros();
                });
        },
        edit(id) {
            const parametro = this.parametros.find(p => p.id === id);
            this.parametro = { ...parametro };
        },
        delete(id) {
            axios.delete(`/envia-email-parametro/${id}`)
                .then(response => {
                    this.message = response.data.message;
                    this.fetchParametros();
                });
        },
        toggleStatus(param) {
            axios.post(`/envia-email-parametro/toggle/${param.id}`)
                .then(response => {
                    this.message = response.data.message;
                    param.status = response.data.status;  // Atualizar o status com o valor do backend
                })
                .catch(() => {
                    this.message = 'Erro ao atualizar o status';
                });
        }
    }
};
</script>
