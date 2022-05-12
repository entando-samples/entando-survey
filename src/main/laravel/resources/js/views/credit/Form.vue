<template>
    <form class="mt-3" @submit.prevent="update">
        <b-page-header title="Credit"></b-page-header>

        <b-block>
            <div>
                <quill-editor v-model="content" :options="editorOptions"></quill-editor>
                <err-msg :message="errors.content"></err-msg>
            </div>

            <b-button :disabled="loading" type="submit" class="mt-5 py-2 w-full">Update</b-button>
        </b-block>
    </form>
</template>


<script>
import useCredit from "@/compositions/credit";
import { defineComponent, onMounted } from "@vue/composition-api";
import Vue from "vue";
import { quillEditor, editorOptions } from '@/components/Editor';

export default defineComponent({
    name: "CreditFormPage",
    components: { quillEditor },
    setup() {
        const { loading, content, errors, getCredit, updateCredit } = useCredit();

        onMounted(() => {
            getCredit()
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        })

        function update() {
            updateCredit(content.value)
                .then(() => {
                    Vue.toasted.success("Updated successfully")
                })
                .catch(err => {
                    Vue.toasted.error(err?.response?.data?.message || err.message)
                })
        }

        return {
            loading,
            errors,
            content,
            editorOptions,
            update
        }
    }
})
</script>
