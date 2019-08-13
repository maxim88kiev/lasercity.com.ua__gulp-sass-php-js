(function () {
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
    selector: '.workdays__button-open, .workday__button-close',
    callBackIn: function (obj, target) {
      if (target.classList.contains('workday__button-close')) {
        obj.block.parentElement.classList.remove('workdays__list-wrapper--active');
      } else {
        obj.block.nextElementSibling.nextElementSibling.classList.toggle('workdays__list-wrapper--active');
      }
    },
    callBackOut: function (obj, target) {
      if (target.closest('.workdays__list-wrapper') === null) {
        obj.block.nextElementSibling.nextElementSibling.classList.remove('workdays__list-wrapper--active');
      }
    }
  });

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

  //Slider
  window.plugins.slider('.salon__slider', '.slider__button--prev', '.slider__button--next');

  let renderedImagesToGalleryPopup = false;
  const listImagesToPopup = document.querySelectorAll('.slider__list img');
  const modalGalleryList = document.querySelector('.gallery .gallery__list');
  const modalGalleryButtons = document.querySelectorAll('.gallery .gallery__button');

  function renderImagesToGalleryPopup() {
    let nameOriginalImage, liElement, pictureElement, sourceElement, imgElement;
    for (let i = 0, length = listImagesToPopup.length; i < length; i++) {
      nameOriginalImage = listImagesToPopup[i].getAttribute('data-original-name');
      liElement = document.createElement('li');
      liElement.classList.add('gallery__item');
      pictureElement = document.createElement('picture');
      sourceElement = document.createElement('source');
      sourceElement.setAttribute('srcset', `${nameOriginalImage}_original.webp 1x, ${nameOriginalImage}_retina.webp 2x`);
      sourceElement.type = 'image/webp';
      imgElement = document.createElement('img');
      imgElement.classList.add('gallery__img');
      imgElement.classList.add('lazyload');
      imgElement.setAttribute('data-src', `${nameOriginalImage}_original.jpg`);
      imgElement.setAttribute('srcset', `${nameOriginalImage}_retina.jpg 2x`);
      imgElement.setAttribute('loading', 'lazy');
      imgElement.setAttribute('title', listImagesToPopup[i].getAttribute('title'));
      imgElement.setAttribute('alt', listImagesToPopup[i].getAttribute('alt'));
      pictureElement.appendChild(sourceElement);
      pictureElement.appendChild(imgElement);
      liElement.appendChild(pictureElement);
      modalGalleryList.appendChild(liElement);
    }
  }

  const html = document.querySelector('html');
  const modal = document.querySelector('.modal');

  window.plugins.globalClick.add({
    selector: '.slider__list img',
    //containsSelector: '.gallery_img,.gallery__button',
    callBackIn: function (obj) {
      if (!renderedImagesToGalleryPopup) {
        renderImagesToGalleryPopup();
        renderedImagesToGalleryPopup = true;
      }
      html.classList.add('html-overflow');
      modal.setAttribute('data-current-value', 'gallery');
      modal.classList.add('modal--active');
      const parentArray = Array.from(window.util.parentStep(obj.block, 4).children);
      const element = window.util.parentStep(obj.block, 3);
      const index = parentArray.indexOf(element);
      obj.activeImg = index;
      for (let i = 0, length = modalGalleryList.children.length; i < length; i++) {
        modalGalleryList.children[i].classList.remove('gallery__item--active');
      }
      modalGalleryList.children[index].classList.add('gallery__item--active');
    },
    callBackOut: function (obj, target) {
      if (target.closest('.gallery__button')) {
        obj.activeClick = true;
        const button = target.closest('button');
        let index = obj.activeImg;
        if (button.classList.contains('gallery__button--prev')) {
          index = obj.activeImg - 1 > 0 ? obj.activeImg - 1 : 0;
        } else if (button.classList.contains('gallery__button--next')) {
          index = obj.activeImg + 1 < modalGalleryList.children.length ? obj.activeImg + 1 : modalGalleryList.children.length - 1;
        }
        for (let i = 0, length = modalGalleryList.children.length; i < length; i++) {
          modalGalleryList.children[i].classList[i === index ? 'add' : 'remove']('gallery__item--active');
        }
        for (let i = 0, length = modalGalleryButtons.length; i < length; i++) {
          modalGalleryButtons[i].disabled = false;
        }
        button.disabled = index === 0 || index === modalGalleryList.children.length - 1;
        obj.activeImg = index;
      } else if (target.closest('.gallery__img')) {
        obj.activeClick = true;
      } else {
        html.classList.remove('html-overflow');
        modal.setAttribute('data-current-value', '');
        modal.classList.remove('modal--active');
        obj.activeClick = false;
      }
    }
  });

  //SalonPrice
  const changeBodyPriceButtons = document.querySelectorAll('.salon-price__for-button');
  const offerForTable = document.querySelectorAll('.salon-price__offer-table');

  //console.log(offerForTable);

  for (let button of changeBodyPriceButtons) {
    button.onclick = function () {
      for (let btn of changeBodyPriceButtons) {
        btn.classList.remove('salon-price__for-button--active');
      }

      const activeClasses = ['salon-price__offer-table--women', 'salon-price__offer-table--men'];
      const activeClass = 'salon-price__offer-table--' + button.dataset.toggle;

      for (const table of offerForTable) {
        for (const className of activeClasses) {
          table.classList.remove(className);
        }
        table.classList.add(activeClass);
      }
      button.classList.add('salon-price__for-button--active');
    }
  }

  const salonServices = document.querySelectorAll('.salon-prices__row-part>label');

  for (const salonService of salonServices) {
    salonService.onclick = function (evt) {
      const target = evt.target;
      if (target.localName === 'input') {
        window.util.parentStep(target, 3).classList[target.checked ? 'add' : 'remove']('salon-prices__tbody-row--active');
      }
    }
  }

  /*const offerForButton = document.querySelectorAll('.salon-price__for-button');
  const offerForTable = document.querySelectorAll('.salon-price__offer-table');

  for (let i = 0; i < offerForButton.length; i++) {
    offerForButton[i].addEventListener('click', function () {
      if (!(this.classList.contains('salon-price__for-button--active'))) {
        for (let i = 0; i < offerForButton.length; i++) {
          offerForButton[i].classList.remove('salon-price__for-button--active');
          for (let j = 0; j < offerForTable.length; j++) {
            const offerForTbody = offerForTable[j].querySelectorAll('.salon-prices__table-tbody');
            offerForTbody[i].classList.remove('salon-prices__table-tbody--active');
          }
        }
        offerForButton[i].classList.add('salon-price__for-button--active');
        for (let j = 0; j < offerForTable.length; j++) {
          const offerForTbody = offerForTable[j].querySelectorAll('.salon-prices__table-tbody');
          offerForTbody[i].classList.add('salon-prices__table-tbody--active');
        }
      }
    })
  }*/

  const openOffers = function (button, block, active) {
    for (let i = 0; i < button.length; i++) {
      button[i].addEventListener('click', function () {
        block[i].classList.toggle(active)
      });
    }
  };

  const offerButton = document.querySelectorAll('.salon-price__offer-button');
  const offerTitle = document.querySelectorAll('.salon-price__offer-title');
  const offerItem = document.querySelectorAll('.salon-price__offer-item');

  openOffers(offerButton, offerItem, 'salon-price__offer-item--active');
  openOffers(offerTitle, offerItem, 'salon-price__offer-item--active');

  const offerZonaButton = document.querySelectorAll('.salon-price__offer-zona-button');
  const offerZonaTitle = document.querySelectorAll('.salon-price__offer-zona-title');
  const offerZonaItem = document.querySelectorAll('.salon-price__offer-zona-item');

  openOffers(offerZonaButton, offerZonaItem, 'salon-price__offer-zona-item--active');
  openOffers(offerZonaTitle, offerZonaItem, 'salon-price__offer-zona-item--active');

  //AddCommentPopupRating
  const rating = function (selector) {
    const ratingType = document.querySelector(selector);
    const ratingTypeInputs = ratingType.querySelectorAll('.salon-addcomment__rating-point');
    for (let i = 0; i < ratingTypeInputs.length; i++) {
      ratingTypeInputs[i].addEventListener('click', function (e) {
        e.target.parentElement.nextElementSibling.innerText = e.target.value;

        const ratingTypeAll = document.querySelectorAll('.salon-addcomment__rating-point-total');
        let avarageRaring = 0;

        for (let j = 0; j < ratingTypeAll.length; j++) {
          avarageRaring += parseInt(ratingTypeAll[j].innerText);
        }
        avarageRaring = Math.round(avarageRaring / 5);

        const ratigStarts = document.querySelector('.salon-addcomment__ratings-list-total');

        ratigStarts.innerHTML = null;

        for (let j = 0; j < 5; j++) {
          ratigStarts.innerHTML += '<li class="salon-addcomment__ratings-total-item salon-addcomment__ratings-total-item--' +
            (j < avarageRaring ? 'color' : 'transparent') +
            '"><span class="visually-hidden">Звезда рейтинга</span></li>';
        }
      });
    }
  };

  rating('.salon-addcomment__ratings-item--place');
  rating('.salon-addcomment__ratings-item--relationship');
  rating('.salon-addcomment__ratings-item--purity');
  rating('.salon-addcomment__ratings-item--quality');
  rating('.salon-addcomment__ratings-item--price');
})();