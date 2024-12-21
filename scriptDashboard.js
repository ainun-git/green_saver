document.addEventListener("DOMContentLoaded", function () {
    const toggleSidebarButton = document.getElementById("toggleSidebar");
    const sidebar = document.getElementById("nav-bar");

    toggleSidebarButton.addEventListener("click", function () {
        sidebar.classList.toggle("hidden");
    });
});
