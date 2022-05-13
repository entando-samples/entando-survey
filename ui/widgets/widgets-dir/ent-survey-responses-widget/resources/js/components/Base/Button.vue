<template>
    <button
        @click="$emit('click', $event)"
        class="px-5 py-1 text-center text-white text-sm font-bold rounded hover:shadow-lg"
        :class="variantClass"
    >
        <slot></slot>
    </button>
</template>

<script>
import { defineComponent, computed } from "@vue/composition-api";

export default defineComponent({
    name: "BaseButton",
    props: ['variant'],
    emits: ['click'],
    setup(props) {
        const variantClass = computed(() => {
            return {
                'primary': 'bg-primary border-2 border-primary',
                'primary-alt': 'border-2 border-primary text-primary hover:bg-primary hover:text-white',
            }[props.variant || 'primary']
        })

        return {
            variantClass
        }
    },
})
</script>
