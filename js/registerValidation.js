document.getElementById("myForm").addEventListener("submit", function (event) {
    let valid = true;

    let firstname = document.getElementById("firstname");
    let lastname = document.getElementById("lastname");
    let email = document.getElementById("email");
    let password = document.getElementById("password");
    let passwordRepeate = document.getElementById("passwordRepeate");

    valid = validateField("firstname", "nameError",
        (value) => {
            const trimmed = value.trim();
            return /^[A-Za-zÁÉÍÓÚÝŔŮĽŠČŤŽŇÄÔĎŤÚÝŽáéíóúýŕůľščťžňäôďťúýž\s\-]+$/.test(trimmed)
                && trimmed.length >= 2
                && trimmed.length <= 20;
        },
        messages.firstname_invalid
    ) && valid;

    valid = validateField("lastname", "surnameError",
        (value) => {
            const trimmed = value.trim();
            return /^[A-Za-zÁÉÍÓÚÝŔŮĽŠČŤŽŇÄÔĎŤÚÝŽáéíóúýŕůľščťžňäôďťúýž\s\-]+$/.test(trimmed)
                && trimmed.length >= 2
                && trimmed.length <= 20;
        },
        messages.lastname_invalid
    ) && valid;

    valid = validateField("email", "emailError",
        (value) => /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(value),
        messages.email_invalid) && valid;

    valid = validateField("password", "passwordError",
        (value) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/.test(value),
        messages.password_invalid) && valid;

    valid = validateField("passwordRepeate", "passwordRepeateError",
        (value) => value === document.getElementById("password").value.trim(),
        messages.password_mismatch) && valid;

    if (!valid) {
        event.preventDefault();
    }
});

function validateField(fieldID, errorID, validatorFunction, errorMessage) {
    let field = document.getElementById(fieldID);
    let error = document.getElementById(errorID);
    let isValid = validatorFunction(field.value.trim());

    field.classList.remove("invalid-input", "valid-input");
    error.textContent = "";

    if (!isValid) {
        error.textContent = errorMessage;
        field.classList.add("invalid-input");
    } else {
        field.classList.add("valid-input");
    }

    return isValid;
}

function setupFieldValidation(fieldID, errorID, validatorFunction, errorMessage) {
    const field = document.getElementById(fieldID);

    field.addEventListener("input", function() {
        validateField(fieldID, errorID, validatorFunction, errorMessage);

        if (fieldID === "password" && document.getElementById("passwordRepeate").value) {
            validateField("passwordRepeate", "passwordRepeateError",
                (value) => value === document.getElementById("password").value.trim(),
                "Heslá sa nezhodujú.");
        }
    });

    field.addEventListener("blur", function() {
        validateField(fieldID, errorID, validatorFunction, errorMessage);
    });
}

setupFieldValidation("firstname", "nameError",
    (value) => {
        const trimmed = value.trim();
        return /^[A-Za-zÁÉÍÓÚÝŔŮĽŠČŤŽŇÄÔĎŤÚÝŽáéíóúýŕůľščťžňäôďťúýž\s\-]+$/.test(trimmed)
            && trimmed.length >= 2
            && trimmed.length <= 20;
    },
    messages.firstname_invalid);

setupFieldValidation("lastname", "surnameError",
    (value) => {
        const trimmed = value.trim();
        return /^[A-Za-zÁÉÍÓÚÝŔŮĽŠČŤŽŇÄÔĎŤÚÝŽáéíóúýŕůľščťžňäôďťúýž\s\-]+$/.test(trimmed)
            && trimmed.length >= 2
            && trimmed.length <= 50;
    },
    messages.lastname_invalid);

setupFieldValidation("email", "emailError",
    (value) => /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(value),
    messages.email_invalid);

setupFieldValidation("password", "passwordError",
    (value) => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/.test(value),
    messages.password_invalid);

setupFieldValidation("passwordRepeate", "passwordRepeateError",
    (value) => value === document.getElementById("password").value.trim(),
    messages.password_mismatch);