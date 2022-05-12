<template>
    <span class="text-xs text-red-500" v-if="message">{{ message }}</span>
</template>

<script>
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
    name: "ErrorMessage",
    props: ['message'],
})
</script>
