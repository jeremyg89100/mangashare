function addComment() {
    const form = document.querySelector(".form-comment");
    const commentBtn = document.querySelector(".comment-btn");

    if (!form) return;

    if (commentBtn) {
        commentBtn.addEventListener("click", () => {
            if (form.querySelector(".comment-container")) {
                return;
            }

            const container = document.createElement("div");
            container.className = "comment-container";

            const textarea = document.createElement("textarea");
            textarea.className = "input-comment";
            textarea.placeholder = "Ecrivez votre commentaire";
            textarea.name = "comment-content";
            textarea.required = true;

            const editBtn = document.createElement("div");
            editBtn.className = "comment-form-btn";

            const submitButton = document.createElement("button");
            submitButton.textContent = "Envoyer";
            submitButton.type = "submit";

            const cancelButton = document.createElement("button");
            cancelButton.textContent = "Annuler";
            cancelButton.type = "button";

            editBtn.append(submitButton, cancelButton);
            container.append(textarea, editBtn);

            form.appendChild(container);

            cancelButton.addEventListener("click", () => {
                container.remove();
            });
        });
    }

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        const pseudo = form.dataset.userPseudo || "Anonyme";
        const miniature = form.dataset.miniature || "";

        const textarea = form.querySelector(".input-comment");
        const content = textarea ? textarea.value : "";
        const date = new Date().toLocaleDateString("fr-FR");

        showComments(pseudo, date, content, miniature);

        const url = form.getAttribute("action");
        const formData = new FormData();
        formData.append("comment-content", content);

        fetch(url, {
            method: "POST",
            body: formData,
        }).then((response) => {
            if (!response.ok) {
                console.error("Erreur lors du chargement en BDD");
            }
        });

        const container = form.querySelector(".comment-container");
        if (container) container.remove();
    });
}

function showComments(pseudo, date, content, miniature) {
    const commentList = document.querySelector(".comment-list");
    if (!commentList) return;

    const newCommentContainer = document.createElement("div");
    newCommentContainer.className = "manga-comment";

    const commentInfo = document.createElement("div");
    commentInfo.className = "comment-info";

    const commentContent = document.createElement("div");
    commentContent.className = "comment-content";

    const avatarContainer = document.createElement("div");
    avatarContainer.className = "nav-avatar";

    const commentNameAvatar = document.createElement("div");
    commentNameAvatar.className = "comment-name-avatar";

    const pseudoContainer = document.createElement("span");
    pseudoContainer.className = "comment-user-pseudo";
    const dateContainer = document.createElement("span");
    dateContainer.className = "comment-date";
    const contentContainer = document.createElement("span");

    if (miniature && miniature.trim() !== "" && miniature !== "null") {
        const imgAvatar = document.createElement("img");
        imgAvatar.src = `uploads/miniatures/${miniature}`;
        imgAvatar.alt = "Avatar";
        avatarContainer.appendChild(imgAvatar);
    } else {
        const initial = pseudo ? pseudo.slice(0, 1).toUpperCase() : "?";
        avatarContainer.textContent = initial;
    }

    pseudoContainer.textContent = pseudo;
    dateContainer.textContent = date;
    contentContainer.textContent = content;

    commentContent.appendChild(contentContainer);
    commentNameAvatar.append(avatarContainer, pseudoContainer);
    commentInfo.append(commentNameAvatar, dateContainer);
    newCommentContainer.append(commentInfo, commentContent);

    commentList.prepend(newCommentContainer);
}

addComment();
