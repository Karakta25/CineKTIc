'use strict';

/**
 * navbar variables
 */

const navOpenBtn = document.querySelector("[data-menu-open-btn]");
const navCloseBtn = document.querySelector("[data-menu-close-btn]");
const navbar = document.querySelector("[data-navbar]");
const overlay = document.querySelector("[data-overlay]");

// Navbar Handling:
//
// Променливите navOpenBtn, navCloseBtn, navbar, и overlay се използват
// за избор на елементите които управляват навигационното меню на уеб страницата.



//Цикълът for се използва, за да се добавят слушатели за събитията
// "click" на елементите, които управляват отварянето и затварянето
// на навигационното меню.
// При кликване, те променят класовете на елементите, които се използват
// за стилизиране (например, active класове), за да се покаже или
// скрие навигационното меню.
const navElemArr = [navOpenBtn, navCloseBtn, overlay];

for (let i = 0; i < navElemArr.length; i++) {

  navElemArr[i].addEventListener("click", function () {

    navbar.classList.toggle("active");
    overlay.classList.toggle("active");
    document.body.classList.toggle("active");

  });

}

/* header sticky */
// Header Sticky Behavior:
// Добавен е слушател за събитието "scroll", който проверява позицията
// на прозореца спрямо скролирането. Ако позицията на скролиране е
// по-голяма или равна на 10 пиксела, се добавя класът "active" на
// хедъра; в противен случай този клас се премахва. Това позволява на
// хедърада се появи или изчезне като фиксиран елемент при скролиране.
const header = document.querySelector("[data-header]");

window.addEventListener("scroll", function () {

  window.scrollY >= 10 ? header.classList.add("active") : header.classList.remove("active");

});


/**
 * go top
 */

// Go Top Button:
//
// Добавен е слушател за събитието "scroll", който показва бутона
// "go top", ако позицията на скролиране на страницата е над определена
// стойност (в случая 500 пиксела). Това позволява на потребителя
// лесно да се върне горе на страницата при дълго скролиране.
const goTopBtn = document.querySelector("[data-go-top]");

window.addEventListener("scroll", function () {

  window.scrollY >= 500 ? goTopBtn.classList.add("active") : goTopBtn.classList.remove("active");

});
// Submenu Handling:
//
//Логиката включва показване и скриване на подменю при навлизане и
// излизане на курсора върху родителския елемент на подменюто.
// Създават се таймери,
// за да се контролира задержката преди подменюто да се скрие.
document.addEventListener('DOMContentLoaded', function () {
  const submenu = document.querySelector('.submenu');
  let submenuTimer;

  // Show submenu on hover
  submenu.parentElement.addEventListener('mouseenter', function () {
    clearTimeout(submenuTimer);
    submenu.style.display = 'block';
  });

  // Hide submenu with a delay on mouse leave
  submenu.parentElement.addEventListener('mouseleave', function () {
    submenuTimer = setTimeout(function () {
      submenu.style.display = 'none';
    }, 300); // Adjust the delay time (in milliseconds) as needed
  });

  // If you need to hide the submenu when not hovering over the parent element
  submenu.addEventListener('mouseenter', function () {
    clearTimeout(submenuTimer);
  });

  submenu.addEventListener('mouseleave', function () {
    submenu.style.display = 'none';
  });
  //Submenu Option Actions:
  //

  // При кликване върху опция в подменюто се изпълнява функция,
  // която прочита текста на опцията и в зависимост от него извършва
  // определени действия. Например, ако се избере "My watchlist",
  // потребителят ще бъде пренасочен
  // към страница за преглед на своята списък с видеоклипове.
  //
  //
  const submenuOptions = document.querySelectorAll('.submenu a');

  submenuOptions.forEach(option => {
    option.addEventListener('click', function (event) {
      event.preventDefault();

      const selectedOption = option.textContent;

      // Perform actions based on the selected option
      console.log('Selected option:', selectedOption);

      // Add your logic here for each option's action
      // For example, trigger a function or navigate to a different page
      if (selectedOption === 'My watchlist') {
        // Redirect to page1.html when Option 1 is selected
        window.location.href = 'display_user_watchlist.php';
      }
      // } else if (selectedOption === 'Log out') {
      //   // Redirect to page2.html when Option 2 is selected
      //   window.location.href = '../../index.php';
      // }
      // Add more conditions or actions for other options as needed
    });
  });
});


  //Submenu Option Actions:
//одът използва fetch() за изпращане на POST заявка към
// "logout_session.php". При успешно изпълнение на заявката
// (HTTP статус 200), потребителят се пренасочва към началната страница.
// В случай на грешка, се извежда съобщение за грешка в конзолата.
  function logout() {
  fetch('logout_session.php', {
    method: 'POST', // Sending data via POST method
    credentials: 'same-origin' // Ensures sending cookies if any
  })
      .then(response => {
        // Check response and redirect if needed
        if (response.ok) {
          window.location.href = '././index.php'; // Redirect after successful logout
        } else {
          console.error('Logout failed');
        }
      })
      .catch(error => {
        console.error('Logout failed: ', error);
      });



}


