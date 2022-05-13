<template>
    <div class="flex items-center text-sm max-w-3xl">
        <button class="p-3 hover:bg-gray-200" @click="goToPage(1)" :disabled="onFirstPage">
            <svg class="w-3 h-3" viewBox="0 0 6 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M5.3291 5.5L5.9166 4.9125L4.00827 3L5.9166 1.0875L5.3291 0.5L2.8291 3L5.3291 5.5Z"
                    fill="#303233"
                />
                <path
                    d="M2.5835 5.5L3.171 4.9125L1.26266 3L3.171 1.0875L2.5835 0.5L0.0834964 3L2.5835 5.5Z"
                    fill="#303233"
                />
            </svg>
        </button>
        <button
            class="p-3 hover:bg-gray-200 flex"
            @click="goToPage(data.currentPage - 1)"
            :disabled="onFirstPage"
        >
            <svg class="w-3 h-3" viewBox="0 0 4 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M2.5835 5.5L3.171 4.9125L1.26266 3L3.171 1.0875L2.5835 0.5L0.0834964 3L2.5835 5.5Z"
                    fill="#303233"
                />
            </svg>
        </button>
        <div
            class="mx-1 grid gap-y-2"
            :class="elements.length > 12 ? 'grid-cols-12' : 'grid-flow-col'"
        >
            <button
                v-for="element in elements"
                :key="element"
                @click="goToPage(element)"
                :disabled="element === data.currentPage"
                class="py-1 px-3 border-b-2 border-transparent hover:text-primary hover:bg-primary hover:bg-opacity-20 hover:border-primary"
                :class="{ 'text-primary bg-primary bg-opacity-20 border-primary': element === data.currentPage }"
            >{{ element }}</button>
        </div>
        <button class="p-3 hover:bg-gray-200" @click="goToPage(data.currentPage + 1)">
            <svg
                class="w-3 h-3 transform rotate-180"
                viewBox="0 0 4 6"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M2.5835 5.5L3.171 4.9125L1.26266 3L3.171 1.0875L2.5835 0.5L0.0834964 3L2.5835 5.5Z"
                    fill="#303233"
                />
            </svg>
        </button>
        <button class="p-3 hover:bg-gray-200" @click="goToPage(data.lastPage)">
            <svg
                class="w-3 h-3 transform rotate-180"
                viewBox="0 0 6 6"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M5.3291 5.5L5.9166 4.9125L4.00827 3L5.9166 1.0875L5.3291 0.5L2.8291 3L5.3291 5.5Z"
                    fill="#303233"
                />
                <path
                    d="M2.5835 5.5L3.171 4.9125L1.26266 3L3.171 1.0875L2.5835 0.5L0.0834964 3L2.5835 5.5Z"
                    fill="#303233"
                />
            </svg>
        </button>
    </div>
</template>

<script>
import { defineComponent, computed } from "@vue/composition-api";

export default defineComponent({
    name: "BasePaginator",
    emits: ['change'],
    props: ['data'],
    setup(props, { emit }) {
        const onFirstPage = computed(() => {
            return props.data.currentPage === 1;
        });

        function goToPage(pageNumber) {
            if (pageNumber >= 1 && pageNumber <= props.data.lastPage) {
                emit('change', { page: pageNumber });
            }
        }

        const elements = computed(() => {
            let elements = [];
            for (let page = 1; page <= props.data.lastPage; page++) {
                elements.push(page);
            }

            return elements;
        });

        return {
            onFirstPage,
            goToPage,
            elements,
        }
    }
})
</script>
