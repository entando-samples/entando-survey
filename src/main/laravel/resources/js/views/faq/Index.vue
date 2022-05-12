<template>
    <div>
        <b-page-header title="FAQ" />

        <div class="flex justify-between">
            <b-button
                class="py-2"
                @click="toggleSort"
                :disabled="loading || sort.loading || (sort.can && !sort.shouldSave)"
            >{{ sort.can ? "Save Position" : "Enable Sort" }}</b-button>
            <router-link to="/faq/create">
                <b-button class="py-2">create</b-button>
            </router-link>
        </div>
        <div class="mt-5 bg-white p-3 rounded-md">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="p-3 text-left">Question</th>
                        <th class="p-3 text-left">Answer</th>
                        <th></th>
                    </tr>
                </thead>
                <tr class="border-t border-b" v-if="loading">
                    <td class="py-4 px-3" colspan="3">
                        <p class="text-gray-600 text-sm text-center">loading faq...</p>
                    </td>
                </tr>

                <tr v-else-if="!faq.length" class="border-t border-b">
                    <td class="py-4 px-3" colspan="3">
                        <p class="text-gray-600 text-sm text-center">No faq available</p>
                    </td>
                </tr>

                <draggable
                    tag="tbody"
                    v-else
                    v-model="faq"
                    ghost-class="sort-ghost"
                    :sort="sort.can"
                    @update="sort.shouldSave = true"
                >
                    <tr class="bg-white border-t border-b" v-for="qa in faq" :key="qa.id">
                        <td class="py-4 px-3">
                            <p style="max-width: 300px;">{{ qa.question | strlimit(200) }}</p>
                        </td>
                        <td class="py-4 px-3">
                            <p style="max-width: 300px;">{{ qa.answer | strlimit(200) }}</p>
                        </td>
                        <td class="py-4 px-3 flex items-center space-x-3">
                            <router-link
                                :to="{ name: 'faq.edit', params: { id: qa.id } }"
                                class="inline-block bg-primary border-2 border-primary rounded-sm p-1 text-center"
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
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                    />
                                </svg>
                            </router-link>
                            <button
                                @click="deleteItem($event, qa.id)"
                                class="p-1 text-primary hover:text-white border-2 border-primary rounded hover:bg-primary"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </draggable>
            </table>
        </div>
    </div>
</template>

<script>
import useFaq from "@/compositions/faq";
import { defineComponent, onMounted, reactive, ref } from "@vue/composition-api";
import Vue from "vue";
import draggable from "vuedraggable"

export default defineComponent({
    name: "FaqIndexPage",
    components: {
        draggable,
    },
    setup() {
        const { loading, faq, getFaq, deleteQa, saveSort } = useFaq();
        const sort = reactive({
            loading: false,
            can: false,
            shouldSave: false,
        });

        onMounted(fetchFaq);

        function fetchFaq() {
            getFaq()
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        }

        function deleteItem(event, id) {
            const closestRow = event.target.closest('tr');

            if (!confirm("Are you sure ?")) return;

            closestRow.style.opacity = '.6';
            closestRow.style.pointerEvents = 'none';

            deleteQa(id)
                .then(() => {
                    Vue.toasted.success('Success');
                    fetchFaq();
                })
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        }

        function toggleSort() {
            sort.can = !sort.can;

            if (sort.can) {
                Vue.toasted.info("Drag and drop row to sort");
            } else {
                if (sort.shouldSave) saveOrder();
            }
        }

        function saveOrder() {
            const ids = faq.value.map(item => item.id)
            sort.loading = true;

            saveSort(ids)
                .then(() => {
                    sort.shouldSave = false;
                    Vue.toasted.success("Sort saved successfully")
                })
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message);
                })
                .finally(() => { sort.loading = false });
        }

        return {
            sort,
            loading,
            faq,
            deleteItem,
            toggleSort
        }
    }
})
</script>

<style>
.sort-ghost {
    @apply opacity-50 bg-gray-300;
}
</style>
