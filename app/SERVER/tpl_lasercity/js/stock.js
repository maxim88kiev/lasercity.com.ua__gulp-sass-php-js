(function () {
  ////SliderOffer
  window.plugins.slider(
      '.stock__slider',
      '.slider__button--prev',
      '.slider__button--next',
      '.stock__slider-toggle',
      'stock__slider-toggle--current');

  //SliderOffer
  window.plugins.slider('.stock-offer__slider', '.slider__button--prev', '.slider__button--next');

  //StickBlock
  if (1170 <= window.outerWidth) {
    const heightOfAllBlocks = function (selectors) {
      let whileHeight = 0;
      for (let i = 0; i < selectors.length; i++) {
        const block = document.querySelector(selectors[i]);
        if (block !== null) {
          whileHeight += block.offsetHeight;
        }
      }
      return whileHeight;
    };

    window.onscroll = function (evt) {
      const blockHeigh = heightOfAllBlocks(['.page-header', '.breadcrumbs', '.stock__title']) + 67;
      const fullHeigh = heightOfAllBlocks(['.page-header', '.breadcrumbs', '.stock']) + 67;
      const fixedStock = document.querySelector('.stock__aside');
      if (window.scrollY > blockHeigh) {

        fixedStock.classList.add('stock__aside--fixed');

        if (window.scrollY < fullHeigh - 271) {
          fixedStock.style.top = (window.scrollY - blockHeigh + 10) + 'px';
          fixedStock.style.bottom = '';
        } else {
          fixedStock.style.top = (fullHeigh - blockHeigh - 271) + 'px';
        }
      } else {
        fixedStock.classList.remove('stock__aside--fixed');
      }
    };
  }

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
})();