// Get the user image and notifications icon
const userIcon = document.querySelector('.search_user img');
const notificationsIcon = document.querySelector('.notifications img');

// Get the user popup and notifications popup
const userPopup = document.querySelector('.search_user .search');
const notificationsPopup = document.querySelector('.notifications-popup');

// Function to toggle the visibility of the user popup
userIcon.addEventListener('click', function() {
    userPopup.classList.toggle('show');
});

// Function to toggle the visibility of the notifications popup
notificationsIcon.addEventListener('click', function() {
    notificationsPopup.classList.toggle('show');
});

// Close the popups if user clicks outside of them
window.addEventListener('click', function(event) {
    if (!event.target.matches('.search_user img')) {
        userPopup.classList.remove('show');
    }
    if (!event.target.matches('.notifications img')) {
        notificationsPopup.classList.remove('show');
    }
});

// Get the user image and the user panel
const userImage = document.querySelector('.search_user img');
const userPanel = document.querySelector('.user-panel');

// Show the user panel when the user image is clicked
userImage.addEventListener('click', function() {
    userPanel.classList.toggle('show');
});

// Hide the user panel when clicking outside of it
window.addEventListener('click', function(event) {
    if (!event.target.matches('.search_user img') && !event.target.closest('.user-panel')) {
        userPanel.classList.remove('show');
    }
});

// Handle logout button click
document.getElementById('logout-btn').addEventListener('click', function() {
    // Add logout functionality here
});

// Handle delete account button click
document.getElementById('delete-btn').addEventListener('click', function() {
    // Add delete account functionality here
});

// Get references to elements
const changePasswordBtn = document.getElementById('change-password-btn');
const changeUsernameBtn = document.getElementById('change-username-btn');

// Get the password popup and username popup
const passwordPopup = document.querySelector('.password-popup');
const usernamePopup = document.querySelector('.username-popup');

// Event listener for change password button
changePasswordBtn.addEventListener('click', function(event) {
    event.stopPropagation(); // Prevent the click event from bubbling up to the window
    passwordPopup.classList.toggle('show');
});

// Event listener for change username button
changeUsernameBtn.addEventListener('click', function(event) {
    event.stopPropagation(); // Prevent the click event from bubbling up to the window
    usernamePopup.classList.toggle('show');
});

// Event listener for mode switch
modeSwitch.addEventListener('change', function() {
    // Code to toggle between light and dark mode
    if (this.checked) {
        // Dark mode
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode'); // Remove light mode
    } else {
        // Light mode
        document.body.classList.remove('dark-mode');
        document.body.classList.add('light-mode'); // Add light mode
    }
});
