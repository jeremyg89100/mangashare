document.addEventListener("turbo:load", () => {
    const reportButtons = document.querySelectorAll(".btn-comment-report");
    const confirmBtn = document.querySelector("#confirmReportBtn");
    const myModal = new bootstrap.Modal(document.querySelector("#reportModal"));
    let currentCommentId = null;

    reportButtons.forEach((button) => {
        button.addEventListener("click", () => {
            currentCommentId = button.getAttribute("data-comment-id");
            myModal.show();
        });
    });

    confirmBtn.addEventListener("click", () => {
        if (!currentCommentId) return;

        const cleanId = currentCommentId.trim();

        fetch(`/comment/${cleanId}/signaler`, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                myModal.hide();
                if (data.success) {
                    alert("Le commentaire a bien été signalé");
                }
            })
            .catch((error) => console.error("Erreur", error));
    });
});
