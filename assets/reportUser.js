document.addEventListener("turbo:load", () => {
    const reportButtons = document.querySelectorAll(".btn-user-report");
    const confirmBtn = document.querySelector("#confirmReportBtn");
    const myModal = new bootstrap.Modal(document.querySelector("#reportModal"));
    const reasonText = document.querySelector("#reportReason");
    let currentUserId = null;

    reportButtons.forEach((button) => {
        button.addEventListener("click", () => {
            currentUserId = button.getAttribute("data-user-id");
            myModal.show();
        });
    });

    confirmBtn.addEventListener("click", () => {
        if (!currentUserId) return;

        const textValue = reasonText.value.trim();

        if (textValue === "") {
            alert("Veuillez spécifier une raison pour le signalement.");
            return;
        }

        const cleanId = currentUserId.trim();

        fetch(`/admin/user/${cleanId}/signaler`, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                reason: textValue,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                myModal.hide();
                if (data.success) {
                    alert(
                        "L'utilisateur a bien été signalé pour raison : ",
                        textValue,
                    );
                    reasonInput.value = "";
                }
            })
            .catch((error) => console.error("Erreur", error));
    });
});
