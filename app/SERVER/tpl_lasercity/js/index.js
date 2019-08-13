(function () {
  //Autocomplete
  const autocompleteIndex = document.querySelector('.advantages__autocomplete');

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
    selector: '.advantages__input',
    url: 'https://lasercity.com.ua/?option=com_lasercity&task=getMainInfo',
    callBackShow: function (array) {
      callBackShow(array, autocompleteIndex);
    },
    startAfter: 1,
    callBackHide: function () {
      autocompleteIndex.classList.remove('show');
    }
  });

  window.plugins.globalClick.add({
    selector: '.advantages__input',
    containsSelector: 'advantages__autocomplete',
    callBackOut: function (obj) {
      obj.block.nextElementSibling.classList.remove('show');
    }
  });

  //SliderTopRating
  window.plugins.slider('.top-rating__slider', '.slider__button--prev', '.slider__button--next');

  //Attendance
  const html = document.querySelector('html');
  const attendance = document.querySelector('.attendance');

  const attendanceButton = attendance.querySelectorAll('.attendance__button');
  const attendanceTittle = attendance.querySelectorAll('.attendance__title');
  const attendanceKind = attendance.querySelectorAll('.attendance__search-item');

  const attendanceOpen = function (button) {
    for (let i = 0; i < button.length; i++) {
      button[i].addEventListener('click', function () {
        attendanceKind[i].classList.toggle('attendance__search-item--active');
        html.classList.toggle('html-overflow');
        const scrollX = window.scrollX;
        const scrollY = window.scrollY;
        if (attendanceKind[i].classList.contains('attendance__search-item--active')) {
          window.onscroll = function () { window.scrollTo(scrollX, scrollY); };
        } else {
          window.onscroll = null;
        }
      })
    }
  };

  attendanceOpen(attendanceButton);

  if (window.outerWidth <= 1169) {
    attendanceOpen(attendanceTittle);
  }


  const attendanceItemOpen = function (button, active) {
    for (let i = 0; i < button.length; i++) {
      button[i].addEventListener('click', function () {
        window.util.parentStep(button[i], 2).classList.toggle(active);
      });
    }
  };

  const attendanceTitle = attendance.querySelectorAll('.attendance__services-title');
  const servicesItemsButton = attendance.querySelectorAll('.attendance__services-button');

  if(window.outerWidth < 1170 ) {
    attendanceItemOpen(attendanceTitle, 'attendance__services-item--active');
  }
  attendanceItemOpen(servicesItemsButton, 'attendance__services-item--active');

  const aparatsItemsTitle = attendance.querySelectorAll('.attendance__aparats-name');
  const aparatsItemsButton = attendance.querySelectorAll('.attendance__aparats-button');

  if(window.outerWidth < 1170 ) {
    attendanceItemOpen(aparatsItemsTitle, 'attendance__aparats-item--active');
  }
  attendanceItemOpen(aparatsItemsButton, 'attendance__aparats-item--active');

  window.plugins.globalClick.add({
    selector: '.attendance__services-all',
    containsSelector: '.attendance__services-item-wrapper',
    callBackIn: function (obj) {
      const items = document.querySelectorAll('.attendance__services-item-wrapper');
      for (let i = 0, length = items.length; i < length; i++) {
        items[i].classList.remove('attendance__services-item-wrapper--active');
      }
      obj.block.parentElement.classList.add('attendance__services-item-wrapper--active');
    },
    callBackOut: function (obj) {
      obj.block.parentElement.classList.remove('attendance__services-item-wrapper--active');
      console.log(obj);
    }
  });

  //SliderStockIndex
  if(window.outerWidth < 1170) {
    window.plugins.slider('.index-stocks__slider', '.slider__button--prev', '.slider__button--next');
  }

  //Lasercity
  const laserCityIs = document.querySelector('.lasercity');

  const laserCityForButton = laserCityIs.querySelectorAll('.lasercity__for-button');
  const laserCitySlider = laserCityIs.querySelectorAll('.slider__list');
  const laserCityRegisterFor = laserCityIs.querySelectorAll('.lasercity__register');

  for (let i = 0; i < laserCityForButton.length; i++) {
    laserCityForButton[i].addEventListener('click', function () {
      if (!(this.classList.contains('show'))) {
        for (let i = 0; i < laserCityForButton.length; i++) {
          laserCityForButton[i].classList.remove('lasercity__for-button--active');
          laserCitySlider[i].classList.remove('slider__list--active');
          laserCityRegisterFor[i].classList.remove('lasercity__register--active');
        }
        laserCityForButton[i].classList.add('lasercity__for-button--active');
        laserCitySlider[i].classList.add('slider__list--active');
        laserCityRegisterFor[i].classList.add('lasercity__register--active');
      }
    })
  }

  const laserCitySlidPrev = laserCityIs.querySelector('.slider__button--prev');
  const laserCitySlidNext = laserCityIs.querySelector('.slider__button--next');

  if(window.outerWidth < 1170) {
    laserCitySlidPrev.addEventListener('click', function () {
      sliderMove('.slider__list', 'left')
    });
    laserCitySlidNext.addEventListener('click', function () {
      sliderMove('.slider__list', 'right')
    });
  }

  const sliderMove = function (id, move) {
    const activeClass = 'lasercity__steps-item--active';

    const sliders = document.querySelectorAll(id + '.slider__list--active .lasercity__steps-item');
    let activslider = 0;

    for (let i = 0; i < sliders.length; i++) {
      if (sliders[i].classList.contains(activeClass)) {
        activslider = i;
        break;
      }
    }

    let sliderGo = activslider;
    if (move === 'left') {
      sliderGo = sliderGo - 1 < 0 ? 0 : sliderGo - 1;
    }
    if (move === 'right') {
      sliderGo = sliderGo + 1 > sliders.length - 1 ? sliders.length - 1 : sliderGo + 1;
    }
    sliders[activslider].classList.remove(activeClass);
    sliders[sliderGo].classList.add(activeClass);
  };

  //AttendanceAparats
  const allApparatusButton = document.querySelector('.attendance__aparats-full');

  allApparatusButton.addEventListener('click', function () {
    allApparatusButton.parentElement.classList.toggle('attendance__aparats-wrapper--active')
  })
})();