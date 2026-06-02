const likeBtn = document.querySelector(".like-btn");

if (likeBtn) {
    likeBtn.addEventListener("click", async function (e) {
        e.preventDefault();
        const targetUrl = this.dataset.url;

        try {
            const response = await fetch(targetUrl, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": likeBtn.dataset.csrf,
                },
            });

            if (!response.ok) {
                throw new Error(`Erreur serveur : {response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                const mangaLikes = document.querySelector(".manga-likes");
                if (mangaLikes) {
                    mangaLikes.textContent = data.newLikeCount;
                }

                if (data.isLiked) {
                    likeBtn.textContent = "Je n'aime plus";
                } else {
                    likeBtn.textContent = "J'aime";
                }
            } else if (data.error) {
                alert(data.error);
            }
        } catch (error) {
            console.error("Fetch error : ", error);
        }
    });
}
