document.addEventListener("DOMContentLoaded", () => {
    const animElements = document.querySelectorAll(".animate");
    
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    animElements.forEach(el => observer.observe(el));
});
