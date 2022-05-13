<template>
    <div class="base-block bg-white p-3 overflow-scroll">
        <p v-if="title" class="text-xl font-semibold text-primary mb-5">{{ title }}</p>
        <slot></slot>
    </div>
</template>

<script>
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
    name: "BaseBlock",
    props: ['title']
})
</script>
