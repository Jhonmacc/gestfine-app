<template>
    <div class="mx-auto p-6 bg-neutral-900 text-white shadow-md rounded-lg">
        <!-- Botão para abrir o modal -->
        <div class="flex justify-between items-center">
            <Tag class="text-3xl bg-gray-800 text-white">
            <h2 class="text-xl font-bold ">Lista de Instâncias</h2>
            </Tag>
            <button @click="openModal"
                class="mb-6 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                + INSTÂNCIA
            </button>
        </div>
        <!-- Modal para criar uma nova instância -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-neutral-900 text-white p-6 rounded-lg shadow-lg max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">
                    Nova Instância
                </h2>
                <form @submit.prevent="createInstance">
                    <div class="mb-4">
                        <label for="instanceName" class="block text-white font-semibold mb-2">
                            Nome da Instância:
                        </label>
                        <input type="text" id="instanceName" v-model="instanceName" required
                            class="w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                    </div>
                    <div class="mb-4 relative">
                        <label for="token" class="block text-white font-semibold mb-2">
                            Chave de API:
                        </label>
                        <span @click="generateApiKey" class="input-group-text cursor-pointer"><i
                                class="mdi mdi-lock-reset text-3xl mr-2"></i>
                            <input type="text" id="token" v-model="token" required
                                class="w-full px-3 py-2 border text-gray-700 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <!-- Ícone para gerar nova chave --></span>
                        <span class="absolute top-2   text-gray-500 hover:text-gray-700">
                        </span>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button @click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-md mr-2">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
       <!-- Modal para exibir o QR Code -->
        <div v-if="showQrCodeModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
            <div class="bg-neutral-900 text-white p-6 rounded-lg shadow-lg max-w-md w-full">
            <h2 class="text-xl font-semibold mb-4">Conectar via QR Code</h2>

            <div v-if="qrCodeUrl">
                <img :src="qrCodeUrl" alt="QR Code para conexão" class="mx-auto" />
            </div>

            <div v-else class="flex justify-center items-center">
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="transparent"
                                animationDuration=".5s" aria-label="Carregando QR Code..." />
                <p class="ml-2">Carregando QR Code...</p>
            </div>

            <div class="flex justify-end mt-6">
                <button @click="closeQrCodeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-4 rounded-md">
                Fechar
                </button>
            </div>
            </div>
        </div>
        <!-- Lista de Instâncias -->
        <div class="mt-1">
            <input type="text" v-model="searchInstanceName" @input="fetchInstances"
                placeholder="Buscar por Instâncias..."
                class="mb-4 w-full px-3 py-2 border border-gray-300 rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                <div v-if="instances.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4" style="width: 70rem;">
                <div v-for="(instance, index) in filteredInstances" :key="index"
                    class="p-4 bg-neutral-900 border-b-4 border-gray-700 border-t-4 border-r-4 border-l-4 shadow-sm rounded-2xl flex justify-between items-center cursor-pointer hover:bg-gray-700 "
                    @click="showQrCode(instance)">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-2 text-sm">
                        <Tag><strong>Nome Instância:</strong> {{ instance.instanceName }}</Tag>
                        <Tag><strong>Número:</strong> {{ formatPhoneNumber(instance.owner) }}</Tag>
                        <Tag>
                       <img :src="instance.profilePictureUrl" alt="Profile Picture" class="w-20 h-20 rounded-full object-cover">
                    </Tag>
                        <Tag class="p-0 m-0">
                            {{ translateStatus(instance.status) }}
                            <i :class="getStatusIconColor(instance.status)"
                            class="fas fa-circle m-0 p-0 text-xs mr-2"></i>
                        </Tag>
                        <Tag> {{ instance.profileName }} </Tag>
                        <div class="card flex justify-center">
                             <!-- Botão 'Ir para a Tela' -->
                <div class="card flex justify-center">
                    <Button label="Enviar Mensagens" :href="instance.url" class="p-button-primary" />
                </div>
                    </div>
                    </div>
                    <!-- Ações de instância -->
                    <div class="flex space-x-3">
                        <button @click.stop="deleteInstance(instance)"
                            class="relative flex items-center justify-center p-2 rounded-full border-2 border-red-100 cursor-pointer">
                            <span class="absolute inset-0 rounded-full bg-red-200"></span>
                            <i
                                class="fa-solid fa-trash lg:text-lg text-red-500 transition-transform duration-300 transform hover:scale-125"></i>
                        </button>
                    </div>
                </div>
            </div>
            <p v-else>Não há instâncias disponíveis.</p>
        </div>
        <!-- Mensagem de resposta ou erro -->
        <!-- <div v-if="response" class="mt-6 p-4 bg-green-100 text-green-800 border border-green-300 rounded-md">
            <strong>Sucesso:</strong> {{ response }}
        </div>
        <div v-if="errorMessage" class="mt-6 p-4 bg-red-100 text-red-800 border border-red-300 rounded-md">
            <strong>Erro:</strong> {{ errorMessage }}
        </div> -->
    </div>
     <!-- Barra Loading -->
     <div v-if="isLoading" class="items-center justify-center z-50 bg-black bg-opacity-50">
            <ProgressBar mode="indeterminate"  style="height: 6px"></ProgressBar>
        </div>
