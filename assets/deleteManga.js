document.addEventListener("turbo:load", () => {
    document.querySelectorAll(".btn-erase-manga").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            if (!confirm("Supprimer ce manga ?")) {
                return;
            }

            const mangaCard = this.closest(".project-card");
            const mangaId = this.getAttribute("data-manga-id");

            const form = this.closest("form");
            const csrfToken = form.querySelector('input[name="_token"]').value;

            if (!mangaId) {
                console.error("ID du manga introuvable sur le bouton.");
                return;
            }

            fetch(`/delete/manga/${mangaId}`, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-Token": csrfToken,
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        if (mangaCard) {
                            mangaCard.remove();
                        }
                    } else {
                        alert(data.message || "Une erreur est survenue.");
                    }
                })
                .catch((error) => console.error("Erreur:", error));
        });
    });
});
