class Dashboard {
    constructor() {
        this.currentChart = null;
        this.currentMetric = "likes";
        this.currentWeek = this.getCurrentWeek();
        this.init();
    }

    getCurrentWeek() {
        const now = new Date();
        const year = now.getFullYear();
        const oneJan = new Date(year, 0, 1);
        const week = Math.ceil(
            ((now - oneJan) / 86400000 + oneJan.getDay() + 1) / 7,
        );
        return `${year}-W${String(week).padStart(2, "0")}`;
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
                this.refreshDashboard();
            });
        });
        const weekPicker = document.querySelector("#week-picker");
        if (weekPicker) {
            weekPicker.value = this.currentWeek;
            weekPicker.addEventListener("change", (e) => {
                this.currentWeek = e.target.value;
                this.refreshDashboard();
            });
        }
    }

    refreshDashboard() {
        const activeContent = document.querySelector("#dashboard-panel.active");
        if (activeContent) {
            this.showDashboard(
                activeContent.dataset.type,
                activeContent.dataset.id,
            );
        }
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
        const response = await fetch(
            `/dashboard/data/${type}/${id}/${metric}?week=${this.currentWeek}`,
        );
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
                        borderColor: "#111111",
                        backgroundColor: "rgba(17,17,17,0.1)",
                        pointBackgroundColor: "#111111",
                        pointBorderColor: "#ffffff",
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 2,
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
                            color: "#111111",
                        },
                        grid: {
                            color: "rgba(0,0,0,0.08)",
                        },
                    },
                    x: {
                        ticks: {
                            color: "#111111",
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
