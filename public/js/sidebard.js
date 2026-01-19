// Load sidebar.html dynamically
document.addEventListener("DOMContentLoaded", function() {
  fetch("sidebar.html")
    .then(response => response.text())
    .then(data => {
      document.getElementById("sidebar-container").innerHTML = data;

      // Highlight active link
      let currentPage = window.location.pathname.split("/").pop();
      let menuLinks = document.querySelectorAll(".sidebar .menu a");

      menuLinks.forEach(link => {
        if (link.getAttribute("href") === currentPage) {
          link.parentElement.classList.add("active");
        }
      });
    });
});
