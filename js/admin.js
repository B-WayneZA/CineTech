// SIDEBAR TOGGLE

let sidebarOpen = false;
const sidebar = document.getElementById('sidebar');

function openSidebar() {
  if (!sidebarOpen) {
    sidebar.classList.add('sidebar-responsive');
    sidebarOpen = true;
  }
}

function closeSidebar() {
  if (sidebarOpen) {
    sidebar.classList.remove('sidebar-responsive');
    sidebarOpen = false;
  }
}

// ---------- CHARTS ----------

// BAR CHART
const barChartOptions = {
  series: [
    {
      data: [10, 8, 6, 4, 2],
      name: 'Movie/Series',
    },
  ],
  chart: {
    type: 'bar',
    background: 'transparent',
    height: 350,
    toolbar: {
      show: false,
    },
  },
  colors: ['#9021ac', '#cf6016', '#1c6166', '#c52a84', '#583cb3'],
  plotOptions: {
    bar: {
      distributed: true,
      borderRadius: 4,
      horizontal: false,
      columnWidth: '40%',
    },
  },
  dataLabels: {
    enabled: false,
  },
  fill: {
    opacity: 1,
  },
  grid: {
    borderColor: '#55596e',
    yaxis: {
      lines: {
        show: true,
      },
    },
    xaxis: {
      lines: {
        show: true,
      },
    },
  },
  legend: {
    labels: {
      colors: '#f5f7ff',
    },
    show: true,
    position: 'top',
  },
  stroke: {
    colors: ['transparent'],
    show: true,
    width: 2,
  },
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark',
  },
  xaxis: {
    categories: ['Action', 'Animation', 'Drama', 'Romance', 'Anime'],
    title: {
      style: {
        color: '#f5f7ff',
      },
    },
    axisBorder: {
      show: true,
      color: '#55596e',
    },
    axisTicks: {
      show: true,
      color: '#55596e',
    },
    labels: {
      style: {
        colors: '#f5f7ff',
      },
    },
  },
  yaxis: {
    title: {
      text: 'Count',
      style: {
        color: '#f5f7ff',
      },
    },
    axisBorder: {
      color: '#55596e',
      show: true,
    },
    axisTicks: {
      color: '#55596e',
      show: true,
    },
    labels: {
      style: {
        colors: '#f5f7ff',
      },
    },
  },
};

const barChart = new ApexCharts(
  document.querySelector('#bar-chart'),
  barChartOptions
);
barChart.render();

// AREA CHART
const areaChartOptions = {
  series: [
    {
      name: 'Movies',
      data: [31, 40, 28, 51, 42, 109, 100],
    },
    {
      name: 'Series',
      data: [11, 32, 45, 32, 34, 52, 41],
    },
  ],
  chart: {
    type: 'area',
    background: 'transparent',
    height: 350,
    stacked: false,
    toolbar: {
      show: false,
    },
  },
  colors: ['#cf6016', '#312697'],
  labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
  dataLabels: {
    enabled: false,
  },
  fill: {
    gradient: {
      opacityFrom: 0.4,
      opacityTo: 0.1,
      shadeIntensity: 1,
      stops: [0, 100],
      type: 'vertical',
    },
    type: 'gradient',
  },
  grid: {
    borderColor: '#55596e',
    yaxis: {
      lines: {
        show: true,
      },
    },
    xaxis: {
      lines: {
        show: true,
      },
    },
  },
  legend: {
    labels: {
      colors: '#f5f7ff',
    },
    show: true,
    position: 'top',
  },
  markers: {
    size: 6,
    strokeColors: '#1b2635',
    strokeWidth: 3,
  },
  stroke: {
    curve: 'smooth',
  },
  xaxis: {
    axisBorder: {
      color: '#55596e',
      show: true,
    },
    axisTicks: {
      color: '#55596e',
      show: true,
    },
    labels: {
      offsetY: 5,
      style: {
        colors: '#f5f7ff',
      },
    },
  },
  yaxis: [
    {
      title: {
        text: 'Movies',
        style: {
          color: '#f5f7ff',
        },
      },
      labels: {
        style: {
          colors: ['#f5f7ff'],
        },
      },
    },
    {
      opposite: true,
      title: {
        text: 'Series',
        style: {
          color: '#f5f7ff',
        },
      },
      labels: {
        style: {
          colors: ['#f5f7ff'],
        },
      },
    },
  ],
  tooltip: {
    shared: true,
    intersect: false,
    theme: 'dark',
  },
};

const areaChart = new ApexCharts(
  document.querySelector('#area-chart'),
  areaChartOptions
);
areaChart.render();




// -----------------  Added code to add functionality using the api --------------------------


// Delete Movie or Series Form Submission
document.getElementById('deleteForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent default form submission

  const title = document.getElementById('deleteTitle').value; // Get title input value
  const type = document.querySelector('input[name="deleteType"]:checked').value; // Get selected type (Movie/Series)

  // AJAX request to delete movie or series
  fetch('https://cinetechwatch.000webhostapp.com/php/api.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      type: 'Remove',
      title: title,
      item: type
    })
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from API
    if (data.success) {
      alert(data.message); // Show success message
    } else {
      alert(data.error); // Show error message
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.'); // Show generic error message
  });
});




// Delete User Form Submission
document.getElementById('deleteUserForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent default form submission

  const email = document.getElementById('deleteEmail').value; // Get email input value

  // AJAX request to delete user
  fetch('https://cinetechwatch.000webhostapp.com/php/api.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      type: 'DeleteUser',
      email: email
    })
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from API
    if (data.status === 'success') {
      alert(data.data); // Show success message
    } else {
      alert('An error occurred: ' + data.data); // Show error message
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.'); // Show generic error message
  });
});




// Add Movie or Series Form Submission
document.getElementById('addForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent default form submission

  // Get input values
  const title = document.getElementById('addTitle').value;
  const description = document.getElementById('addDescription').value;
  const rating = document.getElementById('addRating').value;
  const genres = document.getElementById('addGenres').value;
  const yearReleased = document.getElementById('addYearReleased').value;
  const type = document.querySelector('input[name="addType"]:checked').value; // Get selected type (Movie/Series)

  // AJAX request to add movie or series
  fetch('https://cinetechwatch.000webhostapp.com/php/api.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      type: (type === 'Movie') ? 'AddMovie' : 'AddSeries', // Determine whether it's a movie or series
      title: title,
      description: description,
      rating: rating,
      genres: genres,
      yearReleased: yearReleased,
      objectType: type
    })
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from API
    if (data.status === 'success') {
      alert(data.data); // Show success message
    } else {
      alert(data.error); // Show error message
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.'); // Show generic error message
  });
});




// Edit Movie or Series Form Submission
document.getElementById('editForm').addEventListener('submit', function(event) {
  event.preventDefault(); // Prevent default form submission

  // Get input values
  const title = document.getElementById('editTitle').value;
  const editField = document.getElementById('editField').value;
  const editValue = document.getElementById('editValue').value;
  const type = document.querySelector('input[name="editType"]:checked').value; // Get selected type (Movie/Series)

  // Prepare fields object
  const fields = {};
  fields[editField] = editValue;

  // AJAX request to edit movie or series
  fetch('https://cinetechwatch.000webhostapp.com/php/api.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      type: type === 'movie' ? 'EditMovie' : 'EditShow',
      title: title,
      fields: fields
    })
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from API
    if (data.status === 'success') {
      alert(data.data); // Show success message
    } else {
      alert('An error occurred: ' + data.data); // Show error message
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred. Please try again.'); // Show generic error message
  });
});
