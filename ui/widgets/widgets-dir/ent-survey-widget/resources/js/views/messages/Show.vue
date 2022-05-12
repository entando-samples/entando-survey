<template>
    <div class="mt-3 mx-10">
        <b-page-header title="Message Detail" />

        <div class="bg-white">
            <div class="p-5 py-3 border-b flex items-center justify-between">
                <div class="flex items-center">
                    <div class="cursor-pointer" @click="goBack">
                        <svg
                            class="w-6 h-6 text-primary"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                    </div>
                    <div class="ml-4" v-if="message.require_call && !message.from_bo && !loading">
                        <b-button
                            class="py-1"
                            :disabled="message.called_at"
                            @click="onMarkAsCalledClick"
                        >Segna come contattato</b-button>
                    </div>
                </div>
                <div
                    class="text-gray-500"
                    v-if="!loading"
                >Ricevuto il: {{ message.created_at | datetime }}</div>
            </div>
            <div class="p-5">
                <div class="ml-10">
                    <template v-if="!loading">
                        <div>
                            <h2
                                class="font-semibold text-lg"
                                v-if="message.sender"
                            >{{ message.sender.name }}</h2>
                            <h2 class="font-semibold text-lg text-red-500" v-else>User unavailable</h2>
                            <p class="mt-2">
                                <span>Tipologia:</span>
                                <span class="ml-3 font-semibold">{{ message.topic ? message.topic.title : '' }}</span>
                            </p>
                            <p class="mt-4">{{ message.body }}</p>

                            <div
                                class="mt-4"
                                v-if="(message.attachments || []).length || message.voice_message"
                            >
                                <div v-if="(message.attachments || []).length">
                                    <p class="font-semibold">Attachments:</p>
                                    <ul class="list-disc">
                                        <li
                                            class="flex items-center space-x-3"
                                            v-for="(attachment, idx) in message.attachments"
                                            :key="idx"
                                        >
                                            <a
                                                class="text-primary"
                                                :href="attachment"
                                                target="_blank"
                                            >{{ attachment }}</a>
                                            <a :href="attachment" download>
                                                <svg
                                                    class="w-6 h-6 text-primary"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="mt-4" v-if="message.voice_message">
                                        <p class="font-semibold flex items-center space-x-5">
                                            <span>Voice Message:</span>
                                            <a :href="message.voice_message" download>
                                                <svg
                                                    class="w-6 h-6 text-primary"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </a>
                                        </p>
                                        <audio class="mt-2" controls>
                                            <source :src="message.voice_message" type="audio/mpeg" />Your browser does not support the audio tag.
                                        </audio>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="message.reply && !message.from_bo" class="mt-5 bg-gray-100 rounded-md px-4 py-5">
                            <div class="flex items-center justify-between">
                                <h2
                                    class="font-semibold text-lg"
                                    v-if="message.reply.author"
                                >{{ message.reply.author.name }}</h2>
                                <h2
                                    class="font-semibold text-lg text-red-500"
                                    v-else
                                >User unavailable</h2>

                                <p>Risposto: {{ message.reply.created_at | datetime }}</p>
                            </div>
                            <p class="mt-4">{{ message.reply.body }}</p>
                        </div>
                    </template>
                    <p v-else class="text-gray-500">Loading...</p>
                </div>
            </div>
        </div>

        <div class="mt-5 flex flex-row-reverse" v-if="!message.from_bo">
            <div class="flex items-center space-x-5">
                <b-button
                    variant="primary-alt"
                    :disabled="message.is_archived || message.require_call || loading"
                    @click="onMarkAsRequireCallClick"
                >Da contattare telefonicamente</b-button>
                <b-button @click="modals.reply = true" :disabled="message.reply || loading">Rispondi</b-button>
            </div>
        </div>

        <b-modal title="Rispondi al messaggio" v-if="modals.reply" @close="modals.reply = false">
            <textarea
                class="p-3 w-full text-sm resize-none border rounded-sm"
                placeholder="write your message here..."
                rows="10"
                v-model="reply.body"
            ></textarea>
            <small class="block text-red-600" v-if="errors.body">{{ errors.body }}</small>

            <template #footer>
                <div class="-mt-8 flex flex-row-reverse">
                    <div class="flex items-center space-x-5">
                        <b-button variant="primary-alt" @click="modals.reply = false">Indietro</b-button>
                        <b-button @click="sendMessageReply" :disabled="loading">Invia</b-button>
                    </div>
                </div>
            </template>
        </b-modal>
    </div>
</template>

<script>
import useMessages from "@/compositions/messages";
import router from "@/router";
import { defineComponent, reactive } from "@vue/composition-api";
import Vue from "vue";

export default defineComponent({
    name: "MessageShowPage",
    setup(_, { root }) {
        const { loading, errors, message, getMessage, sendReply, markAsRequireCall, markAsCalled } = useMessages();

        const modals = reactive({
            reply: false,
        })

        const reply = reactive({
            body: null
        });


        fetchMessage();

        function fetchMessage() {
            return getMessage(root.$route.params.id, true)
                .catch(err => {
                    if (err.response?.status === 404) {
                        router.back();
                    }

                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        function onMarkAsRequireCallClick() {
            markAsRequireCall(root.$route.params.id)
                .then(() => {
                    fetchMessage();
                    Vue.toasted.success("Message status is set to 'require call' successfully")
                })
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        function onMarkAsCalledClick() {
            markAsCalled(root.$route.params.id)
                .then(() => {
                    fetchMessage();
                    Vue.toasted.success("Message status is set to 'called' successfully")
                })
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
        }

        return {
            errors,
            modals,
            loading,
            message,
            reply,
            onMarkAsRequireCallClick,
            onMarkAsCalledClick,
            goBack: () => {
                router.replace({
                    name: localStorage.getItem('last-active-message-list') || 'messages.index'
                });
            },
            sendMessageReply: () => {
                sendReply({ id: root.$route.params.id, data: { body: reply.body } })
                    .then(res => {
                        fetchMessage();

                        modals.reply = false;

                        Vue.toasted.success("Reply sent successfully");
                    })
                    .catch(err => {
                        Vue.toasted.error(err?.response?.data?.message || err.message)
                    })
            }
        }
    }
})
</script>
