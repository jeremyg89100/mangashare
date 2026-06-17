document.addEventListener("turbo:load", () => {
    document.querySelectorAll(".admin-user-action").forEach((userRow) => {
        const commentBtn = userRow.querySelector(".comment-btn");
        const commentsListContainer = userRow.querySelector(
            ".user-comments-list",
        );

        if (!commentBtn || !commentsListContainer) return;

        commentBtn.addEventListener("click", () => {
            const userId = commentBtn.getAttribute("data-id");

            if (commentsListContainer.classList.contains("loaded")) {
                commentsListContainer.classList.toggle("hidden");
                commentBtn.textContent =
                    commentsListContainer.classList.contains("hidden")
                        ? "Afficher les commentaires"
                        : "Masquer les commentaires";
                return;
            }

            fetch(`/admin/user/${userId}/comments`, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            })
                .then((response) => {
                    if (!response.ok)
                        throw new Error(
                            "Impossible de charger les commentaires",
                        );
                    return response.json();
                })
                .then((data) => {
                    if (data.success && data.comments.length > 0) {
                        commentsListContainer.innerHTML = "";

                        data.comments.forEach((comment) => {
                            const commentElement =
                                createAdminCommentRow(comment);
                            commentsListContainer.appendChild(commentElement);
                        });
                    } else {
                        commentsListContainer.innerHTML =
                            "<p class='no-comment'>Aucun commentaire pour cet utilisateur.</p>";
                    }

                    commentsListContainer.classList.add("loaded");
                    commentsListContainer.classList.remove("hidden");
                    commentBtn.textContent = "Masquer les commentaires";
                })
                .catch((error) => console.error("Erreur :", error));
        });
    });
});

function createAdminCommentRow(comment) {
    const mainDiv = document.createElement("div");
    mainDiv.className = "admin-comment-item";

    const topDiv = document.createElement("div");
    topDiv.classList = "admin-comment-top";

    const dateDiv = document.createElement("div");
    dateDiv.className = "comment-meta";
    dateDiv.textContent = `Le ${comment.date}`;

    const contentDiv = document.createElement("div");
    contentDiv.className = "comment-body";
    contentDiv.textContent = comment.content;

    const link = document.createElement("a");
    link.target = "_blank";
    link.classList = "comment-link";
    link.textContent = "Lien du commentaire";

    if (comment.mangaId && comment.mangaLink) {
        link.href = comment.mangaLink;
    }

    topDiv.append(dateDiv, link);
    mainDiv.append(topDiv, contentDiv);

    return mainDiv;
}
