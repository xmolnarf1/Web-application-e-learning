let points = 0;

// window.onload = function (){
//     localStorage.setItem('score', 0);
// }
if (!localStorage.getItem('score')) {
    localStorage.setItem('score', 0);
}

function checkAnswer(question, correctOption) {
    const result = document.getElementById('result-' + question);
    const selectedOption = document.querySelector('input[name="question-' + question + '"]:checked');


    if (selectedOption) {
        if (selectedOption.value === correctOption) {
            result.innerHTML = "<span style='color:green;'>Správna odpoveď!</span>";
            points++;


            let score = parseInt(localStorage.getItem('score'));
            score++;
            localStorage.setItem('score', score);
        } else {
            result.innerHTML = "<span style='color:red;'>Nesprávna odpoveď!</span>";

        }
        // Disable all radio buttons for this question
        const radios = document.querySelectorAll(`input[name="question-${question}"]`);
        radios.forEach(radio => radio.disabled = true);
    } else {
        result.innerHTML = "<span style='color:red;'>Vyberte odpoveď!</span>";
    }

}

// function displayPoints() {
//     let score = localStorage.getItem('score');
//     const result = document.getElementById('result-whole');
//     result.innerHTML = "<span style='color:green;'>Počet bodov: " + score + " / 10</span>";
//
//
//     const triedy = document.getElementById('vysledok');
//     const percentage = (score / 10) * 100;
//     if(percentage >= 90){
//         triedy.innerHTML = "<span style='color:green;'>Veľmi dobrý výsledok! Výsledok: " + percentage + "%</span>";
//     }else if(percentage >= 70){
//         triedy.innerHTML = "<span style='color:green;'>Dobrý výsledok! Výsledok: " + percentage + "%</span>";
//     } if(percentage >= 45){
//         triedy.innerHTML = "<span style='color:green;'>Nabudúce to bude lepšie! Výsledok: " + percentage + "%</span>";
//     } else{
//         triedy.innerHTML = "<span style='color:green;'>Zlý výsledok!  Pozri sa znovu na učivo a skús to znova. Výsledok: " + percentage + "%</span>";
//     }
//
//
//     localStorage.setItem('score', 0);
//     // alert("Kolko bodov si dostal? " + score);
// }



function displayPoints() {
    let totalScore = 0;

    // Example: Count correct answers based on predefined logic
    const correctAnswers = {
    1: '2', 2: '1', 3: '2', 4: '1',
    5: '2', 6: '3', 7: '2', 8: '2',
    9: '1', 10: '2'
    };

    for (let question in correctAnswers) {
        const selected = document.querySelector(`input[name="question-${question}"]:checked`);
        if (selected && selected.value === correctAnswers[question]) {
            totalScore += 1;
        }
    }

    document.getElementById("result-whole").innerText = `Total Score: ${totalScore}`;

    // Submit score to backend
    fetch('http://molnarserver.local/save_score.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `score=${totalScore}`
    })
        .catch(() => {
            return fetch('http://192.168.1.110/save_score.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `score=${totalScore}`
            });
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById("vysledok").innerText = data;
        })
    .catch(error => console.error('Error:', error));
}


//
//
//
//
//
//
//
//
//
//
// function validateUsername(poleID, errorMessage){   //validujem ci bol zadany pouzivatelske meno
//     const meno = document.getElementById(poleID);
//     const poleMenoError = document.getElementById(errorMessage);
//
//     if (meno.value.trim() === ""){  //ak nie, vypis error message a return false
//         poleMenoError.style.display = 'inline';
//         return false;
//     } else{  //ak ano nevypis nic a return true
//         poleMenoError.style.display = 'none';
//         return true;
//     }
// }
//
// function validatePassword(poleID, errorMessage){        //validujem ci bol zadany password
//     const meno = document.getElementById(poleID);
//     const poleMenoError = document.getElementById(errorMessage);
//
//     if (meno.value.trim() === ""){   //ak nie, vypis error message a return false
//         poleMenoError.style.display = 'inline';
//         return false;
//     } else{   //ak ano nevypis nic a return true
//         poleMenoError.style.display = 'none';
//         return true;
//     }
// }
//
//
//
// function validateVsetko(){   //validujem ci registration form bol dobre vyplneny
//     let dobreAleboNie = true;   //najprv nastavym
//     if (!validateUsername('registration-username', 'menoError')){
//         dobreAleboNie = false;
//     }
//     if(!validatePassword('registration-password', 'passwordError')){
//        dobreAleboNie = false;
//     }
//
//     return dobreAleboNie;
// }
//
// function validateVsetkoLogin(){
//     let dobreAleboNielogin = true;
//     if (!validateUsername('login-username', 'menoErrorLogin')){
//         dobreAleboNielogin = false;
//     }
//     if(!validatePassword('login-password', 'passwordErrorLogin')){
//         dobreAleboNielogin = false;
//     }
//
//     return dobreAleboNielogin;
// }
//
//
//
//
//
// function toRegister(){
//     document.getElementById('registrationForm').style.display = 'block';
//     document.getElementById('loginForm').style.display = 'none';
// }
//
//
//
//
// function storeRegistration(){    //elmentem a rgisztralt usernamet es passwordot localStorageba
//     const meno = document.getElementById('registration-username').value;
//     const heslo = document.getElementById('registration-password').value;
//
//
//     if (validateVsetko()){
//         localStorage.setItem('registeredUsername', meno);
//         localStorage.setItem('registeredPassword', heslo);
//
//         document.getElementById('registrationForm').style.display = 'none';
//         document.getElementById('loginForm').style.display = 'block';
//
//     }
//
// }
//
// function login(){
//     let zadaneMenoPole = document.getElementById('login-username');
//     let zadaneHesloPole = document.getElementById('login-password');
//     const zadaneMeno = document.getElementById('login-username').value;
//     const zadaneHeslo = document.getElementById('login-password').value;
//
//     const ulozeneMeno = localStorage.getItem('registeredUsername');
//     const ulozeneHeslo = localStorage.getItem('registeredPassword');
//
//     const odhlasitDisplay = document.getElementById('odlhasit');
//
//     const vypis = document.getElementById('loginOKorNot');
//     if (zadaneMeno === ulozeneMeno && zadaneHeslo === ulozeneHeslo){
//         localStorage.setItem('loggedInUSer', zadaneMeno); //elmentem ki van bejelentkezve
//         vypis.style.color = "green";
//         vypis.textContent = "Login succesfull!"
//
//
//
//         // odhlasitDisplay.style.display = "block";
//         displayLoggedInUser();
//     } else{
//         vypis.style.color = "red";
//         vypis.textContent = "Login not succesfull!"
//     }
// }
//
// // function logout(){
// //     localStorage.removeItem('loggedInUser');
// //
// //     const display = document.getElementById('ktoJePrihlaseny');
// //     display.querySelector('p').textContent = "Nie ste prihlásený";
// // }
// //
// //
// // function displayLoggedInUser(){
// //     const menoUser = localStorage.getItem('loggedInUSer');
// //     const display = document.getElementById('ktoJePrihlaseny');
// //
// //     if (menoUser){
// //         display.querySelector('p').textContent = "Prihlásený: " + menoUser;
// //     } else {
// //         display.querySelector('p').textContent = "";
// //     }
// // }
//
// // On page load, check if there is a logged-in user and display it
// window.onload = function () {
//     displayLoggedInUser();
// };