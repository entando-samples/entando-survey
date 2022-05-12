<template>
    <div class="bg-white p-3 rounded-md">
        <table class="w-full">
            <tr class="border-t border-b" v-if="loading">
                <td class="py-4 px-3">
                    <p class="text-gray-600 text-sm text-center">loading messages...</p>
                </td>
            </tr>

            <tr v-else-if="!messages.data.length" class="border-t border-b">
                <td class="py-4 px-3">
                    <p class="text-gray-600 text-sm text-center">No messages available</p>
                </td>
            </tr>

            <tr v-else class="border-t border-b" v-for="message in messages.data" :key="message.id">
                <td class="py-4 px-3">
                    <p
                        v-if="message.sender"
                        class="text-primary font-semibold"
                    >{{ message.sender.name }}</p>
                    <p v-else class="text-gray-700 font-semibold">User Deleted</p>
                </td>
                <td class="py-4 px-3">
                    <p class="font-semibold" v-if="(message.topic)">{{ (message.topic.title) }}</p>
                </td>
                <td
                    class="py-4 px-3 text-sm"
                    style="max-width: 200px;"
                >{{ message.body | strlimit(30) }}</td>
                <td class="py-4 px-3 text-sm text-gray-500">Letto il: {{ message.read_at | date }}</td>
                <td class="py-4 px-3">
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
        </table>

        <portal to="message-pagination">
            <div class="flex flex-row-reverse">
                <b-paginator @change="onPageChange" :data="messages.meta" v-if="messages.meta"></b-paginator>
            </div>
        </portal>
    </div>
</template>

<script>
import { computed, defineComponent, onMounted, watch } from "@vue/composition-api";
import debounce from "lodash/debounce";
import useMessages from "@/compositions/messages";

export default defineComponent({
    name: "ReadList",
    setup(_, ctx) {
        const { loading, messages, getMessages } = useMessages();

        const filters = computed(() => ctx.attrs.filters);

        function fetchMessages(extra) {
            return getMessages({
                listType: "read",
                topics: filters.value.topics,
                search: filters.value.search,
                ...extra
            })
        }

        onMounted(fetchMessages);

        const onFiltersChange = debounce(fetchMessages, 500);

        watch(ctx.attrs.filters, onFiltersChange)

        return {
            loading,
            messages,
            onPageChange: ({ page }) => {
                fetchMessages({ page })
            }
        }
    }
})
</script>
