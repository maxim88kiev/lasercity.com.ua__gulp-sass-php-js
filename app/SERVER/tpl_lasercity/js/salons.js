(function () {
  const multiPopup = document.querySelector('.multi-popup');

  //FilterMobileNavigation
  const filterNavButton = multiPopup.querySelectorAll('.multi-popup__list-button');

  for (let i = 0; i < filterNavButton.length; i++) {
    filterNavButton[i].addEventListener('click', function (evt) {
      for (let i = 0; i < filterNavButton.length; i++) {
        filterNavButton[i].parentElement.classList.remove('multi-popup__list-item--active');
      }
      evt.target.parentElement.classList.add('multi-popup__list-item--active');
    });
  }

  //FilterLocation
  const placeBlocks = multiPopup.querySelectorAll('.popup-place__item-location');
  const placeButton = multiPopup.querySelectorAll('.popup-place__location-button');
  const placeTitle = multiPopup.querySelectorAll('.popup-place__location-title');

  const placeOpen = function (typeOpen) {
    for (let i = 0; i < typeOpen.length; i++) {
      typeOpen[i].addEventListener('click', function () {
        if (!(this.classList.contains('popup-place__item-location--active'))) {
          for (let i = 0; i < typeOpen.length; i++) {
            placeBlocks[i].classList.remove('popup-place__item-location--active');
          }
          placeBlocks[i].classList.add('popup-place__item-location--active');
        }
      });
    }
  };

  placeOpen(placeButton);
  placeOpen(placeTitle);

  const popupListsOpen = function (typeOpen, activeBlock, activeOpen) {
    for (let i = 0; i < typeOpen.length; i++) {
      typeOpen[i].addEventListener('click', function () {
        activeBlock[i].classList.toggle(activeOpen);
      });
    }
  };

  //FilterLocation-area
  const placeAreas = multiPopup.querySelectorAll('.popup-place__areas-item');
  const placeAreasButton = multiPopup.querySelectorAll('.popup-place__areas-button');
  const placeAreasTitle = multiPopup.querySelectorAll('.popup-place__areas-title');

  popupListsOpen(placeAreasButton, placeAreas, 'popup-place__areas-item--active');
  popupListsOpen(placeAreasTitle, placeAreas, 'popup-place__areas-item--active');

  //FilterLocation-metro
  const placeMetroButton = multiPopup.querySelectorAll('.popup-place__line-button');
  const placeMetroStations = multiPopup.querySelectorAll('.popup-place__stations-list');

  for (let i = 0; i < placeMetroButton.length; i++) {
    placeMetroButton[i].addEventListener('click', function (evt) {
      for (let i = 0; i < placeMetroButton.length; i++) {
        placeMetroButton[i].parentElement.classList.remove('popup-place__line-item--active');
        placeMetroStations[i].classList.remove('popup-place__stations-list--active');
      }
      evt.target.parentElement.classList.add('popup-place__line-item--active');
      placeMetroStations[i].classList.add('popup-place__stations-list--active');
    });
  }

  //popupZona
  const zonaButton = multiPopup.querySelectorAll('.popup-zona__organ-button');
  const zonatTitle = multiPopup.querySelectorAll('.popup-zona__organ-title');
  const activeZona = multiPopup.querySelectorAll('.popup-zona__item-organ');

  if (window.outerWidth <= 1169) {
    popupListsOpen(zonaButton, activeZona, 'popup-zona__item-organ--active');
    popupListsOpen(zonatTitle, activeZona, 'popup-zona__item-organ--active');
  } else if (window.outerWidth >= 1170) {
      for (const btn of Array.from(zonaButton).concat(Array.from(zonatTitle))) {
        btn.onclick = function (evt) {
          for (const el of activeZona) {
            el.classList.remove('popup-zona__item-organ--active')
          }
          window.util.parentStep(evt.target, 2).classList.add('popup-zona__item-organ--active');
        }
      }
  }
  //popupAparat
  const aparatButton = multiPopup.querySelectorAll('.popup-aparat__kind-button');
  const aparatTitle = multiPopup.querySelectorAll('.popup-aparat__kind-title');
  const activeAparat = multiPopup.querySelectorAll('.popup-aparat__item-kind');

  popupListsOpen(aparatButton, activeAparat, 'popup-aparat__item-kind--active');
  popupListsOpen(aparatTitle, activeAparat, 'popup-aparat__item-kind--active');

  //FilterSorting
  const sortButton = document.querySelector('.filters__type-sort-button');
  const sortList = document.querySelector('.filters__type-sort-wrapper');

  sortButton.addEventListener('click', function () {
    sortList.classList.toggle('filters__type-sort-wrapper--active');
  });

  //FilterLogic
  function getActiveFilters(selector) {
    let dataInputUrl = [];
    const activeInputs = document.querySelector(selector).querySelectorAll('[data-filter]:checked');
    if (activeInputs.length > 0) {
      let nameInput = null;
      let textInput = null;
      let valueInput = null;
      for (let i = 0; i < activeInputs.length; i++) {
        nameInput = activeInputs[i].getAttribute('data-name');
        valueInput = activeInputs[i].getAttribute('data-value');
        textInput = activeInputs[i].parentElement.innerText;

        let tmp = [];
        tmp['value'] = valueInput;
        tmp['text'] = textInput;

        if (typeof dataInputUrl[nameInput] === 'undefined') {
          dataInputUrl[nameInput] = []
        }

        dataInputUrl[nameInput].push(tmp);
      }
    }

    let toURL = '?';
    let URL_comps = [];
    for (let key in dataInputUrl) {
      if (dataInputUrl.hasOwnProperty(key)) {
        let paramValue = dataInputUrl[key];
        let values = [];
        for (let i = 0; i < paramValue.length; i++) {
          values[i] = '"' + paramValue[i]['value'] + '"';
        }
        values = '[' + values.join() + ']';
        URL_comps.push(key + '=' + values);
      }
    }

    const countURL = URL_comps.length;
    if (countURL) {
      toURL += URL_comps[0] + (countURL > 1 ? '&' : '');

      for (let i = 1; i < countURL; i++) {
        toURL += URL_comps[i] + (i < countURL - 1 ? '&' : '');
      }
      return toURL;
    }
    return '';
  }

  function Filter(evt) {
    evt.preventDefault();
    location.href = getActiveFilters('.multi-popup');
  }

  document.querySelector('.multi-popup__button-popup--show').addEventListener('click', function (e) {
    Filter(e);
  });

  const filterInputs = document.querySelectorAll('[data-filter]');
  let checkedInputsCount = 0;
  for (let i = 0; i < filterInputs.length; i++) {
    if (filterInputs[i].checked) {
      checkedInputsCount++;
    }
    filterInputs[i].addEventListener('click', function () {
      const filterInputsChecked = document.querySelectorAll('[data-filter]:checked');
      document.querySelector('.multi-popup__button-popup--show').disabled = filterInputsChecked.length === 0;
    });
  }

  if (checkedInputsCount === 0) {
    document.querySelector('.multi-popup__button-popup--show').setAttribute('disabled', true)
  }

  //AutocompleteRender
  function callBackShow(array, block) {
    block.classList.add('show');
    block.querySelector('.autocomplete__results > output').innerHTML = array.length;
    const list = block.querySelector('.autocomplete__list');
    list.innerHTML = null;
    let itemList = '';
    for (let i = 0; i < array.length; i++) {
      itemList += `<li class="autocomplete__item"><a href="${array[i].href}">${array[i].title}</a></li>`;
    }
    list.innerHTML = itemList;
  }

  //SalonsMap
  const salonsMapButton = document.querySelector('.filters__map-button');
  const salonMap = document.querySelector('.salons__onmap');

  salonsMapButton.addEventListener('click', function () {
    salonMap.classList.add('salons__onmap--active');
    document.querySelector('html').classList.add('html-overflow');
    const scrollX = window.scrollX;
    const scrollY = window.scrollY;
    if (salonMap.classList.contains('salons__onmap--active')) {
      window.onscroll = function () {
        window.scrollTo(scrollX, scrollY);
      };
    }
  });

  const salonMapButtonClose = salonMap.querySelector('.salon-map__button-close');

  salonMapButtonClose.addEventListener('click', function () {
    salonMap.classList.remove('salons__onmap--active');
    document.querySelector('html').classList.remove('html-overflow');

    window.onscroll = null;
  });

  //AutocompleteName
  const autocompleteName = document.querySelector('.filters__name-autocomplete');

  plugins.inputDelay({
    selector: '.filters__name',
    url: 'https://lasercity.com.ua/index.php?option=com_lasercity&task=getSalonNames',
    callBackShow: function (array) {
      callBackShow(array, autocompleteName);
    },
    startAfter: 1,
    callBackHide: function () {
      autocompleteName.classList.remove('show');
    }
  });

  window.plugins.globalClick.add({
    selector: '.filters__name',
    containsSelector: 'filters__name-autocomplete',
    callBackOut: function (obj) {
      obj.block.nextElementSibling.classList.remove('show');
    }
  });

  //AutoCompletePlace
  const autocompletePlace = document.querySelector('.filters__place-autocomplete');

  plugins.inputDelay({
    selector: '.filters__place',
    url: 'https://lasercity.com.ua/?option=com_lasercity&task=getPlaceNames',
    callBackShow: function (array) {
      callBackShow(array, autocompletePlace);
    },
    startAfter: 1,
    callBackHide: function () {
      autocompletePlace.classList.remove('show');
    }
  });

  window.plugins.globalClick.add({
    selector: '.filters__place',
    containsSelector: 'filters__place-autocomplete',
    callBackOut: function (obj) {
      obj.block.nextElementSibling.classList.remove('show');
    }
  });

  //Pagination
  plugins.loadMore(
    '.salons__show-all',
    'https://lasercity.com.ua/?option=com_lasercity&task=getAffiliates',
    '.salons__pagination',
    '.salons_salon-list');

  plugins.loadMore(
    '.organizations__show-all',
    'https://lasercity.com.ua/?option=com_lasercity&task=getOrganizations',
    '.salons__pagination',
    '.salons_salon-list');

  //Description
  window.plugins.globalClick.add({
    selector: '.salons__description-button',
    callBackIn: function (obj) {
      obj.block.parentElement.classList.add('salons__description--active');
    }
  });

  //Phones
  window.plugins.globalClick.add({
    selector: '.phonebook__button',
    containsSelector: '.phonebook__wrapper',
    callBackIn: function (obj) {
      obj.block.parentElement.classList.toggle('phonebook__wrapper--active');
    },
    callBackOut: function (obj) {
      obj.block.parentElement.classList.remove('phonebook__wrapper--active');
    }
  });

  window.plugins.globalClick.add({
    selector: '.phonebook__item',
    callBackIn: function (obj, target) {
      obj.block.parentElement.parentElement.classList.add('phonebook__wrapper--active-popup');
      const newTarget = target.closest('.phonebook__popup-close');
      if (newTarget !== null) {
        newTarget.parentElement.parentElement.classList.remove('.phonebook__wrapper--active-popup')
      }
    },
    callBackOut: function (obj, target) {
      const wrapper = target.closest('.phonebook__popup');
      const close = target.closest('.phonebook__popup-close');
      const showPhone = target.closest('.phonebook__popup-show-number');
      if (wrapper !== null && close === null) {
        obj.activeClick = true;
        if (showPhone !== null) {
          showPhone.parentElement.classList.add('phonebook__popup-item--active');
        }
      } else {
        obj.block.parentElement.parentElement.classList.remove('phonebook__wrapper--active-popup');
      }
    }
  });

  //Workdays
  window.plugins.globalClick.add({
    selector: '.branches-list__workdays, .workday__button-close',
    callBackIn: function (obj, target) {
      if (target.classList.contains('workday__button-close')) {
        obj.block.parentElement.classList.remove('workdays__list-wrapper--active');
      } else {
        obj.block.children[2].classList.toggle('workdays__list-wrapper--active');
      }
    },
    callBackOut: function (obj, target) {
      if (target.closest('.workdays__list-wrapper') === null) {
        obj.block.children[2].classList.remove('workdays__list-wrapper--active');
      }
    }
  });
})();