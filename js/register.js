document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(form);

        if (validateForm(formData)) {
            registerUser(formData, (success) => {
                if (success) {
                    alert('Registration successful!');
                    window.location.href = '/html/login.html'; // Redirection to the login page
                } else {
                    alert('Registration failed. Please try again.');
                }
            });
        }
    });

    function validateForm(formData) {
        const email = formData.get('email');
        const password = formData.get('password');

        if (!validateEmail(email)) {
            alert('Invalid email format');
            return false;
        }

        if (!validatePassword(password)) {
            alert('Password must be at least 8 characters long, including an uppercase letter, a lowercase letter, and a number');
            return false;
        }

        return true;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    function validatePassword(password) {
        const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
        return re.test(password);
    }

    function registerUser(formData, callback) {
        const url = 'https://cinetechwatch.000webhostapp.com/php/api.php';
        const requestBody = {
            type: 'Register',
            name: formData.get('name'),
            surname: formData.get('surname'),
            email: formData.get('email'),
            password: formData.get('password'),
            username: formData.get('username'),
            admin: "true"
        };

        const xhr = new XMLHttpRequest();

        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                const result = JSON.parse(xhr.responseText);
                console.log('Response:', result);
                if (result.status === 'success') {
                    callback(true);
                } else {
                    callback(false);
                }
            } else {
                console.error('Request failed with status:', xhr.status);
                callback(false);
            }
        };

        xhr.onerror = function () {
            console.error('Request failed');
            callback(false);
        };

        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        console.log('Sending request:', JSON.stringify(requestBody));
        xhr.send(JSON.stringify(requestBody)); // Send JSON string
    }
});
