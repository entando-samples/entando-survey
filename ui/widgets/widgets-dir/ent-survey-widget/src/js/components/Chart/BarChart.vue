<script>
import { Bar } from "vue-chartjs";
export default {
  extends: Bar,
  props: {
    labels: String,
    datas: Array,
  },
  data() {
    return {
      options: {
        responsive: true,
        maintainAspectRatio: true,
      },
    };
  },

  mounted() {
    let val = {
      labels: Array(this.labels ?? ""),
      datasets: this.datas,
    };
    this.renderChart(val, this.options);
  },
};
</script>