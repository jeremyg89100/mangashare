const uploadPages = document.querySelector("#upload-pages");
let fileCounter = 0;

uploadPages.addEventListener("change", function (event) {
    if (event.target.classList.contains("file-input")) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        const container = fileInput.closest(".new-project-middle");
        const imgPreview = container.querySelector(".image-preview");
        const plusIcon = container.querySelector(".plus-icon");
        const deleteBtn = container.querySelector(".delete-btn-pages");

        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (imgPreview) {
                    imgPreview.src = e.target.result;
                    imgPreview.style.display = "block";
                    plusIcon.style.display = "none";
                }

                if (deleteBtn) deleteBtn.style.display = "flex";

                if (container === uploadPages.lastElementChild) {
                    fileCounter++;
                    createNewUpload(fileCounter);
                }
            };
            reader.readAsDataURL(file);
        }
    }
});

uploadPages.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-btn-pages")) {
        const deleteBtn = event.target;
        const container = deleteBtn.closest(".new-project-middle");

        if (uploadPages.children.length === 1) {
            const fileInput = container.querySelector(".file-input");
            const imgPreview = container.querySelector(".image-preview");
            const plusIcon = container.querySelector(".plus-icon");

            fileInput.value = "";
            imgPreview.removeAttribute("src");
            imgPreview.style.display = "none";
            plusIcon.style.display = "block";
            deleteBtn.style.display = "none";
        } else {
            container.remove();
        }
    }
});

function createNewUpload(index) {
    const container = document.createElement("div");
    container.classList.add("new-project-middle");

    // Create file input
    const input = document.createElement("input");
    input.type = "file";
    input.id = `file-upload-${index}`;
    input.name = "pages[]";
    input.accept = "image/png, image/jpeg";
    input.classList.add("file-input");

    const label = document.createElement("label");
    label.setAttribute("for", `file-upload-${index}`);
    label.classList.add("upload-file");

    const plusSpan = document.createElement("span");
    plusSpan.classList.add("plus-icon");
    plusSpan.textContent = "+";

    const img = document.createElement("img");
    img.classList.add("image-preview");
    img.alt = "Aperçu";
    img.style.display = "none";

    const deleteBtnPage = document.createElement("span");
    deleteBtnPage.classList.add("delete-btn-pages");
    deleteBtnPage.innerHTML = "&times;";
    deleteBtnPage.style.display = "none";

    label.append(plusSpan, img);

    container.append(input, label, deleteBtnPage);

    uploadPages.appendChild(container);
}
