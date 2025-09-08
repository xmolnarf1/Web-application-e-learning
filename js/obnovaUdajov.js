document.getElementById("myForm").addEventListener("submit", function (event) {
    let valid = true;

    let email = document.getElementById("email");
    let firstname = document.getElementById("firstname");
    let lastname = document.getElementById("priezvisko");


    let emailError = document.getElementById("emailError");
    let nameError = document.getElementById("firstnameError");
    let surnameError = document.getElementById("priezviskoError");



    emailError.textContent = "";
    firstnameError.textContent = "";
    priezviskoError.textContent = "";


    email.classList.remove("invalid-input");
    firstname.classList.remove("invalid-input");
    priezvisko.classList.remove("invalid-input");


    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value.trim())) {
        emailError.textContent = "E-mail v zlom formáte.";
        email.classList.add("invalid-input");
        valid = false;
    }

    let nameRegex = /^[A-Za-zÁÉÍÓÚÝŔĽŠČŤŽáéíóúýŕľščťž]+$/;
    if (!nameRegex.test(firstname.value.trim()) || firstname.value.length < 2) {
        firstnameError.textContent = "Meno v zlom formáte. (iba písmená, minimálne 2)";
        firstname.classList.add("invalid-input");
        valid = false;
    }

    if (!nameRegex.test(priezvisko.value.trim()) || priezvisko.value.length < 2) {
        priezviskoError.textContent = "Priezvisko v zlom formáte. (iba písmená, minimálne 2)";
        priezvisko.classList.add("invalid-input");
        valid = false;
    }


    if (!valid) {
        event.preventDefault();
    }
});

function validateField(fieldID, errorID, validatorFunction, errorMessage) {
    let field = document.getElementById(fieldID);
    let error = document.getElementById(errorID);

    error.textContent = "";
    field.classList.remove("invalid-input");

    if (!validatorFunction(field.value.trim())) {
        error.textContent = errorMessage;
        field.classList.add("invalid-input");
        return false;
    }
    return true;
}


document.getElementById("email").addEventListener("blur", function () {
    validateField("email", "emailError", (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value), "E-mail v zlom formáte.");
});

document.getElementById("firstName").addEventListener("blur", function () {
    validateField("firstName", "firstnameError", (value) => /^[A-Za-zÁÉÍÓÚÝŔĽŠČŤŽáéíóúýŕľščťž]+$/.test(value) && value.length >= 2, "Meno v zlom formáte. (iba písmená, minimálne 2)");
});

document.getElementById("priezvisko").addEventListener("blur", function () {
    validateField("priezvisko", "priezviskoError", (value) => /^[A-Za-zÁÉÍÓÚÝŔĽŠČŤŽáéíóúýŕľščťž]+$/.test(value) && value.length >= 2, "Priezvisko v zlom formáte. (iba písmená, minimálne 2)");
});




