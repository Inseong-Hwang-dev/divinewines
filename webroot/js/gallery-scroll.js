document.addEventListener("DOMContentLoaded", () => {
    const container = document.querySelector(".gallery-container");
    const gallery = document.querySelector(".gallery");
    const leftArrow = document.querySelector(".arrow-nav.left");
    const rightArrow = document.querySelector(".arrow-nav.right");

    if (!container || !gallery || !leftArrow || !rightArrow) {
        console.error("Required gallery elements not found");
        return;
    }

    const updateArrows = () => {
        const maxScrollLeft = container.scrollWidth - container.clientWidth;
        const scrollLeft = container.scrollLeft;

        // Update arrow states
        leftArrow.style.opacity = scrollLeft <= 0 ? "0.3" : "1";
        leftArrow.style.pointerEvents = scrollLeft <= 0 ? "none" : "auto";
        rightArrow.style.opacity = scrollLeft >= maxScrollLeft - 1 ? "0.3" : "1"; // -1 for rounding errors
        rightArrow.style.pointerEvents = scrollLeft >= maxScrollLeft - 1 ? "none" : "auto";
    };

    const getVisibleCards = () => {
        const cards = Array.from(gallery.querySelectorAll(".product-card"));
        const containerRect = container.getBoundingClientRect();
        return cards.filter(card => {
            const cardRect = card.getBoundingClientRect();
            return cardRect.left >= containerRect.left && cardRect.right <= containerRect.right;
        });
    };

    const getNextCardPosition = (direction) => {
        const cards = Array.from(gallery.querySelectorAll(".product-card"));
        const containerRect = container.getBoundingClientRect();
        const containerCenter = containerRect.left + containerRect.width / 2;
        
        let targetCard;
        if (direction === 'right') {
            targetCard = cards.find(card => {
                const cardRect = card.getBoundingClientRect();
                return cardRect.left > containerCenter;
            });
        } else {
            targetCard = [...cards].reverse().find(card => {
                const cardRect = card.getBoundingClientRect();
                return cardRect.right < containerCenter;
            });
        }

        if (!targetCard) return null;

        const cardRect = targetCard.getBoundingClientRect();
        const cardCenter = cardRect.left + cardRect.width / 2;
        const scrollAmount = cardCenter - containerCenter;
        
        return scrollAmount;
    };

    const scrollToNextCard = (direction) => {
        const scrollAmount = getNextCardPosition(direction);
        if (scrollAmount !== null) {
            container.scrollBy({ left: scrollAmount, behavior: "smooth" });
            setTimeout(updateArrows, 300);
        }
    };

    leftArrow.addEventListener("click", () => {
        scrollToNextCard('left');
        // Reset auto-scroll timer when user manually interacts
        resetAutoScroll();
    });

    rightArrow.addEventListener("click", () => {
        scrollToNextCard('right');
        // Reset auto-scroll timer when user manually interacts
        resetAutoScroll();
    });

    // Auto-scroll functionality
    let autoScrollInterval;
    const startAutoScroll = () => {
        autoScrollInterval = setInterval(() => {
            const maxScrollLeft = container.scrollWidth - container.clientWidth;
            const scrollLeft = container.scrollLeft;

            if (scrollLeft >= maxScrollLeft - 1) {
                // If we've reached the end, scroll back to the beginning
                container.scrollTo({ left: 0, behavior: "smooth" });
            } else {
                scrollToNextCard('right');
            }
        }, 8000); // 8 seconds interval
    };

    const resetAutoScroll = () => {
        clearInterval(autoScrollInterval);
        startAutoScroll();
    };

    // Start auto-scrolling when the page loads
    startAutoScroll();

    // Pause auto-scrolling when user hovers over the gallery
    container.addEventListener("mouseenter", () => {
        clearInterval(autoScrollInterval);
    });

    // Resume auto-scrolling when user leaves the gallery
    container.addEventListener("mouseleave", () => {
        startAutoScroll();
    });

    // Debounce scroll updates
    let scrollTimeout;
    container.addEventListener("scroll", () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(updateArrows, 100);
    });

    window.addEventListener("resize", updateArrows);
    updateArrows(); // Initial call
});
