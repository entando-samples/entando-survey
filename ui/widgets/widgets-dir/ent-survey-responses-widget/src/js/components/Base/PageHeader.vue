<template>
  <portal to="header">
    <div class="flex items-center">
      <h1 class="text-xl font-bold" v-if="title">{{ title }}</h1>

      <div class="ml-5">
        <slot></slot>
      </div>
    </div>
  </portal>
</template>


<script>
import { defineComponent, onMounted } from "@vue/composition-api";
export default defineComponent({
  name: "BasePageHeader",
  props: ["title"],
  setup(props) {
    onMounted(() => {
      document.title = props.title || "Survey";
    });
  },
});
</script>
