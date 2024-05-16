//event listeners to filter buttons, sa filter lang 'to if ano ang active button
document.getElementById('viewAllBtn').addEventListener('click', function(event) {
  event.preventDefault();
  toggleFilterButton(this);
});

document.getElementById('approveBtn').addEventListener('click', function(event) {
  event.preventDefault();
  toggleFilterButton(this);
});

document.getElementById('rejectBtn').addEventListener('click', function(event) {
  event.preventDefault();
  toggleFilterButton(this);
});

document.getElementById('authenticateBtn').addEventListener('click', function(event) {
  event.preventDefault();
  toggleFilterButton(this);
});

// Function to toggle selected filter button
function toggleFilterButton(button) {
  // Remove 'selected-filter' class from all filter buttons
  document.querySelectorAll('.filter-button').forEach(function(btn) {
      btn.classList.remove('selected-filter');
  });

  // Add 'selected-filter' class to the clicked button
  button.classList.add('selected-filter');
}