</template>

<script>
import axios from 'axios';
import ProgressBar from 'primevue/progressbar';
import ProgressSpinner from 'primevue/progressspinner';
import Tag from 'primevue/tag';
import Button from 'primevue/button';

export default {
    props: {
    url: {
      type: String,
      required: true
    }
  },
    components: {
    ProgressBar,
    ProgressSpinner,
    Tag
},
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
            isLoading: false,
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
        formatPhoneNumber(owner) {
        // Verifica se o valor existe e se é uma string
        if (!owner || typeof owner !== 'string') {
            return ''; // Retorna uma string vazia se não houver número
        }

        // Extrai apenas o número antes do '@'
        const rawNumber = owner.split('@')[0];

        // Aplica a máscara ao número
        const maskedNumber = rawNumber.replace(/(\d{2})(\d{2})(\d{4})(\d{4})/, '+$1 ($2)$3-$4');

        return maskedNumber;
    },
        async createInstance() {
            try {
                this.isLoading = true;
                const formData = new FormData();
                formData.append('instanceName', this.instanceName);
                formData.append('token', this.token);

                const response = await axios.post('/create-instance', formData);

                this.response = response.data.message;
                this.fetchInstances(); // Atualiza a lista de instâncias após a criação
                this.closeModal(); // Fecha o modal
            } catch (error) {
                this.errorMessage = error.response ? error.response.data.message : error.message;
            } finally {
                this.isLoading = false; // Oculta a barra de progresso
            }
        },
        async fetchInstances() {
            try {
                this.isLoading = true;
                const response = await axios.get('/instance/fetchInstances');
                this.instances = response.data.data.map(item => item.instance);
            } catch (error) {
                this.instances = [];
                this.errorMessage = error.response ? error.response.data.message : 'Erro ao listar as instâncias.';
            } finally {
                this.isLoading = false; // Oculta a barra de progresso
            }
        },
        async deleteInstance(instance) {
            try {
                this.isLoading = true; // Exibe a barra de progresso
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
            } finally {
                this.isLoading = false; // Oculta a barra de progresso
            }
        },
        async showQrCode(instance) {
            this.showQrCodeModal = true;
            this.qrCodeUrl = null; // Limpa o QR Code anterior

            try {
                const response = await axios.get(`http://localhost:8002/instance/connect/${instance.instanceName}`, {
                headers: {
                    apikey: 'J6P756FCDA4D4FD5936555990E718741'
                }
                });

                this.qrCodeBase64 = response.data.base64;
                this.qrCodeUrl = `data:image/png;base64,${this.qrCodeBase64}`;

                // Inicia o monitoramento do status de conexão
                this.monitorConnection(instance.instanceName);

            } catch (error) {
                console.error('Erro ao buscar o QR Code:', error);
            }
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
        return statusMap[status] || status;
    },
    getStatusIconColor(status) {
        const colorMap = {
            'close': 'text-red-500',        // Cor vermelha para "close"
            'connecting': 'text-yellow-500', // Cor laranja para "connecting"
            'open': 'text-green-500'         // Cor verde para "open"
        };
        return colorMap[status] || 'text-gray-500'; // Classe padrão se o status não for encontrado
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
