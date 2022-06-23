<template>
    <div>
        <router-link
            :to="to"
            class="px-4 py-3 flex items-center space-x-2 text-nav hover:bg-ternary-dark"
        >
            <slot/>
        </router-link>
    </div>
</template>

<script>
import { defineComponent } from "@vue/composition-api";

export default defineComponent({
    name: "NavItem",
    props: ['to'],
    setup() {
        return {};
    },
})
</script>
