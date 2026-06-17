document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-erase-report-user").forEach((button) => {
        button.addEventListener("click", function () {
            const row = this.closest(".admin-user-action");

            const userId = this.getAttribute("data-user-id");

            if (!userId) {
                console.error("ID du commentaire introuvable sur le bouton.");
                return;
            }

            fetch(`/admin/delete/report/user/${userId}`, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        if (row) {
                            row.remove();
                        }
                    } else {
                        alert("Une erreur est survenue.");
                    }
                })
                .catch((error) => console.error("Erreur:", error));
        });
    });
});
