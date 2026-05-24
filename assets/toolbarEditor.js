const quill = new Quill("#editor-article", {
    theme: "snow",
    modules: {
        toolbar: [
            ["bold", "italic"],
            ["blockquote", "link"],
            ["image"],
            ["clean"],
        ],
    },
});

const toolbar = quill.getModule("toolbar");
toolbar.addHandler("image", () => {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/jpeg, image/png, image/gif, image/webp";

    input.addEventListener("change", async () => {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("upload", file);

        try {
            const response = await fetch("/upload-article-image", {
                method: "POST",
                body: formData,
            });

            const data = await response.json();
            console.log("Réponse du serveur :", data);

            if (data.url) {
                quill.focus();
                const range = quill.getSelection();
                quill.insertEmbed(range.index, "image", data.url);
            } else {
                console.error("Erreur de l'upload : ", data.error);
            }
        } catch (error) {
            console.error("Erreur : ", error);
        }
    });
    input.click();
});

const form = document.querySelector("form");
if (form) {
    form.addEventListener("submit", () => {
        document.querySelector("#article-content-hidden").value =
            quill.root.innerHTML;
    });
}
