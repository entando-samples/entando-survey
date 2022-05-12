
<template>
  <portal to="modals">
    <transition name="modal">
      <div
        @click.self="$emit('close')"
        class="
          fixed
          z-50
          top-0
          left-0
          w-full
          h-screen
          bg-black bg-opacity-40
          transition-opacity
          ease-in
        "
      >
        <div class="h-full mx-auto pt-48" :class="sizeClass">
          <div class="bg-white px-8 py-5 rounded">
            <div class="flex items-center justify-between">
              <div>
                <slot name="header">
                  <h1 class="text-xl uppercase font-semibold tracking-wide">
                    {{ title }}
                  </h1>
                </slot>
              </div>
              <button @click.prevent="$emit('close')">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-7 w-7 fill-current"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
            <div class="mt-5">
              <slot></slot>
            </div>
            <div class="mt-10" v-if="!!slots.footer">
              <slot name="footer"></slot>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </portal>
</template>

<script>
import {
  defineComponent,
  onMounted,
  onUnmounted,
  computed,
} from "@vue/composition-api";

export default defineComponent({
  name: "BaseModal",
  props: ["title", "size", "closeOnEsc"],
  setup(props, { emit, slots }) {
    const sizeClass = computed(() => {
      return (
        {
          md: "max-w-md",
          xl: "max-w-xl",
          "2xl": "max-w-2xl",
          "3xl": "max-w-3xl",
          "4xl": "max-w-4xl",
          "5xl": "max-w-5xl",
          "6xl": "max-w-6xl",
        }[props.size] || "max-w-xl"
      );
    });

    const escapeKeyFn = function (evt) {
      if (evt.keyCode === 27) {
        emit("close");
      }
    };

    onMounted(() => {
      document.addEventListener("keyup", escapeKeyFn);
      document.body.style.overflowY = "hidden";
    });

    onUnmounted(() => {
      document.removeEventListener("keyup", escapeKeyFn);
      document.body.style.overflowY = "auto";
    });

    return { sizeClass, slots };
  },
});
</script>
