<template>
  <div>
    <b-page-header title="Dashboard" />
    <div>
      <div class="mt-4 grid grid-cols-3 gap-x-4">
        <div class="text-center py-10 bg-white rounded-lg">
          <bar-chart
            v-if="!loading && windowwidth > 1279"
            :datas="documentsStat"
          ></bar-chart>
          <p class="text-xl font-semibold">
            <span class="text-red-500 hover:text-red-900 cursor-pointer">
              {{ report.documents.total - report.documents.read }}
            </span>
            /
            {{ report.documents.total }}
          </p>
          <p class="mt-2 text-xl">Patient documents</p>
        </div>
        <div class="text-center py-10 bg-white rounded-lg">
          <bar-chart
            v-if="!loading && windowwidth > 1279"
            :datas="infoDocStats"
          ></bar-chart>
          <p class="text-xl font-semibold">
            <router-link
              :to="{ path: '/patients', query: { infodocument: 'unread' } }"
            >
              <span class="text-red-500 hover:text-red-900 cursor-pointer">
                {{ report.infoDocuments.total - report.infoDocuments.read }}
              </span> </router-link
            >/
            {{ report.infoDocuments.total }}
          </p>
          <p class="mt-2 text-xl">Info Documents</p>
        </div>
        <div class="text-center py-10 bg-white rounded-lg">
          <bar-chart
            v-if="!loading && windowwidth > 1279"
            :datas="surveyStat"
          ></bar-chart>
          <p class="text-xl font-semibold">
            <span class="text-red-500 hover:text-red-900 cursor-pointer">
              {{ report.surveys.alertable }}
            </span>
            / {{ report.surveys.total }}
          </p>
          <p class="mt-2 text-xl">Questionari</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import useDashboard from "@/compositions/dashboard";
import { defineComponent, onMounted, ref } from "@vue/composition-api";
import Vue from "vue";
import BarChart from "../components/Chart/BarChart.vue";
export default defineComponent({
  components: { BarChart },
  name: "DashboardPage",

  setup() {
    let loading = ref(true);
    const windowwidth = ref(window.innerWidth);
    const { report, fetchReport, documentsStat, infoDocStats, surveyStat } =
      useDashboard();
    fetchReport()
      .catch((err) => {
        Vue.toasted.error(err?.response?.data?.message || err.message);
      })
      .finally(() => {
        loading.value = false;
      });
    console.log(windowwidth.value);
    onMounted(() => {
      window.addEventListener("resize", () => {
        windowwidth.value = window.innerWidth;
        console.log("on rezise", windowwidth.value);
      });
    });

    return {
      report,
      documentsStat,
      loading,
      infoDocStats,
      surveyStat,
      windowwidth,
    };
  },
});
</script>
