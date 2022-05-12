import { reactive, set } from "@vue/composition-api";

export default function Validator(errors = {}) {
    const reactiveErrors = reactive(errors);

    function reset() {
        Object.keys(errors).forEach((key) => {
            set(reactiveErrors, key, "");
        });
    }

    function isValidationError(error) {
        return error?.response?.status === 422;
    }

    function adaptErr(err) {
        const validationErrs = err.response.data.errors;

        Object.keys(validationErrs).forEach((key) => {
            set(reactiveErrors, key, validationErrs[key][0]);
        });
    }

    return {
        isValidationError,
        errors: reactiveErrors,
        reset,
        adaptErr,
    };
}
