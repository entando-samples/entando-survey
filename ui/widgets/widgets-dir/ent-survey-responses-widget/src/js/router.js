// import { useAuthStore } from "@/stores/auth";
import LogoutPage from "@/views/auth/Logout.vue";
import SurveyIndexPage from "@/views/surveys/Index.vue";
import SurveyFormPage from "@/views/surveys/Form.vue";
import NotFoundPage from "@/views/errors/NotFound.vue";
import AppLayout from "@/views/layouts/AppLayout.vue";
import ErrorLayout from "@/views/layouts/ErrorLayout.vue";
import VueRouter from "vue-router";

const auth = {
    logout: LogoutPage
};

const surveys = {
    index: SurveyIndexPage,
    form: SurveyFormPage,
};

const errors = {
    notFound: NotFoundPage,
};

const router = new VueRouter({
    mode: "hash",
    linkActiveClass: "active--nav",
    routes: [
        {
            path: "/",
            component: AppLayout,
            meta: {
                guest: true,
                // auth: true,
            },
            redirect: "surveys",
            children: [
                {
                    path: "surveys",
                    name: "surveys.index",
                    component: surveys.index,
                },
                {
                    path: "surveys/create",
                    name: "surveys.create",
                    component: surveys.form,
                },
                {
                    path: "surveys/:id",
                    name: "surveys.edit",
                    component: surveys.form,
                },
                {
                    path: "logout",
                    name: "logout",
                    component: auth.logout,
                },
            ],
        },
        {
            path: "/",
            component: ErrorLayout,
            children: [
                {
                    path: "*",
                    name: "404",
                    component: errors.notFound,
                },
            ],
        },
    ],
});

// router.beforeEach((to, _, next) => {
//     const authStore = useAuthStore();
//     if (to.matched.some((record) => record.meta.auth)) {
//         if (authStore.isAuthenticated) {
//             next();
//         } else {
//             if (to.name === "logout") {
//                 next({ name: "login" });
//             } else {
//                 next({
//                     name: "login",
//                     query: {
//                         redirect: to.fullPath,
//                     },
//                 });
//             }
//         }
//     } else if (to.matched.some((record) => record.meta.guest)) {
//         if (authStore.isAuthenticated) {
//             next({
//                 name: "questions",
//             });
//         } else {
//             next();
//         }
//     } else {
//         next();
//     }
// });

export default router;
