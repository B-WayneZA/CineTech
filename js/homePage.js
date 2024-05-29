document.addEventListener('DOMContentLoaded', () => {
    // Get the user image and notifications icon
    const userIcon = document.querySelector('.search_user img');
    const notificationsIcon = document.querySelector('.notifications img');

    // Get the user panel, user popup, and notifications popup
    const userPanel = document.querySelector('.user-panel');
    const notificationsPopup = document.querySelector('.notifications-popup');

       // Check if the elements exist before adding event listeners
       if (userIcon && notificationsIcon) {
        // Rest of your code for adding event listeners
         // Function to toggle the visibility of the user panel
    userIcon.addEventListener('click', function (event) {
        event.stopPropagation(); // Prevent the click event from bubbling up to the window
        userPanel.classList.toggle('show');
    });
    } else {
        console.error('One or more elements not found.');
    }

   

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


      // Function to handle image click event
      function handleImageClick(title) {
        // Redirect to viewMore page with the movie/series title as a query parameter
        window.location.href = "https://cinetechwatch.000webhostapp.com/html/viewMore.php";
    }

     // Event listener for all <a> tags with href="#"
     const clickableLinks = document.querySelectorAll('a[href="#"]');
     clickableLinks.forEach(link => {
         link.addEventListener('click', function(event) {
             event.preventDefault(); // Prevent default behavior of the link
             const cardTitle = document.querySelector('h4').innerText; // Get the title from the closest card's <h4> element
             handleImageClick(cardTitle); // Call the handleImageClick function with the title
         });
     });

    });

