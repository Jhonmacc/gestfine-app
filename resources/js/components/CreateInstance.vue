<template>
    <div class="mx-auto p-6 bg-white shadow-md rounded-lg">


        <!-- Botão para abrir o modal -->
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold ">Lista de Instâncias</h2>
            <button @click="openModal"
                class="mb-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">
                + INSTÂNCIA
            </button>
        </div>


        <!-- Modal para criar uma nova instância -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">Nova Instância</h2>
                <form @submit.prevent="createInstance">
                    <div class="mb-4">
                        <label for="instanceName" class="block text-gray-700 font-semibold mb-2">Nome da
                            Instância:</label>
                        <input type="text" id="instanceName" v-model="instanceName" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                    <div class="mb-4 relative">
                        <label for="token" class="block text-gray-700 font-semibold mb-2">Chave de API:</label>
                        <input type="text" id="token" v-model="token" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                        <!-- Ícone para gerar nova chave -->
                        <span @click="generateApiKey"
                            class="absolute top-2 right-2 cursor-pointer text-gray-500 hover:text-gray-700">
                            🔄
                        </span>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button @click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-md mr-2">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>

         <!-- Modal para exibir o QR Code -->
    <div v-if="showQrCodeModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-xl font-semibold mb-4">Conectar via QR Code</h2>
            <div v-if="qrCodeUrl">
                <img :src="qrCodeUrl" alt="QR Code para conexão" class="mx-auto" />
            </div>
            <p v-else>Carregando QR Code...</p>
            <div class="flex justify-end mt-6">
                <button @click="closeQrCodeModal"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-md">
                    Fechar
                </button>
            </div>
        </div>
    </div>

        <!-- Lista de Instâncias -->
        <div class="mt-1">
            <input type="text" v-model="searchInstanceName" @input="fetchInstances"
                placeholder="Buscar por Instâncias..."
                class="mb-4 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
            <ul v-if="instances.length">
                <li v-for="(instance, index) in filteredInstances" :key="index"
                    class="mb-2 p-4 bg-gray-100 border border-gray-300 rounded-md flex justify-between items-center cursor-pointer hover:bg-gray-200"
                    @click="showQrCode(instance)">
                    <div>
                        <p><strong>Nome:</strong> {{ instance.instanceName }}</p>
                        <p><strong>Status:</strong> {{ translateStatus(instance.status) }}</p>
                    </div>
                    <!-- Ações de instância -->
                    <div class="flex space-x-3">
                        <button @click.stop="deleteInstance(instance)"
                            class="relative flex items-center justify-center p-2 rounded-full border-2 border-red-100 cursor-pointer">
                            <!-- Contêiner para o fundo vermelho -->
                            <span class="absolute inset-0 rounded-full bg-red-200"></span>

                            <!-- Ícone dentro do botão -->
                            <i
                                class="fa-solid fa-trash lg:text-lg text-red-500 transition-transform duration-300 transform hover:scale-125"></i>
                        </button>
                    </div>
                </li>
            </ul>
            <p v-else>Não há instâncias disponíveis.</p>
        </div>
        <!-- Mensagem de resposta ou erro -->
        <div v-if="response" class="mt-6 p-4 bg-green-100 text-green-800 border border-green-300 rounded-md">
            <strong>Sucesso:</strong> {{ response }}
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
            searchInstanceName: '',
            response: null,
            instances: [],
            errorMessage: null,
            showModal: false,
            showQrCodeModal: false,
            qrCodeBase64: '',
            monitoringConnection: null,
        };
    },
    computed: {
        filteredInstances() {
            return this.instances.filter(instance =>
                instance.instanceName.toLowerCase().includes(this.searchInstanceName.toLowerCase())
            );
        },
        qrCodeUrl() {
            return this.qrCodeBase64;
        },
    },
    methods: {
        openModal() {
            this.showModal = true;
            this.generateApiKey(); // Gera uma chave de API quando o modal é aberto
        },
        closeModal() {
            this.showModal = false;
            this.instanceName = ''; // Limpa os campos do modal ao fechar
            this.token = '';
        },
        generateApiKey() {
            this.token =
                Math.random().toString(36).substring(2, 15) +
                Math.random().toString(36).substring(2, 15);
        },
        async createInstance() {
            try {
                const formData = new FormData();
                formData.append('instanceName', this.instanceName);
                formData.append('token', this.token);

                const response = await axios.post('/create-instance', formData);

                this.response = response.data.message;
                this.fetchInstances(); // Atualiza a lista de instâncias após a criação
                this.closeModal(); // Fecha o modal
            } catch (error) {
                this.errorMessage = error.response ? error.response.data.message : error.message;
            }
        },
        async fetchInstances() {
            try {
                const response = await axios.get('/instance/fetchInstances');
                this.instances = response.data.data.map(item => item.instance);
            } catch (error) {
                this.instances = [];
                this.errorMessage = error.response ? error.response.data.message : 'Erro ao listar as instâncias.';
            }
        },
        async deleteInstance(instance) {
    try {
        // Faz a requisição de exclusão usando um caminho relativo
        const response = await axios.delete(`/instance/deleteAndLogout/${instance.instanceName}`);

        if (response.data.status === 'success') {
            this.response = response.data.message;
            this.fetchInstances(); // Atualiza a lista de instâncias
        } else {
            this.errorMessage = `Erro: ${response.data.message}`;
        }
    } catch (error) {
        if (error.response && error.response.status === 404) {
            this.errorMessage = `Erro: A instância com o nome "${instance.instanceName}" não foi encontrada.`;
        } else {
            this.errorMessage = error.response ? error.response.data.message : error.message;
        }
    }
},

        async showQrCode(instance) {
    this.showQrCodeModal = true;

    // console.log('Instância:', instance); // Log para debug dados da instancia

    // Busca o QR Code
    await axios.get(`http://localhost:8002/instance/connect/${instance.instanceName}`, {
        headers: {
            apikey: 'J6P756FCDA4D4FD5936555990E718741'
        }
    })
    .then(response => {
        this.qrCodeBase64 = response.data.base64;

        // Inicia o monitoramento do status de conexão
        this.monitorConnection(instance.instanceName);
    })
    .catch(error => {
        console.error('Erro ao buscar o QR Code:', error);
    });
},
async monitorConnection(instanceName) {
    this.monitoringConnection = setInterval(async () => {
        try {
            console.log('Verificando status para:', instanceName);
            const response = await axios.get(`http://localhost:8002/instance/connectionState/${instanceName}`, {
                headers: {
                    apikey: 'J6P756FCDA4D4FD5936555990E718741'
                }
            });
            const state = response.data.instance.state; // Ajuste aqui para acessar 'state'

            console.log('Status recebido:', state); // Verifique o status recebido

            if (state === 'open') {
                // Fecha o modal e exibe a mensagem de sucesso
                this.showQrCodeModal = false;
                this.response = "Dispositivo conectado com sucesso!";
                this.fetchInstances();
                // Para o monitoramento
                clearInterval(this.monitoringConnection);
            }
        } catch (error) {
            console.error('Erro ao verificar o status de conexão:', error);
        }
    }, 2000);
},


closeQrCodeModal() {
    this.showQrCodeModal = false;

    // Para o monitoramento se o modal for fechado manualmente
    if (this.monitoringConnection) {
        clearInterval(this.monitoringConnection);
        this.monitoringConnection = null;
    }

    this.fetchInstances();
},

        translateStatus(status) {
            const statusMap = {
                'close': 'Não Conectado',
                'connecting': 'Conectando',
                'open': 'Conectado',
            };
            return statusMap[status] || status; // Retorna o status traduzido ou o status original se não encontrado
        },
        mounted() {
            this.showQrCode();
        },
    },
    created() {
        this.fetchInstances();
    }
};
</script>


<style scoped></style>
