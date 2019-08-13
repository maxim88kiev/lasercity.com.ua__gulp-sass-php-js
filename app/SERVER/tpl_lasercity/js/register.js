(function () {
  const registerPassword = document.querySelector('.register__who-input--password');
  const registerPasswordBtn = document.querySelector('.button-password');

  registerPasswordBtn.addEventListener('click', function (evt) {
    registerPasswordBtn.classList.toggle('button-password--active');
    registerPassword.type = registerPassword.type === 'password' ? 'text' : 'password';
  });
})();