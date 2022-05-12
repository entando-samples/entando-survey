import client from "@/http";
import { reactive, ref } from "@vue/composition-api";

export default function useDashboard() {
    const report = reactive({
        documents: { total: 0, read: 0 },
        infoDocuments: { total: 0, read: 0 },
        surveys: { total: 0, alertable: 0, partially: 0 },
    })

    //need to update code 
    let documentsStat = reactive([
        {
            label: "Total",
            backgroundColor: "#2c445c",
            data: [0],
        }, {
            label: "unread",
            backgroundColor: "#ff6666",
            data: [0],
        }, {
            label: "read",
            backgroundColor: "#b3d9ff",
            data: [0],
        }
    ]);

    let infoDocStats = reactive([
        {
            label: "Total",
            backgroundColor: "#2c445c",
            data: [0],
        }, {
            label: "unread",
            backgroundColor: "#ff6666",
            data: [0],
        }, {
            label: "read",
            backgroundColor: "#b3d9ff",
            data: [0],
        }
    ]);

    let surveyStat = reactive([
        {
            label: "Not filled",
            backgroundColor: "#2c445c",
            data: [0],
        }, {
            label: "Alertable",
            backgroundColor: "#ff6666",
            data: [0],
        }, {
            label: "Partially",
            backgroundColor: "#b3d9ff",
            data: [0],
        }
    ]);

    function fetchReport() {
        return client.get("/backend/dashboard").then(response => {
            console.log(response.data.data);
            let data = response.data.data;
            report.documents.total = data.documents.total;
            report.documents.read = data.documents.read;
            report.infoDocuments.total = data.infoDocuments.total;
            report.infoDocuments.read = data.infoDocuments.read;
            //alertable survey
            report.surveys.total = data.surveys.total;
            report.surveys.alertable = data.surveys.alertable;
            report.surveys.partially = data.surveys.partially;

            fillDocumentStats(data.documents);
            fillInfoDocuments(data.infoDocuments);
            fillSurvey(data.surveys);
        })
    }

    function fillDocumentStats({ total, read }) {
        documentsStat[0].data.unshift(total);
        documentsStat[1].data.unshift(total - read);
        documentsStat[2].data.unshift(read);
    }

    function fillInfoDocuments({ total, read }) {
        infoDocStats[0].data.unshift(total);
        infoDocStats[1].data.unshift(total - read);
        infoDocStats[2].data.unshift(read);
    }

    function fillSurvey({ total, alertable, partially }) {
        surveyStat[0].data.unshift(total);
        surveyStat[1].data.unshift(alertable);
        surveyStat[2].data.unshift(partially);
    }

    return {
        report,
        fetchReport,
        documentsStat,
        infoDocStats,
        surveyStat
    }
}
