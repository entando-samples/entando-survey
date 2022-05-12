<template>
    <ul class="accordion space-y-3">
        <slot></slot>
    </ul>
</template>

<script>
import { defineComponent, provide, reactive } from "@vue/composition-api";

import AccordionItem from "./AccordionItem.vue";

export { AccordionItem };

export default defineComponent({
    name: "Accordion",
    props: {},
    setup() {
        provide('Accordion', reactive({
            count: 0,
            active: null
        }))
    }
})
</script>
