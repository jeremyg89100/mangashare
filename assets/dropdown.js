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

const bellBtn = document.querySelector("#bell-btn");
const notificationsDropdown = document.querySelector("#notifications-dropdown");

if (bellBtn && notificationsDropdown) {
    bellBtn.addEventListener("click", async (e) => {
        e.stopPropagation();
        notificationsDropdown.classList.toggle("open");

        if (notificationsDropdown.classList.contains("open")) {
            await fetch("/notifications/mark-read", { method: "POST" });
            const badge = document.querySelector(".nav-bell-badge");
            if (badge) badge.remove();
        }
    });
}
