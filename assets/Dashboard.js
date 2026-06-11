class Dashboard {
    constructor() {
        this.currentChart = null;
        this.currentMetric = "likes";
        this.init();
    }

    init() {
        document.querySelectorAll(".dashboard-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                const type = btn.dataset.type;
                const id = btn.dataset.id;
                this.showDashboard(type, id);
            });
        });

        document.querySelectorAll(".metric-btn").forEach((btn) => {
            btn.addEventListener("click", () => {
                this.currentMetric = btn.dataset.metric;
                document
                    .querySelectorAll(".metric-btn")
                    .forEach((b) => b.classList.remove("active"));
                btn.classList.add("active");

                const activeContent = document.querySelector(
                    "#dashboard-panel.active",
                );

                if (activeContent) {
                    this.showDashboard(
                        activeContent.dataset.type,
                        activeContent.dataset.id,
                    );
                }
            });
        });
    }

    async showDashboard(type, id) {
        const panel = document.querySelector("#dashboard-panel");
        panel.classList.add("active");
        panel.dataset.type = type;
        panel.dataset.id = id;

        const data = await this.fetchData(type, id, this.currentMetric);
        this.renderChart(data);
    }

    async fetchData(type, id, metric) {
        const response = await fetch(`/dashboard/data/${type}/${id}/${metric}`);
        return await response.json();
    }

    renderChart(data) {
        const panel = document.querySelector("#dashboard-panel");
        const canvas = document.createElement("canvas");
        canvas.id = "dashboard-chart";
        panel.innerHTML = "";
        panel.appendChild(canvas);

        const ctx = document.querySelector("#dashboard-chart").getContext("2d");

        if (this.currentChart) {
            this.currentChart.destroy();
        }

        this.currentChart = new Chart(ctx, {
            type: "line",
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: data.metric,
                        data: data.values,
                        borderColor: "#111111", // Ligne noire
                        backgroundColor: "rgba(17,17,17,0.1)", // Fond légèrement gris
                        pointBackgroundColor: "#111111", // Points noirs
                        pointBorderColor: "#ffffff", // Bordure blanche sur les points
                        pointRadius: 5, // Taille des points
                        pointHoverRadius: 7, // Taille au survol
                        borderWidth: 2, // Épaisseur de la ligne
                        tension: 0.3,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: "#111111", // Couleur des chiffres Y
                        },
                        grid: {
                            color: "rgba(0,0,0,0.08)", // Grille plus visible
                        },
                    },
                    x: {
                        ticks: {
                            color: "#111111", // Couleur des dates X
                        },
                        grid: {
                            color: "rgba(0,0,0,0.08)",
                        },
                    },
                },
            },
        });
    }
}

new Dashboard();
