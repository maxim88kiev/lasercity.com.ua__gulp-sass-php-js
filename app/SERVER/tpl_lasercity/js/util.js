(function () {
  window.util = {
    extend: function (defaults, options) {
      let extended = {};
      let prop;
      for (prop in defaults) {
        if (Object.prototype.hasOwnProperty.call(defaults, prop)) {
          extended[prop] = defaults[prop];
        }
      }
      for (prop in options) {
        if (Object.prototype.hasOwnProperty.call(options, prop)) {
          extended[prop] = options[prop];
        }
      }
      return extended;
    },
    checkExist: function (block) {
      return !(block === null)
    },
    getElementIndex: function (elem) {
      elem = elem.tagName ? elem : document.querySelector(elem);
      return [].indexOf.call(elem.parentNode.children, elem)
    },
    parentStep: function (element, count) {
      for (let i = 0; i < count; i++) {
        element = element.parentNode;
      }
      return element;
    },
    isArray: function (arg) {
      return Object.prototype.toString.call(arg) === "[object Array]";
    },
    cyrToLat: function (string, lan) {
      const cyr = ['й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ф', 'ы', 'і', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'є', 'я', 'ч', 'с', 'м', 'и', 'т', 'ь', 'б', 'ю'];
      const lat = ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', '[', 'a', 's', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', ';', '\'', '\'', 'z', 'x', 'c', 'v', 'b', 'n', 'm', ',', '.'];

      let findArray;
      let changeArray;

      switch (lan) {
        case 'lat' :
          findArray = cyr;
          changeArray = lat;
          break;
        case 'cyr':
          findArray = lat;
          changeArray = cyr;
          break;
        default :
          findArray = cyr;
          changeArray = lat;
          break
      }
      let result = '';
      let index;
      for (let i = 0; i < string.length; i++) {
        index = findArray.indexOf(string[i].toLowerCase());
        result += index + 1 ? (string[i] === findArray[index] ? changeArray[index] : changeArray[index].toUpperCase()) : string[i];
      }
      return result
    }
  };
})();