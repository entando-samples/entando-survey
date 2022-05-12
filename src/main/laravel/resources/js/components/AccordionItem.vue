<template>
    <li class="relative">
        <div
            class="accordion__trigger"
            :class="{ 'accordion__trigger_active': visible }"
            @click="open"
        >
            <div class="flex space-x-1 items-center font-semibold">
                <div>
                    <svg
                        v-if="visible"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6 fill-current"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
                <slot name="accordion-trigger"></slot>
            </div>
            <div class="text-sm font-medium" :class="{'text-white': visible, 'text-gray-500': !visible}">
                <slot name="accordion-trigger-end"></slot>
            </div>
        </div>

        <transition
            name="accordion"
            @enter="start"
            @after-enter="end"
            @before-leave="start"
            @after-leave="end"
        >
            <div class="accordion__content py-3 px-6" v-show="visible">
                <!-- This slot will handle all the content that is passed to the accordion -->
                <slot name="accordion-content"></slot>
            </div>
        </transition>
    </li>
</template>


<script>
import { computed, defineComponent, ref, inject } from "@vue/composition-api";

export default defineComponent({
    name: "AccordionItem",
    props: {},
    setup() {
        const index = ref(null);
        const Accordion = inject('Accordion');

        const visible = computed(() => {
            return index.value == Accordion.active;
        });

        index.value = Accordion.count++

        function open() {
            if (visible.value) {
                Accordion.active = null;
            } else {
                Accordion.active = index.value;
            }
        }

        function start(el) {
            el.style.height = el.scrollHeight + "px";
        }

        function end(el) {
            el.style.height = "";
        }

        return {
            visible,
            open,
            start,
            end
        }
    }
});
</script>

<style scoped>
.accordion__trigger {
    @apply cursor-pointer px-5 py-2 bg-gray-200 flex items-center justify-between text-primary;
}

.accordion__trigger_active {
    @apply bg-ternary text-white;
}
.accordion-enter-active,
.accordion-leave-active {
    will-change: height, opacity;
    transition: height 0.3s ease, opacity 0.3s ease;
    overflow: hidden;
}

.accordion-enter,
.accordion-leave-to {
    height: 0 !important;
    opacity: 0;
}
</style>
