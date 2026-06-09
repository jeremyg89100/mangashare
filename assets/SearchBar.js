class SearchBar {
    constructor(inputId, dropdownId) {
        this.input = document.querySelector(inputId);
        this.dropdown = document.querySelector(dropdownId);
        this.timeout = null;

        if (!this.input || !this.dropdown) return;

        this.init();
    }

    init() {
        this.input.addEventListener("input", (e) => this.handleInput(e));

        document.addEventListener("click", (e) => {
            if (
                !this.input.contains(e.target) &&
                !this.dropdown.contains(e.target)
            )
                this.close();
        });
    }

    handleInput(e) {
        clearTimeout(this.timeout);
        const query = e.target.value.trim();

        if (query.length < 2) {
            this.close();
            return;
        }

        this.timeout = setTimeout(() => this.search(query), 300);
    }

    async search(query) {
        try {
            const response = await fetch(
                `/search?q=${encodeURIComponent(query)}`,
            );
            const data = await response.json();
            this.render(data);
        } catch (error) {
            console.error("Erreur de recherche : ", error);
        }
    }

    render(data) {
        const { mangas, articles, users } = data;
        const total = mangas.length + articles.length + users.length;

        this.dropdown.innerHTML = "";
        this.dropdown.classList.remove("search-empty");

        if (total === 0) {
            this.dropdown.textContent = "Aucun résultat";
            this.dropdown.classList.add("search-empty");
            this.open();
            return;
        }

        if (mangas.length > 0) {
            const label = document.createElement("div");
            label.classList.add("search-section-label");
            label.textContent = "Mangas :";
            this.dropdown.appendChild(label);

            mangas.forEach((manga) => {
                const redirect = document.createElement("a");
                redirect.href = `/info/manga/${manga.id}`;
                redirect.className = "search-item";

                const searchTitle = document.createElement("span");
                searchTitle.className = "search-item-title";
                searchTitle.textContent = manga.title;

                redirect.appendChild(searchTitle);
                this.dropdown.appendChild(redirect);
            });
        }

        if (users.length > 0) {
            const label = document.createElement("div");
            label.classList.add("search-section-label");
            label.textContent = "Auteurs :";
            this.dropdown.appendChild(label);

            users.forEach((user) => {
                const redirect = document.createElement("a");
                redirect.href = `/user/${user.id}/info`;
                redirect.className = "search-item";

                const searchTitle = document.createElement("span");
                searchTitle.className = "search-item-title";
                searchTitle.textContent = user.pseudo;

                redirect.appendChild(searchTitle);
                this.dropdown.appendChild(redirect);
            });
        }

        if (articles.length > 0) {
            const label = document.createElement("div");
            label.classList.add("search-section-label");
            label.textContent = "Articles :";
            this.dropdown.appendChild(label);

            articles.forEach((article) => {
                const redirect = document.createElement("a");
                redirect.href = `/read/article/${article.id}`;
                redirect.className = "search-item";

                const searchTitle = document.createElement("span");
                searchTitle.className = "search-item-title";
                searchTitle.textContent = article.title;

                redirect.appendChild(searchTitle);
                this.dropdown.appendChild(redirect);
            });
        }

        this.open();
    }

    open() {
        this.dropdown.classList.remove("close");
        this.dropdown.classList.add("open");
    }

    close() {
        this.dropdown.classList.remove("open");
        this.dropdown.classList.add("close");
    }
}

function bootSearch() {
    const input = document.querySelector(".nav-mobile-search");
    const dropdown = document.querySelector(".search-dropdown");

    if (input && dropdown && !input.searchBarInitialized) {
        new SearchBar(".nav-mobile-search", ".search-dropdown");
        input.searchBarInitialized = true;
    }
}

document.addEventListener("turbo:load", () => {
    bootSearch();
});

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootSearch);
} else {
    bootSearch();
}
