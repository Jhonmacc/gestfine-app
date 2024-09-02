<template>
    <div class="w-1/2 mx-auto p-8 bg-neutral-900 text-white shadow-md rounded-full">
        <Tag
            class="text-4xl font-semibold mb-4">
            <h4 class="text-white font-bold"> Enviar Mensagem via WhatsApp</h4>
        </Tag>
        <form @submit.prevent="sendMessage">
            <div class="mb-4">
                <Tag for="instanceopen" class="card block text-sm font-medium mb-1">Instância</Tag>
                <select
                    v-model="selectedInstance"
                    id="instanceopen"
                    class="w-full p-2 border border-gray-300 text-black rounded-md"
                    required
                >
                    <option value="" disabled>Selecione</option>
                    <option v-for="instance in openInstances" :key="instance.instanceId" :value="instance.instanceName">
                        {{ instance.instanceName }}
                    </option>
                </select>
            </div>
            <div class="mb-4">
                <Tag for="numbers" class="card block text-sm font-medium mb-1">Números</Tag>
                <MultiSelect
                    v-model="selectedNumbers"
                    display="chip"
                    :options="numbers"
                    optionLabel="name"
                    filter
                    placeholder="Selecione os números..."
                    :maxSelectedLabels="3"
                    class="w-full md:w-80"
                />
            </div>
            <div class="mb-4">
                <Tag for="message" class="card block text-sm font-medium mb-1">Texto</Tag>
                <textarea
                    v-model="textMessage"
                    id="message"
                    rows="8"
                    placeholder=
                    "Informe o texto da mensagem que deseja enviar, podendo usar os mesmos recursos que você usaria no aplicativo ou na web, que são:
- Emojis
- Negrito, entre *seutexto*
- Itálico, entre _seutexto_
- Riscado, entre ~seutexto~
- Monoespaçado, entre ```seutexto```
Para quebrar uma linha, insira uma quebra de linha \n na mensagem."
                    class="w-full p-2 border border-gray-300 text-black rounded-md"
                    required
                ></textarea>
            </div>
            <button
                type="submit"
                class="px-4 py-2 font-bold bg-green-600 text-white rounded-md hover:bg-green-700"
            >
                Enviar Mensagem
            </button>
            <div v-if="progress" class="mt-4">
                <p :class="progress.percentage === 100 ? 'text-green-500' : 'text-red-500'">{{ progress.message }}</p>
                <ProgressBar :value="progress.percentage" class="mt-2" />
                <Message severity="error" v-if="progress.failureNumbers.length > 0">
                    Esses são os números que falharam no recebimento da mensagem
                    ({{ progress.failureNumbers.join(', ') }}).
                </Message>
                    <Message class="mt-4" v-if="progress.failureNumbers.length > 0" severity="success">
                        O restante dos números receberam a mensagem com sucesso! 😁
                    </Message>
            </div>
            <div v-if="errorMessage" class="mt-4 text-red-500">{{ errorMessage }}</div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import MultiSelect from 'primevue/multiselect';
import Tag from 'primevue/tag';
import ProgressBar from 'primevue/progressbar';
import Message from 'primevue/message';
import axios from 'axios';

const textMessage = ref('');
const response = ref(null);
const errorMessage = ref(null);
const instances = ref([]);
const selectedInstance = ref('');
const selectedNumbers = ref([]);
const numbers = ref([]);
const progress = ref({ percentage: 0, message: '', failureNumbers: [] });

const openInstances = computed(() => {
    return instances.value.filter(instance => instance.status === 'open');
});

const fetchNumbers = async () => {
    try {
        const res = await axios.get('/numbers');
        numbers.value = res.data;
    } catch (error) {
        errorMessage.value = error.response ? error.response.data.message : 'Erro ao listar os números.';
    }
};

const sendMessage = async () => {
    try {
        progress.value = { message: 'Enviando mensagens...', percentage: 0, failureNumbers: [] };
        const total = selectedNumbers.value.length;
        let successCount = 0;

        for (let i = 0; i < total; i++) {
            const number = selectedNumbers.value[i].code;

            const payload = {
                instanceopen: selectedInstance.value,
                number: [number],
                options: {
                    delay: 1200,
                    presence: 'composing',
                    linkPreview: false
                },
                textMessage: {
                    text: textMessage.value
                }
            };

            const res = await axios.post('/send-message', payload, {
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            if (res.data.status === 'success') {
                successCount++;
            } else {
                progress.value.failureNumbers.push(number);
            }

            progress.value.percentage = (successCount / total) * 100;

            if (i < total - 1) {
                await new Promise(resolve => setTimeout(resolve, 30000)); // Aguardar 60 segundos
            }
        }

        if (progress.value.failureNumbers.length > 0) {
            progress.value.message = 'Algumas mensagens falharam ao serem enviadas.';
        } else {
            progress.value.message = 'Todas as mensagens foram enviadas com sucesso!';
        }

    } catch (error) {
        errorMessage.value = error.response ? error.response.data.message : error.message;
    }
};
const fetchInstances = async () => {
    try {
        const res = await axios.get('/instance/fetchInstances');
        instances.value = res.data.data.map(item => item.instance);
    } catch (error) {
        instances.value = [];
        errorMessage.value = error.response ? error.response.data.message : 'Erro ao listar as instâncias.';
    }
};

fetchInstances();
fetchNumbers();
</script>

<style scoped>
/* Adicione seu estilo aqui, se necessário */
</style>
