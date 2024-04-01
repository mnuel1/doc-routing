// Define the data to be sent in the request
const data = {
  route: 'document/release', // refer to the route.php and authRoute.php
  data: 'data',
  data1: 1,
};

// endpoint file
// either route.php for general functions or authRoute for authentication function
// your port ewan ko kung anong port gamit niyo sa akin 3000
// makikita yan pag ni run yung html

const url = 'http://localhost:<YOURPORT>/<endpointfile>.php';

// requesting is depende sa inyo, this is just an example
// use whatever you want bossings

// Define the options for the fetch request
const options = {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json' // Set the content type to JSON
  },
  body: JSON.stringify(data) // Convert data to JSON string
};

// Send the fetch request
fetch(url, options)
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json(); // Parse JSON response
  })
  .then(data => {
    // either show it in pop up or alert() pag tinamad na kayo no probs
    console.log(data); // Output response data
  })
  .catch(error => {
    // either show it in pop up or alert() pag tinamad na kayo no probs
    console.error('There was a problem with the fetch operation:', error);
  });
