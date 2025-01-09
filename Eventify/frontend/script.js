document.addEventListener("DOMContentLoaded", function() {
    const roles = document.querySelectorAll(".role-card a");
    roles.forEach(role => {
        role.addEventListener("click", function(event) {
            event.preventDefault();
            window.location.href = role.getAttribute("href");
        });
    });
});
