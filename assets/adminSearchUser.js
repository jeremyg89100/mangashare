document.addEventListener("turbo:load", () => {
    const searchInput = document.getElementById("admin-user-search");

    if (!searchInput) return;

    searchInput.addEventListener("input", (e) => {
        const query = e.target.value.toLowerCase().trim();
        const userRows = document.querySelectorAll(".admin-user-action");

        userRows.forEach((row) => {
            const pseudoText = row
                .querySelector(".col-pseudo")
                .textContent.toLowerCase();

            if (pseudoText.includes(query)) {
                row.classList.remove("hidden");
            } else {
                row.classList.add("hidden");
            }
        });
    });
});
