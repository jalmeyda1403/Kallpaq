<template>
    <div class="chatbot-container">
        <!-- Floating Button -->
        <button class="chatbot-toggle btn btn-danger rounded-circle shadow-lg" @click="toggleChat" v-if="!isOpen">
            <img :src="'/images/icono_chatbot.png'" alt="Chatbot"
                style="width: 45px; height: 45px; object-fit: contain;">
        </button>

        <!-- Chat Window -->
        <div class="chatbot-window card shadow-lg" v-if="isOpen">
            <div
                class="card-header bg-danger text-white d-flex justify-content-between align-items-center position-relative">
                <div class="d-flex align-items-center">
                    <img :src="'/images/icono_chatbot.png'" alt="Chatbot" class="mr-2"
                        style="width: 30px; height: 30px; object-fit: contain;">
                    <h5 class="mb-0 font-weight-bold">Jaris</h5>
                </div>
                <button class="btn btn-sm btn-link text-white p-0" @click="toggleChat"
                    style="position: absolute; right: 15px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="card-body chat-history p-3" ref="chatHistory">
                <div v-for="(msg, index) in messages" :key="index"
                    :class="['message mb-3 d-flex', msg.role === 'user' ? 'justify-content-end' : 'justify-content-start']">
                    <div :class="['p-3 shadow-sm', msg.role === 'user' ? 'text-white' : 'bg-white text-dark border']"
                        :style="{
                            backgroundColor: msg.role === 'user' ? '#2c3e50' : '#ffffff',
                            maxWidth: '85%', 
                            wordWrap: 'break-word', 
                            borderRadius: '15px',
                            borderBottomRightRadius: msg.role === 'user' ? '0' : '15px',
                            borderBottomLeftRadius: msg.role === 'assistant' ? '0' : '15px'
                        }">
                        <div v-html="formatMessage(msg.content)"></div>
                    </div>
                </div>
                <div v-if="isTyping" class="text-left mb-3">
                    <div class="d-inline-block p-3 bg-white text-dark border shadow-sm" style="border-radius: 15px; border-bottom-left-radius: 0;">
                        <i class="fas fa-circle-notch fa-spin text-danger"></i> <span class="text-muted ml-2">Escribiendo...</span>
                    </div>
                </div>
            </div>

            <div class="card-footer p-2 bg-white border-top">
                <form @submit.prevent="sendMessage" class="input-group">
                    <input type="text" class="form-control border-0 bg-light" placeholder="Escribe tu consulta..." v-model="newMessage"
                        :disabled="isTyping" style="border-radius: 20px 0 0 20px; padding-left: 15px;">
                    <div class="input-group-append">
                        <button class="btn btn-danger" type="submit" :disabled="!newMessage.trim() || isTyping" style="border-radius: 0 20px 20px 0;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick, onMounted, watch } from 'vue';
import axios from 'axios';

const isOpen = ref(false);
const messages = ref([
    { role: 'assistant', content: 'Hola, soy **Jaris**. ¿En qué puedo ayudarte hoy? Puedo guiarte por el sistema o responder tus dudas.' }
]);
const newMessage = ref('');
const isTyping = ref(false);
const chatHistory = ref(null);

const toggleChat = () => {
    isOpen.value = !isOpen.value;
    if (isOpen.value) {
        scrollToBottom();
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (chatHistory.value) {
        chatHistory.value.scrollTop = chatHistory.value.scrollHeight;
    }
};

const formatMessage = (text) => {
    // Simple markdown formatter for bold and newlines
    let formatted = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Bold
        .replace(/\n/g, '<br>'); // Newlines

    // Detect links (simple regex)
    formatted = formatted.replace(
        /(https?:\/\/[^\s]+)/g,
        '<a href="$1" target="_blank" class="text-primary font-weight-bold" style="text-decoration: underline;">$1</a>'
    );

    return formatted;
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;

    const userMsg = newMessage.value;
    messages.value.push({ role: 'user', content: userMsg });
    newMessage.value = '';
    scrollToBottom();
    isTyping.value = true;

    try {
        const response = await axios.post('/chatbot/chat', { message: userMsg });
        const data = response.data;

        messages.value.push({ role: 'assistant', content: data.message });

        // Handle actions
        if (data.action) {
            handleAction(data.action);
        }

    } catch (error) {
        console.error('Chatbot error:', error);
        messages.value.push({ role: 'assistant', content: 'Lo siento, hubo un error al procesar tu solicitud.' });
    } finally {
        isTyping.value = false;
        scrollToBottom();
    }
};

const handleAction = (action) => {
    console.log('Handling action:', action);
    if (action.type === 'navigate') {
        let url = action.url;
        if (action.parameters) {
            const params = new URLSearchParams(action.parameters);
            url += `?${params.toString()}`;
        }
        window.location.href = url;
    } else if (action.type === 'event') {
        // Dispatch custom event
        window.dispatchEvent(new CustomEvent(action.name, { detail: action.detail }));
    }
};

watch(messages, () => {
    scrollToBottom();
}, { deep: true });

</script>

<style scoped>
.chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
}

.chatbot-window {
    width: 350px;
    height: 500px;
    display: flex;
    flex-direction: column;
    border-radius: 15px;
    overflow: hidden;
    border: none;
}

.chat-history {
    flex: 1;
    overflow-y: auto;
    background-color: #f4f6f9; /* Lighter background for better contrast */
}

.message {
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Scrollbar styling */
.chat-history::-webkit-scrollbar {
    width: 6px;
}

.chat-history::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-history::-webkit-scrollbar-thumb {
    background: #adb5bd;
    border-radius: 3px;
}
</style>
