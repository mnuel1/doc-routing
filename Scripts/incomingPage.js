//same thing for filter button/active button
document.addEventListener("DOMContentLoaded", function() {
  console.log("JavaScript loaded and running");

  const filterButtons = document.querySelectorAll('.btn-group .btn');
  console.log("Filter buttons:", filterButtons);

  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      filterButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      console.log("Active button:", this);
    });
  });
});
