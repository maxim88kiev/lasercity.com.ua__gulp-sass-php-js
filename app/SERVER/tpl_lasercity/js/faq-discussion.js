(function() {
  //Comments ReportsButton
  window.plugins.globalClick.add({
    selector: '.comments__button-functions',
    containsSelector: 'comments__rate',
    callBackIn: function (obj) {
      obj.block.parentElement.classList.toggle('comments__rate--active');
    },
    callBackOut: function (obj) {
      obj.block.parentElement.classList.remove('comments__rate--active');
    }
  });

  //Comments UserPopup
  window.plugins.globalClick.add({
    selector: '.comments__autor-image--open-popup',
    containsSelector: 'comments__autor-popup',
    callBackIn: function (obj) {
      obj.block.parentElement.parentElement.parentElement.classList.toggle('comments__autor-popup-wrapper--active');
    },
    callBackOut: function (obj) {
      obj.block.parentElement.parentElement.parentElement.classList.remove('comments__autor-popup-wrapper--active');
    }
  });
})();