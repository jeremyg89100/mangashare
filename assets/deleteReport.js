document.addEventListener("turbo:load", () => {
    document.querySelectorAll(".btn-erase-report-comment").forEach((button) => {
        button.addEventListener("click", function () {
            const row = this.closest(".table-row");

            const commentId = this.getAttribute("data-comment-id");

            if (!commentId) {
                console.error("ID du commentaire introuvable sur le bouton.");
                return;
            }

            fetch(`/admin/delete/report/${commentId}`, {
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
