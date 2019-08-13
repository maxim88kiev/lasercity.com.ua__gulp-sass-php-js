(function () {
  //Page-Header
  const loginPopup = document.querySelector('.popup-login');
  const singUpShowPassword = loginPopup.querySelector('.button-password');
  const hiddenPassword = loginPopup.querySelector('.popup-login__password');

  singUpShowPassword.addEventListener('click', function (evt) {
    singUpShowPassword.classList.toggle('button-password--active');
    hiddenPassword.type = hiddenPassword.type === 'password' ? 'text' : 'password';
  });

  //HeaderSetting
  window.plugins.globalClick.add({
    selector: '.page-header__setting-city',
    callBackIn: function (obj) {
        /*console.log('in', obj);*/
      obj.block.classList.add('page-header__setting-city--active');

    },
    callBackOut: function (obj, target) {
      /*console.log('out', target);*/
      obj.block.classList.remove('page-header__setting-city--active');
    }

  });

  window.plugins.globalClick.add({
    selector: '.page-header__setting-languages',
    //selectorAriaExpanded: '.page-header__setting-languages-list',
    containsSelector: '.page-header__setting-languages',
    callBackIn: function (obj) {
      obj.block.classList.toggle('page-header__setting-languages--active');
      //console.log(obj.block.querySelector('a').href);
    },
    callBackOut: function (obj, target) {
      obj.block.classList.remove('page-header__setting-languages--active');
      //console.log(obj.block.querySelector('a').href);
    }
  });

  window.plugins.globalClick.add({
    selector: '.page-header__setting-languages-item',
    //selectorAriaExpanded: '.page-header__setting-languages-list',
    containsSelector: '.page-header__setting-languages-item',
    callBackIn: function (obj) {
      self.location.href = obj.block.querySelector('a').href;
    }
  });

  /*window.plugins.globalClick.add({
    selector: '.page-header__setting-languages-list',
    //selectorAriaExpanded: '.page-header__setting-languages-list',
    containsSelector: '.page-header__setting-languages-list',

    callBackIn: function (obj, target) {
      obj.activeClick = true;
      if (target.localName === 'a') {
          self.location.href = obj.block.querySelector('a').href;
      }
    }
  });*/

  const searchButton = document.querySelector('.page-header__search-button');
  const searchWrapper = document.querySelector('.main-navigation__search-from');
  searchButton.addEventListener('click', function () {
    searchWrapper.classList.toggle('main-navigation__search-from--active');
    searchButton.classList.toggle('page-header__search-button--active');
  });

  const searchButtonClose = searchWrapper.querySelector('.main-navigation__search-button-closed');
  searchButtonClose.addEventListener('click', function () {
    searchWrapper.classList.remove('main-navigation__search-from--active');
    searchButton.classList.remove('page-header__search-button--active');
  });

  //HeaderAutocomplete
  const autocompleteIndex = document.querySelector('.main-navigation__search-autocomplete');

  function callBackShow(array, block) {
    block.classList.add('show');
    block.querySelector('.autocomplete__results > output').innerHTML = array.length;
    const list = block.querySelector('.autocomplete__list');
    list.innerHTML = null;
    let itemList = '';
    for (let i = 0; i < array.length; i++) {
      itemList += `<li class="autocomplete__item"><a href="${array[i].href}">${array[i].title}</a><span>${array[i].type}</span></li>`;
    }
    list.innerHTML = itemList;
  }

  window.plugins.inputDelay({
    selector: '.main-navigation__search',
    url: 'https://lasercity.com.ua/?option=com_lasercity&task=getMainInfo',
    callBackShow: function (array) {
      callBackShow(array, autocompleteIndex);
    },
    startAfter: 1,
    callBackHide: function () {
      autocompleteIndex.classList.remove('show');
    }
  });

  //Modal
  const body = document.querySelector('body');

  window.plugins.globalClick.add({
    selector: '[data-set-modal-value]',
    key: true,
    closePopup: function () {
      body.setAttribute('data-current-value', '');
      document.querySelector('html').classList.remove('html-overflow');
    },
    callBackKey: function (obj, event) {
      if (event.key === 'Escape') {
        obj.activeClick = false;
        obj.closePopup();
      }
    },
    callBackIn: function (obj) {
      if (!obj.hasOwnProperty('closeElements')) {
        obj.closeElements = document.querySelectorAll([
          '.button-cross', '.button--gray', '.multi-popup__navigation-popup-back'
        ].join());
      }
      for (let i = 0, length = obj.closeElements.length; i < length; i++) {
        obj.closeElements[i].onclick = function () {
          obj.activeClick = false;
          obj.closePopup();
        }
      }
      const dataSetModalValue = obj.block.getAttribute('data-set-modal-value');
      body.setAttribute('data-current-value', dataSetModalValue);
      document.querySelector('html').classList.add('html-overflow');
    },
    callBackOut: function (obj, target) {
      obj.activeClick = true;
      if (!target.closest('.modal > div')) {
        for (let i = 0, length = obj.closeElements.length; i < length; i++) {
          obj.closeElements[i].onclick = null;
        }
        obj.activeClick = false;
        obj.closePopup();
      }
    }
  });

  //Page-Footer
  const supportButton = document.querySelector('.page-footer__support-button');
  const footerTitle = document.querySelector('.page-footer__support-title');

  const supportOpen = function(block) {
    block.addEventListener('click', function () {
      window.util.parentStep(block, 2).classList.toggle('page-footer__support--active');
    });
  };

  supportOpen(supportButton);
  supportOpen(footerTitle);
})();