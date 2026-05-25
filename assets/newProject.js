function handleImagePreview(fileInput) {
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
                if (plusIcon) plusIcon.style.display = "none";
            }
            if (deleteBtn) deleteBtn.style.display = "flex";
        };
        reader.readAsDataURL(file);
    }
}

// Add image to miniature
const uploadPages = document.querySelector("#upload-pages");

let fileCounter = document.querySelectorAll(
    "#upload-pages .new-project-middle",
).length;

uploadPages.addEventListener("change", function (event) {
    if (event.target.classList.contains("file-input")) {
        const fileInput = event.target;
        const container = fileInput.closest(".new-project-middle");

        handleImagePreview(fileInput);

        if (fileInput.files[0] && container === uploadPages.lastElementChild) {
            fileCounter++;
            createNewUpload(fileCounter);
        }
    }
});

// Delete images
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-btn-pages")) {
        const deleteBtn = event.target;
        const container = deleteBtn.closest(".new-project-middle");

        if (!container) return;

        const fileInput = container.querySelector("input[type='file']");
        const imgPreview = container.querySelector(".image-preview");
        const plusIcon = container.querySelector(".plus-icon");
        const totalPages = uploadPages ? uploadPages.children.length : 0;

        if (container.closest("#upload-pages") && totalPages > 1) {
            container.remove();
        } else {
            if (fileInput) fileInput.value = "";
            if (imgPreview) {
                imgPreview.removeAttribute("src");
                imgPreview.style.display = "none";
            }
            if (plusIcon) plusIcon.style.display = "block";
            deleteBtn.style.display = "none";
        }
    }
});

// Create new input to upload another image
function createNewUpload(index) {
    const container = document.createElement("div");
    container.classList.add("new-project-middle");

    const input = document.createElement("input");
    input.type = "file";
    input.id = `file-upload-new-${index}`;
    input.name = "pages[]";
    input.accept = "image/png, image/jpeg";
    input.classList.add("file-input");

    const label = document.createElement("label");
    label.setAttribute("for", `file-upload-new-${index}`);
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
