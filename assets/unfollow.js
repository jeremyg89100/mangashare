document.querySelectorAll(".unfollow-btn").forEach((btn) => {
    btn.addEventListener("click", async function (e) {
        e.preventDefault();

        const followCard = this.closest(".following-card");
        const targetUrl = this.dataset.url;

        try {
            const response = await fetch(targetUrl, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": this.dataset.csrf,
                },
            });

            if (!response.ok) {
                throw new Error(`Erreur serveur : ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                if (followCard) {
                    followCard.remove();
                }
            } else if (data.error) {
                alert(data.error);
            }
        } catch (error) {
            console.error("Fetch error : ", error);
        }
    });
});
