class MangaReader {
    constructor(direction) {
        this.pages = document.querySelectorAll(".page-slide");
        this.current = 0;
        this.direction = direction;
        this.total = this.pages.length;

        document
            .querySelector("#next-btn")
            .addEventListener("click", () => this.next());

        document
            .querySelector("#prev-btn")
            .addEventListener("click", () => this.prev());
    }
    next() {
        if (this.direction === "JP") {
            this.goTo(this.current - 1);
        } else {
            this.goTo(this.current + 1);
        }
    }

    prev() {
        if (this.direction === "JP") {
            this.goTo(this.current + 1);
        } else {
            this.goTo(this.current - 1);
        }
    }

    goTo(index) {
        if (index < 0 || index >= this.total) return;
        this.pages[this.current].classList.remove("active");
        this.current = index;
        this.pages[this.current].classList.add("active");
        document.querySelector("#page-counter").textContent =
            `${this.current + 1} / ${this.total}`;
    }
}

const reader = new MangaReader("{{manga.readingDirection}}");
