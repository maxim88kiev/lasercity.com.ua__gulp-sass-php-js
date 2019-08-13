(function () {
  const autocompleteActilces = document.querySelector('.articles-filters__autocomplete');

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

  plugins.inputDelay({
    selector: '.filters__name',
    url: '/?option=com_lasercity&task=getArticles',
    callBackShow: function (array) {
      callBackShow(array, autocompleteActilces);
    },
    startAfter: 1,
    callBackHide: function () {
      autocompleteActilces.classList.remove('show');
    }
  });

  window.plugins.globalClick.add({
    selector: '.filters__name',
    containsSelector: 'articles-filters__autocomplete',
    callBackOut: function (obj) {
      obj.block.nextElementSibling.classList.remove('show');
    }
  });

})();