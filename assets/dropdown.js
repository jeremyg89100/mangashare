document.addEventListener("turbo:load", function () {
    const avatar = document.getElementById("avatar-btn");
    const dropdown = document.getElementById("user-dropdown");

    if (avatar && dropdown) {
        avatar.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle("open");
        });

        dropdown.addEventListener("click", (e) => {
            e.stopPropagation();
        });

        document.addEventListener("click", (e) => {
            if (!avatar.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove("open");
            }
        });
    }
});
