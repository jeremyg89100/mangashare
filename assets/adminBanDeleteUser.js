document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".ban-btn").forEach((button) => {
        button.addEventListener("click", () => {
            const userId = button.getAttribute("data-id");
            const csrfToken = button.getAttribute("data-token");

            if (!userId) {
                console.error("ID de l'utilisateur introuvable sur le bouton.");
                return;
            } else {
                fetch(`/admin/user/${userId}/ban`, {
                    method: "POST",
                    headers: {
                        "Content-type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(
                                `Erreur HTTP : Statut : ${response.status}`,
                            );
                        }
                        return response.json();
                    })
                    .then((data) => {
                        if (data.success) {
                            if (data.isEnabled === true) {
                                button.textContent = "Bannir";
                            } else if (data.isEnabled === false) {
                                button.textContent = "Débannir";
                            } else {
                                alert("Une erreur est survenue");
                            }
                        }
                    })
                    .catch((error) => console.error("Erreur :", error));
            }
        });
    });
    document.querySelectorAll(".delete-btn").forEach((button) => {
        button.addEventListener("click", () => {
            const userId = button.getAttribute("data-id");
            const csrfToken = button.getAttribute("data-token");
            const userCard = button.closest(".admin-user-action");

            if (!userId) {
                console.error("ID de l'utilisateur introuvable sur le bouton.");
                return;
            } else {
                if (
                    !confirm(
                        "Êtes-vous sûr de vouloir supprimer définitivement cet utilisateur ?",
                    )
                ) {
                    return;
                }
                fetch(`/admin/user/${userId}/delete`, {
                    method: "POST",
                    headers: {
                        "Content-type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(
                                `Erreur HTTP : Statut : ${response.status}`,
                            );
                        }
                        return response.json();
                    })
                    .then((data) => {
                        if (data.success) {
                            userCard.remove();
                        }
                    })
                    .catch((error) => console.error("Erreur :", error));
            }
        });
    });
});
