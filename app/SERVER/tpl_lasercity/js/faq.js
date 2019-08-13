(function() {
  //Autocomplete
  const autocompleteIndex = document.querySelector('.faq-search__autocomplete');

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
    selector: '.faq-search__input',
    url: 'https://lasercity.com.ua/?option=com_lasercity&task=getFaq',
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

  //QuestionList
  const questionsTitle = document.querySelectorAll('.faq__title');

  for (let i = 0; i < questionsTitle.length; i++) {
    questionsTitle[i].addEventListener('click', function () {
      questionsTitle[i].nextElementSibling.classList.toggle('faq__item-wrapper--show');
    })
  }
})();