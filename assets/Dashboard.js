class Dashboard {
    constructor() {
        this.currentChart = null;
        this.currentMetric = "likes";
        this.currentWeek = this.getCurrentWeek();
        this.init();
    }

    getCurrentWeek() {
        const now = new Date();
        // In UTC to avoid bug with summer/winter hours
        const target = new Date(
            Date.UTC(now.getFullYear(), now.getMonth(), now.getDate()),
        );
        // Get the day on ISO (Monday = 0, Sunday = 6)
        const dayNr = (target.getUTCDay() + 6) % 7;
        // Move the date to Thursday of the current week
        target.setUTCDate(target.getUTCDate() - dayNr + 3);
        const isoYear = target.getUTCFullYear();
        // January 4th is always on week 1
        const firstThursday = new Date(Date.UTC(isoYear, 0, 4));
        const firstDayNr = (firstThursday.getUTCDay() + 6) % 7;
        // Find the 1st thirsday of year to get the starting point
        firstThursday.setUTCDate(firstThursday.getUTCDate() - firstDayNr + 3);
        // Calc the number of weeks
        const week = 1 + Math.round((target - firstThursday) / (7 * 86400000));
        // Return the ISO Number of the week
        return `${isoYear}-W${String(week).padStart(2, "0")}`;
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
                        borderColor: "#FFFFFF",
                        pointBackgroundColor: "#b8902f",
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
                            color: "#aaaaaa",
                        },
                        grid: {
                            color: "rgba(255, 255, 255, 0.08)",
                        },
                    },
                    x: {
                        ticks: {
                            color: "#aaaaaa",
                        },
                        grid: {
                            color: "rgba(255, 255, 255, 0.05)",
                        },
                    },
                },
            },
        });
    }
}

new Dashboard();
