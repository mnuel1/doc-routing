/*  <!-- SPACE FOR EXPERIMENTS -->*/
// toast.js

// toast.js

async function loadToastHTML() {
    const response = await fetch('../Pages/toast1.htm');
    if (response.ok) {
      const html = await response.text();
      return html;
    } else {
      console.error('Failed to load toast.html:', response.statusText);
      return '';
    }
  }
  
  function showToast() {
    loadToastHTML().then(html => {
      if (html) {
        // Create a temporary container to hold the HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html.trim();
  
        // Find the toast element
        const toast = tempDiv.querySelector('.toast');
  
        // Append the toast to the toast container
        document.getElementById('toast-container').appendChild(toast);
  
        // Initialize and show the toast using Bootstrap's toast method
        $(toast).toast({ delay: 3000 });
        $(toast).toast('show');
  
        // Remove toast from the DOM after it hides
        $(toast).on('hidden.bs.toast', function () {
          toast.remove();
        });
      }
    });
  }
  
  // Initialize toast container on page load
  document.addEventListener('DOMContentLoaded', () => {
    const toastContainer = document.createElement('div');
    toastContainer.id = 'toast-container';
    toastContainer.style.position = 'fixed';
    toastContainer.style.bottom = '20px';  // Positioned at the bottom
    toastContainer.style.right = '20px';
    toastContainer.style.zIndex = '1000';
    document.body.appendChild(toastContainer);
  });
  
  