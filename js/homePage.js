document.addEventListener('DOMContentLoaded', () => {
    // Get the user image and notifications icon
    const userIcon = document.querySelector('.search_user img');
    const notificationsIcon = document.querySelector('.notifications img');

    // Get the user panel, user popup, and notifications popup
    const userPanel = document.querySelector('.user-panel');
    const notificationsPopup = document.querySelector('.notifications-popup');

    // Function to toggle the visibility of the user panel
    userIcon.addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from bubbling up to the window
        userPanel.classList.toggle('show');
    });

    // Function to toggle the visibility of the notifications popup
    notificationsIcon.addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from bubbling up to the window
        notificationsPopup.classList.toggle('show');
    });

    // Close the user panel and notifications popup if the user clicks outside of them
    window.addEventListener('click', function (event) {
        if (!event.target.closest('.search_user img') && !event.target.closest('.user-panel')) {
            userPanel.classList.remove('show');
        }
        if (!event.target.closest('.notifications img') && !event.target.closest('.notifications-popup')) {
            notificationsPopup.classList.remove('show');
        }
    });

    // Handle logout button click
    document.getElementById('logout-btn').addEventListener('click', function () {
        // Add logout functionality here
    });

    // Handle delete account button click
    document.getElementById('delete-btn').addEventListener('click', function () {
        // Add delete account functionality here
    });

    // Get references to elements
    const changePasswordBtn = document.getElementById('change-password-btn');
    const changeUsernameBtn = document.getElementById('change-username-btn');

    // Get the password popup and username popup
    const passwordPopup = document.querySelector('.password-popup');
    const usernamePopup = document.querySelector('.username-popup');

    // Event listener for change password button
    changePasswordBtn.addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from bubbling up to the window
        passwordPopup.classList.toggle('show');
    });

    // Event listener for change username button
    changeUsernameBtn.addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from bubbling up to the window
        usernamePopup.classList.toggle('show');
    });

    // Close the password popup when clicking outside
    window.addEventListener('click', function (event) {
        if (!event.target.closest('#change-password-btn') && !event.target.closest('.password-popup')) {
            passwordPopup.classList.remove('show');
        }
        if (!event.target.closest('#change-username-btn') && !event.target.closest('.username-popup')) {
            usernamePopup.classList.remove('show');
        }
    });

    // Handle save and cancel actions for popups
    document.getElementById('cancel-password').addEventListener('click', function () {
        passwordPopup.classList.remove('show');
    });

    document.getElementById('save-password').addEventListener('click', function () {
        // Save new password logic here
        passwordPopup.classList.remove('show');
    });

    document.getElementById('cancel-username').addEventListener('click', function () {
        usernamePopup.classList.remove('show');
    });

    document.getElementById('save-username').addEventListener('click', function () {
        // Save new username logic here
        usernamePopup.classList.remove('show');
    });

    // Event listener for mode switch
    const modeSwitch = document.getElementById('mode-switch');
    modeSwitch.addEventListener('change', function () {
        if (this.checked) {
            document.body.classList.add('dark-mode');
            document.body.classList.remove('light-mode');
        } else {
            document.body.classList.remove('dark-mode');
            document.body.classList.add('light-mode');
        }
    });
    // section of code to fetch info for the search bar in the html format so that when a  user inputs a search 
    // they see the details of the movie 
    // Example event listener for search button
    document.getElementById('search_button').addEventListener('click', function () {
        // Get the search input value
        const searchTerm = document.getElementById('search_input').value;

        // Send a request to the API to search for movies or shows
        fetch(`https://your-api-url.com/search?search=${searchTerm}`)
            .then(response => response.json())
            .then(data => {
                // Handle the response data
                console.log(data);
            })
            .catch(error => {
                // Handle errors
                console.error('Error:', error);
            });
    });

    // display the api data 
    // Example of updating HTML with API data
    // this must fetch the results from the api and display it 
    const searchResultsContainer = document.getElementById('search_results');

    // Assuming data is an array of search results from the API
    data.forEach(result => {
        const resultItem = document.createElement('div');
        resultItem.textContent = result.title;
        searchResultsContainer.appendChild(resultItem);
    });

    // section of code to get notifications of any movies or series the user recieved from another user that was shared to them

    // section of the code that logsout the user from the homepage and redirects them back to the login page

    // section of the code that deletes the user from the database fully, it must search for their email, exactly and deletes them

    // section of code for getting the shared movie from api, this is the shared movie 
    // Example: Send a request to the API to get shared movies
    fetch('https://your-api-url.com/getShared?apikey=your-api-key')
        .then(response => response.json())
        .then(data => {
            // Handle the response data
            console.log(data);
        })
        .catch(error => {
            // Handle errors
            console.error('Error:', error);
        });

});
