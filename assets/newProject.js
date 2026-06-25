function handleImagePreview(fileInput) {
    const file = fileInput.files[0];
    const container =
        fileInput.closest(".new-project-middle") ||
        fileInput.closest(".avatar-profil-30px");
    const imgPreview = container.querySelector(".image-preview");
    const plusIcon = container.querySelector(".plus-icon");
    const deleteBtn = container.querySelector(".delete-btn-pages");
    const letterPlaceholder =
        container.querySelector(".avatar-letter") ||
        container.querySelector(".avatar-placeholder");

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            if (imgPreview) {
                imgPreview.src = e.target.result;
                imgPreview.style.display = "block";
                if (plusIcon) plusIcon.style.display = "none";
            }
            if (letterPlaceholder) {
                letterPlaceholder.style.setProperty("display", "none");
            }
            if (deleteBtn) deleteBtn.style.display = "flex";
        };
        reader.readAsDataURL(file);
    }
}

function previewFileInSlot(file, container) {
    const imgPreview = container.querySelector(".image-preview");
    const plusIcon = container.querySelector(".plus-icon");
    const deleteBtn = container.querySelector(".delete-btn-pages");

    const reader = new FileReader();
    reader.onload = function (e) {
        imgPreview.src = e.target.result;
        imgPreview.style.display = "block";
        if (plusIcon) plusIcon.style.display = "none";
        if (deleteBtn) deleteBtn.style.display = "flex";
    };
    reader.readAsDataURL(file);
}

const uploadPages = document.querySelector("#upload-pages");

let fileCounter = document.querySelectorAll(
    "#upload-pages .new-project-middle",
).length;

document.addEventListener("change", function (event) {
    if (event.target.classList.contains("file-input")) {
        const fileInput = event.target;
        const container = fileInput.closest(".new-project-middle");
        const files = Array.from(fileInput.files);

        if (files.length === 0) return;

        if (files.length === 1) {
            handleImagePreview(fileInput);

            if (container === uploadPages.lastElementChild) {
                fileCounter++;
                createNewUpload(fileCounter);
            }
            return;
        }
        // Add multiple files
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        fileInput.files = dt.files;
        previewFileInSlot(files[0], container);

        for (let i = 1; i < files.length; i++) {
            fileCounter++;
            const newContainer = createNewUpload(fileCounter);
            const newInput = newContainer.querySelector("input[type='file']");

            const singleDt = new DataTransfer();
            singleDt.items.add(files[i]);
            newInput.files = singleDt.files;

            previewFileInSlot(files[i], newContainer);
        }

        fileCounter++;
        createNewUpload(fileCounter);
    }

    if (event.target.classList.contains("file-input-miniature")) {
        handleImagePreview(event.target);
    }

    if (event.target.classList.contains("file-input-avatar")) {
        handleImagePreview(event.target);
    }
});

const form = document.querySelector("form");

if (form) {
    form.addEventListener("submit", () => {
        const mainInput = document.querySelector("#file-upload");
        const allFileInputs = document.querySelectorAll(
            "#upload-pages .file-input",
        );

        const dt = new DataTransfer();

        allFileInputs.forEach((input) => {
            if (input.files.length > 0) {
                Array.from(input.files).forEach((file) => {
                    dt.items.add(file);
                });
            }
        });

        if (mainInput) {
            mainInput.files = dt.files;
        }
    });
}

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

function createNewUpload(index) {
    const container = document.createElement("div");
    container.classList.add("new-project-middle");

    const input = document.createElement("input");
    input.type = "file";
    input.id = `file-upload-new-${index}`;
    input.name = "pages[]";
    input.accept = "image/png, image/jpeg";
    input.multiple = true;
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

    return container;
}
