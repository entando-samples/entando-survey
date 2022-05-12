<template>
    <div>
        <table class="table-auto w-full text-sm">
            <thead class="uppercase text-white bg-ternary">
                <tr class="font-semibold text-left">
                    <td class="p-2">Data Invio</td>
                    <td class="p-2">stato</td>
                    <td class="p-2">testo della comunicazione</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <tr v-if="loading">
                    <td colspan="4">
                        <div class="text-gray-600 text-center py-3">Loading messages....</div>
                    </td>
                </tr>
                <tr v-else-if="!(messages || []).length">
                    <td colspan="7">
                        <div class="text-gray-600 text-center py-3">No messages available</div>
                    </td>
                </tr>
                <template v-else>
                    <tr
                        v-for="(message, index) in messages"
                        :key="message.id"
                        :class="index % 2 === 0 ? 'bg-gray-100' : ''"
                        :data-row="message.id"
                    >
                        <td class="px-2 py-3">
                            <div>{{ message.created_at | date }}</div>
                        </td>

                        <td class="px-2 py-3 text-white whitespace-nowrap">
                            <span
                                class="px-2 text-xs bg-green-500 rounded-md"
                                v-if="message.read_at"
                                style="padding-top: 2px; padding-bottom: 2px;"
                            >Letto</span>
                            <span
                                class="px-2 text-xs bg-red-500 rounded-md"
                                style="padding-top: 2px; padding-bottom: 2px;"
                                v-else
                            >Non Letto</span>
                        </td>

                        <td class="px-2 py-3">
                            <div class="text-sm">{{ message.body | strlimit(100) }}</div>
                        </td>

                        <td class="px-2 py-3">
                            <router-link
                                :to="{ name: 'messages.show', params: { id: message.id } }"
                                class="inline-block bg-primary rounded-sm p-1 text-center"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                    />
                                </svg>
                            </router-link>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import usePatients from "@/compositions/patients";
import { defineComponent } from "@vue/composition-api";
import Vue from "vue";

export default defineComponent({
    name: "Messages",
    props: ['userId'],
    setup(props) {
        const { loadingMessages: loading, messages, getMessages } = usePatients();

        fetchMessages();

        function fetchMessages() {
            getMessages(props.userId)
                .catch(err => {
                    Vue.toasted.error(err.response?.data?.message || err.message);
                })
        }

        return {
            loading,
            messages,
        }
    }
})
</script>
