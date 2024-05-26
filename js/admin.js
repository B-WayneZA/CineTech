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




// Added code to add functionality using the api



// Function to handle form submission for deleting a movie or series
document.getElementById('deleteForm').addEventListener('submit', function(event) {
  event.preventDefault();
  
  const formData = new FormData(this);
  const title = formData.get('title');
  const type = formData.get('type');

  fetch('https://your-api-url.com/delete', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      type: type,
      title: title,
    }),
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from the server
    console.log(data);
    // Optionally, display a success or error message to the user
  })
  .catch(error => {
    console.error('Error:', error);
    // Optionally, display an error message to the user
  });
});

// Function to handle form submission for adding a movie or series
document.getElementById('addForm').addEventListener('submit', function(event) {
  event.preventDefault();
  
  const formData = new FormData(this);
  const title = formData.get('title');
  const description = formData.get('description');
  const rating = formData.get('rating');
  const genres = formData.get('genres');
  const yearReleased = formData.get('yearReleased');
  const type = formData.get('type');

  fetch('https://your-api-url.com/add', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      type: type,
      title: title,
      description: description,
      rating: rating,
      genres: genres,
      yearReleased: yearReleased,
    }),
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from the server
    console.log(data);
    // Optionally, display a success or error message to the user
  })
  .catch(error => {
    console.error('Error:', error);
    // Optionally, display an error message to the user
  });
});

// Function to handle form submission for editing a movie or series
document.getElementById('editForm').addEventListener('submit', function(event) {
  event.preventDefault();
  
  const formData = new FormData(this);
  const title = formData.get('title');
  const editField = formData.get('editField');
  const editValue = formData.get('editValue');
  const type = formData.get('type');

  fetch('https://your-api-url.com/edit', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      type: type,
      title: title,
      editField: editField,
      editValue: editValue,
    }),
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from the server
    console.log(data);
    // Optionally, display a success or error message to the user
  })
  .catch(error => {
    console.error('Error:', error);
    // Optionally, display an error message to the user
  });
});

// Function to handle form submission for deleting a user
document.getElementById('deleteUserForm').addEventListener('submit', function(event) {
  event.preventDefault();
  
  const formData = new FormData(this);
  const email = formData.get('email');

  fetch('https://your-api-url.com/deleteUser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
    }),
  })
  .then(response => response.json())
  .then(data => {
    // Handle response from the server
    console.log(data);
    // Optionally, display a success or error message to the user
  })
  .catch(error => {
    console.error('Error:', error);
    // Optionally, display an error message to the user
  });
});
