<template>
  <v-select
    class="text-sm"
    ref="vSelect"
    :components="components"
    :options="options"
    :value="value"
    :label="label"
    :placeholder="placeholder"
    :close-on-select="close_on_select || false"
    :multiple="multiple || false"
    :searchable="true"
    :deselect-from-dropdown="true"
    :reduce="reduce"
    @search="emitSearch"
    @input="input"
    :filterable="filterable"
  >
    <template #no-options v-if="serverMessage">
      <p>{{ serverMessage }}</p>
    </template>

    <template #open-indicator="{ attributes }">
      <span v-bind="attributes">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 fill-current text-primary"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
            clip-rule="evenodd"
          />
        </svg>
      </span>
    </template>

    <template #option="option">
      <slot
        name="option"
        :option="option"
        :isSelected="
          label
            ? vSelect.isOptionSelected(option)
            : vSelect.isOptionSelected(option.label)
        "
      >
        <div class="-mx-3 flex items-center space-x-2">
          <input
            type="checkbox"
            :checked="
              label
                ? vSelect.isOptionSelected(option)
                : vSelect.isOptionSelected(option.label)
            "
            style="pointer-events: none"
          />
          <span>{{ vSelect.getOptionLabel(option) }}</span>
        </div>
      </slot>
    </template>
  </v-select>
</template>

<script>
import { defineComponent, ref } from "@vue/composition-api";
import vSelect from "vue-select";

const Deselect = {
  template: `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
</svg>`,
};

export default defineComponent({
  name: "BaseSelect",
  emits: ["input", "search"],
  props: [
    "value",
    "options",
    "placeholder",
    "multiple",
    "searchable",
    "label",
    "reduce",
    "filterable",
    "serverMessage",
    "close_on_select",
  ],
  components: {
    "v-select": vSelect,
  },
  setup(_, context) {
    const vSelect = ref(null);

    function input(value) {
      return context.emit("input", value);
    }

    return {
      vSelect,
      components: {
        Deselect,
      },
      input,
      emitSearch(search, loading) {
        context.emit("search", search, loading);
      },
    };
  },
});
</script>
