const followBtn = document.querySelector(".follow-btn");

if (followBtn) {
    followBtn.addEventListener("click", async function (e) {
        e.preventDefault();

        const targetUrl = this.dataset.url;

        try {
            const response = await fetch(targetUrl, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": followBtn.dataset.csrf,
                },
            });

            if (!response.ok) {
                throw new Error(`Erreur serveur : ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                const pseudo = this.dataset.pseudo;

                if (data.isFollowing) {
                    followBtn.textContent = `Ne plus suivre`;
                } else {
                    followBtn.textContent = `Suivre ${pseudo}`;
                }
            } else if (data.error) {
                alert(data.error);
            }
        } catch (error) {
            console.error("Fetch error : ", error);
        }
    });
}
