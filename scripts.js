// scripts.js

document.addEventListener('DOMContentLoaded', () => {
    // Анимация формы при загрузке
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.style.opacity = 0;
        setTimeout(() => {
            form.style.transition = "opacity 1s";
            form.style.opacity = 1;
        }, 100);
    });
});
