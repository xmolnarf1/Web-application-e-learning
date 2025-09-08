// Enhanced login validation with checkmark/exclamation indicators
document.getElementById("myForm").addEventListener("submit", function (event) {
    let valid = true;
    let emailField = document.getElementById("email");
    let passwordField = document.getElementById("password");
    let emailError = document.getElementById("emailError");
    let passwordError = document.getElementById("passwordError");

    // Reset states
    emailError.textContent = "";
    passwordError.textContent = "";
    emailField.classList.remove("invalid-input", "valid-input");
    passwordField.classList.remove("invalid-input", "valid-input");

    // Email validation
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailField.value.trim())) {
        emailError.textContent = "E-mail v zlom formáte.";
        emailField.classList.add("invalid-input");
        valid = false;
    } else {
        emailField.classList.add("valid-input");
    }

    // Password validation
    if (passwordField.value.trim().length < 4) {
        passwordError.textContent = "Heslo musí mať minimálne 4 znaky.";
        passwordField.classList.add("invalid-input");
        valid = false;
    } else {
        passwordField.classList.add("valid-input");
    }

    if (!valid) {
        event.preventDefault();
    }
});

// Real-time field validation
function setupFieldValidation(fieldID, errorID, validatorFunction, errorMessage) {
    const field = document.getElementById(fieldID);
    const error = document.getElementById(errorID);

    field.addEventListener("input", function() {
        validateField(field, error, validatorFunction, errorMessage);
    });

    field.addEventListener("blur", function() {
        validateField(field, error, validatorFunction, errorMessage);
    });
}

// Generic validation function
function validateField(field, errorElement, validatorFunction, errorMessage) {
    errorElement.textContent = "";
    field.classList.remove("invalid-input", "valid-input");

    if (!validatorFunction(field.value.trim())) {
        errorElement.textContent = errorMessage;
        field.classList.add("invalid-input");
        return false;
    } else {
        field.classList.add("valid-input");
        return true;
    }
}

// Initialize field validations
setupFieldValidation("email", "emailError",
    (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
    "E-mail v zlom formáte.");

setupFieldValidation("password", "passwordError",
    (value) => value.length >= 4,
    "Heslo musí mať minimálne 4 znaky.");
