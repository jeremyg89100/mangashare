document.addEventListener("turbo:load", () => {
    document.querySelectorAll(".btn-delete-comment").forEach((button) => {
        button.addEventListener("click", function () {
            const comment = this.closest(".manga-comment");

            const commentId = this.getAttribute("data-comment-id");

            if (!confirm("Supprimer ce commentaire ?")) {
                return;
            }

            if (!commentId) {
                console.error("ID du commentaire introuvable sur le bouton.");
                return;
            }

            fetch(`/admin/delete/comment/${commentId}`, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        if (comment) {
                            comment.remove();
                        }
                    } else {
                        alert("Une erreur est survenue.");
                    }
                })
                .catch((error) => console.error("Erreur:", error));
        });
    });
});
