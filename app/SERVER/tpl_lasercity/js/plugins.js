(function () {
  window.plugins = {
    inputDelay: function (options) {
      let defaults = {
        selector: 'input',
        url: null,
        timeout: 0,
        startAfter: 3,
        callBackShow: function (value, array) {
          console.log(value, array);
        },
        callBackHide: function () {
          console.log('close');
        },
        callBackSearch: function (value, array) {
          let result = [];
          let transliterateValueCyr = window.util.cyrToLat(value, 'cyr');
          let transliterateValueLat = window.util.cyrToLat(value, 'lat');
          for (let i = 0; i < array.length; i++) {
            if (array[i].title !== null) {
              if (
                array[i].title.toLocaleLowerCase().indexOf(value.toLocaleLowerCase()) + 1 ||
                array[i].title.toLocaleLowerCase().indexOf(transliterateValueCyr.toLocaleLowerCase()) + 1 ||
                array[i].title.toLocaleLowerCase().indexOf(transliterateValueLat.toLocaleLowerCase()) + 1
              ) {
                result.push(array[i]);
              }
            }
          }
          return result;
        }
      };

      const config = window.util.extend(defaults, options);

      const indexInput = document.querySelector(config.selector);
      let typingTimer = null;

      let array;

      if (config.url !== null) {
        const xmltype = new XMLHttpRequest();
        xmltype.onreadystatechange = function () {
          if (xmltype.readyState === 4 && xmltype.status === 200) {
            try {
              array = JSON.parse(xmltype.responseText);
            } catch {
              array = [];
            }
          }
        };
        xmltype.open("GET", config.url);
        xmltype.send();
      }

      function delaySearch(value) {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function () {
          if (value.length >= config.startAfter) {
            config.callBackShow(config.callBackSearch(value, array), indexInput);
          } else {
            config.callBackHide(indexInput);
          }
        }, config.timeout);
      }

      indexInput.oninput = function () {
        delaySearch(this.value);
      }
    },
    slider: function (block, arrowLeftSelector, arrowRightSelector, circleBtnSelector = null, circleActiveClass = '') {
      let allowScroll = true;
      block = document.querySelector(block);
      if (window.util.checkExist(block)) {
        const slider = block.querySelector('.slider__list');
        if (slider !== null) {
          const arrowLeft = block.querySelector(arrowLeftSelector);
          if (arrowLeft !== null) {
            arrowLeft.disabled = true;
          }
          const arrowRight = block.querySelector(arrowRightSelector);
          const circleButtons = block.querySelectorAll(circleBtnSelector);

          let activeSlider = 0;

          if (circleBtnSelector !== null) {
            for (let i = 0, length = circleButtons.length; i < length; i++) {
              circleButtons[i].onclick = function () {
                activeSlider = i;
                scrollSlide(i);
              };
            }
          }
          const elementsCount = block.querySelectorAll('.slider__item').length - 1;

          function scrollSlide(current) {
            if (circleBtnSelector !== null) {
              for (let btn of circleButtons) {
                btn.classList.remove(circleActiveClass);
              }
              circleButtons[current].classList.add(circleActiveClass);
            }

            const elements = block.querySelectorAll('.slider__item');

            let newElementsCount = elementsCount - Math.round(slider.offsetWidth / elements[current].offsetWidth);

            switch (current) {
              case 0 :
                arrowLeft.disabled = true;
                arrowRight.disabled = false;
                break;

              case newElementsCount + 1 :
                arrowLeft.disabled = false;
                arrowRight.disabled = true;
                break;

              default:
                arrowLeft.disabled = false;
                arrowRight.disabled = false;
                break;
            }
            let scroll_to = 0;
            for (let i = 0; i < current; i++) {
              scroll_to += elements[i].offsetWidth;
            }

            let pos = ~~slider.scrollLeft;
            const id = setInterval(frame, 0);
            const countPixels = 10;

            function frame() {
              const toStep = slider.scrollLeft > scroll_to;

              if (~~pos === ~~scroll_to) {
                clearInterval(id);
                allowScroll = true;
              } else {
                let tmp;
                allowScroll = false;
                if (toStep) {
                  tmp = pos - countPixels;
                  pos = tmp < scroll_to ? scroll_to : tmp;
                } else {
                  tmp = pos + countPixels;
                  pos = tmp > scroll_to ? scroll_to : tmp;
                }
                slider.scrollLeft = pos;
              }
            }
          }

          if (arrowLeft !== null) {
            arrowLeft.addEventListener('click', function () {
              if (allowScroll) {
                if (activeSlider !== 0) {
                  activeSlider--;
                }
                scrollSlide(activeSlider);
              }
            });
          }
          if (arrowRight !== null) {
            arrowRight.addEventListener('click', function () {
              if (allowScroll) {
                if (activeSlider !== elementsCount) {
                  activeSlider++;
                }
                scrollSlide(activeSlider);
              }
            });
          }
        }
      }
    },
    globalClick: {
      items: [],
      block: null,
      do: true,
      keyFunction: function (evt) {
        for (let i = 0, length = this.items.length; i < length; i++) {
          if (this.items[i].key && this.items[i].activeClick) {
            this.items[i].callBackKey(this.items[i], evt);
          }
        }
      },
      clickFunction: function (evt) {
        if (this.do) {
          const target = evt.target;
          for (let i = 0, length = this.items.length; i < length; i++) {
            const block = target.closest(this.items[i].selector);
            if (block !== null) {
              this.items[i].block = block;
              this.items[i].activeClick = true;
              this.items[i].callBackIn(this.items[i], target);
              this.toggleAriaExpanded(this.items[i].selectorAriaExpanded, true);
            } else {
              if (this.items[i].block !== null && this.items[i].activeClick) {
                if (this.items[i].containsSelector !== null) {
                  this.items[i].activeClick = true;
                  if (!target.closest(this.items[i].containsSelector)) {
                    this.toggleAriaExpanded(this.items[i].selectorAriaExpanded, false);
                    this.items[i].activeClick = false;
                    this.items[i].callBackOut(this.items[i], target);
                  }
                } else {
                  this.items[i].activeClick = false;
                  this.items[i].callBackOut(this.items[i], target);
                  this.toggleAriaExpanded(this.items[i].selectorAriaExpanded, false);
                }
              }
            }
          }
        }
      },
      init: function () {
        if ('ontouchstart' in window) {
          document.addEventListener('touchstart', function () {
            this.do = true;
          });
          document.addEventListener('touchmove', function () {
            this.do = false;
          });
          document.addEventListener('touchend', this.clickFunction.bind(this));
        } else {
          document.addEventListener('mouseup', this.clickFunction.bind(this));
          window.addEventListener('keydown', this.keyFunction.bind(this))
        }
      },
      toggleAriaExpanded: function (selectorActiveBlock, value) {
        if (selectorActiveBlock !== null) {
          const activeBlocks = document.querySelectorAll(selectorActiveBlock);
          console.log(activeBlocks.length, value);
          for (let i = 0, length = activeBlocks.length; i < length; i++) {
            activeBlocks[i].setAttribute('aria-expanded', value);
          }
        }
      },
      add: function (options) {
        let defaults = {
          selector: null,
          block: null,
          containsSelector: null,
          selectorAriaExpanded: null,
          activeClick: false,
          key: false,
          callBackIn: function (/*obj*/) {
            //console.log('---', 'in', obj);
          },
          callBackOut: function (/*obj*/) {
            //console.log('---', 'out', obj);
          },
          callBackKey: function (/*obj*/) {
            //console.log('---', 'out', obj);
          }
        };
        this.items.push(window.util.extend(defaults, options));
      }
    },
    googleMap: {
      map: null,
      popup: null,
      oldMarker: null,
      init: function (options) {
        let defaults = {
          selector: 'input',
          obj: [],
          center: {
            lat: 50.459705,
            lng: 30.530946
          },
          zoom: 11,
          marker: {
            url: '/templates/lasercity/img/pin-map.svg',
            size: {
              width: 32,
              height: 44
            }
          },
          callBackDraw: function (item) {
            let array = [];
            for (let key in item) {
              if (item.hasOwnProperty(key)) {
                array.push(`<div>${item[key]}</div>`);
              }
            }
            return array.join('');
          }
        };

        const config = window.util.extend(defaults, options);

        let items = [];
        if (window.util.isArray(config.obj)) {
          for (let i = 0, length = config.obj.length; i < length; i++) {
            if (this.checkCorrect(config.obj[i])) {
              items.push(config.obj[i]);
            }
          }
        } else {
          if (this.checkCorrect(config.obj)) {
            items.push(config.obj);
          }
        }

        this.map = new google.maps.Map(document.querySelector(config.selector), {
          center: {
            lat: config.center.lat,
            lng: config.center.lng
          },
          zoom: config.zoom
        });

        for (let i = 0, length = items.length; i < length; i++) {
          this.addMarker(items[i], config.marker, config.callBackDraw)
        }
      },
      checkCorrect: function (item) {
        return 'coordinateLat' in item && 'coordinateLng' in item && 'title' in item
      },
      addMarker: function (item, markerInfo, callBackDraw) {
        const map = this.map;
        const popup = new google.maps.InfoWindow({
          content: callBackDraw(item)
        });

        const marker = new google.maps.Marker({
          position: {
            lat: item.coordinateLat,
            lng: item.coordinateLng
          },
          map: map,
          icon: {
            url: markerInfo.url,
            size: new google.maps.Size(markerInfo.size.width, markerInfo.size.height)
          },
          title: item.title,
          zIndex: 1
        });

        marker.addListener('click', function () {
          if (window.plugins.googleMap.popup) {
            window.plugins.googleMap.popup.close(map, marker);
            if (window.plugins.googleMap.oldMarker !== null) {
              window.plugins.googleMap.oldMarker.setIcon('/templates/lasercity/img/pin-map.svg');
            }
          }
          window.plugins.googleMap.oldMarker = this;
          this.setIcon(new google.maps.MarkerImage(
            '/templates/lasercity/img/pin-map--check.svg',
            null,
            null,
            null,
            new google.maps.Size(57, 76)));
          window.plugins.googleMap.popup = popup;
          popup.open(map, marker);
        });
      }
    },
    ajaxJsonLoad: function (options) {
      let defaults = {
        url: null,
        callback: function (obj) {
          console.log(obj);
        }
      };
      const config = window.util.extend(defaults, options);
      const xmltype = new XMLHttpRequest();
      xmltype.onreadystatechange = function () {
        if (xmltype.readyState === 4 && xmltype.status === 200) {
          try {
            config.callback(JSON.parse(xmltype.responseText));
          } catch {
            console.error(xmltype);
          }
        }
      };
      xmltype.open("GET", config.url);
      xmltype.send();
    },
    setMask: function (options) {
      const defaults = {
        selector: null,
        mask: '',
        placeholderRegex: [],
        placeholderSymbol: '#',
        placeholderReplacement: '_',
        defaultRegex: /[\d]/
      };
      const config = window.util.extend(defaults, options);

      function findRegex(array, index, defaultRegex) {
        for (let i = 0; i < array.length; i++) {
          if (array[i].index.indexOf(index + 1) + 1) {
            return array[i].regex;
          }
        }
        return defaultRegex;
      }

      function replaceAt(str, index, replacement) {
        let newStr = '';
        for (let i = 0, length = str.length; i < length; i++) {
          newStr += i === index ? replacement : str[i];
        }
        return newStr;
      }

      const inputs = document.querySelectorAll(config.selector);

      let positions = [];

      for (let i = 0; i < config.mask.length; i++) {
        if (config.mask[i] === config.placeholderSymbol) {
          positions.push(i);
        }
      }

      let currentPosition = [], masks = [];
      config.mask = config.mask.replace(new RegExp(config.placeholderSymbol, 'g'), config.placeholderReplacement);

      for (let i = 0, length = inputs.length; i < length; i++) {
        currentPosition.push(0);
        masks.push(config.mask);
      }

      for (let i = 0, length = inputs.length; i < length; i++) {
        inputs[i].value = masks[i];
        inputs[i].setAttribute('data-valid', false);
        inputs[i].oninput = function (evt) {
          if (evt.data === null) {
            if (currentPosition[i] >= 0) {
              if (currentPosition[i] > 0) {
                currentPosition[i]--;
              }
              masks[i] = replaceAt(masks[i], positions[currentPosition[i]], config.placeholderReplacement);
            }
          } else if (findRegex(config.placeholderRegex, currentPosition[i], config.defaultRegex).test(evt.data)) {
            if (currentPosition[i] < positions.length) {
              masks[i] = replaceAt(masks[i], positions[currentPosition[i]], evt.data);
              currentPosition[i]++;
            }
          }
          inputs[i].value = masks[i];
          let caretPosition = currentPosition[i] < positions.length - 1 ? positions[currentPosition[i]] : positions[currentPosition[i] - 1] + 1;
          inputs[i].setSelectionRange(caretPosition, caretPosition);
          inputs[i].setAttribute('data-valid', currentPosition[i] === positions.length);
        };
      }
    },
    loadMore: function (selector, searchURL, paginationSelector, listSelector) {
      const button = document.querySelector(selector);
      if (window.util.checkExist(button)) {
        let loadClickPage = null;
        button.addEventListener('click', function () {
          function getPageUrl(page, array) {
            let newArray = [];
            for (let i in array) {
              newArray.push(array[i]);
            }
            newArray.push('page=' + page);
            return newArray;
          }

          let search = window.location.search.substring(1, window.location.search.length).split('&');

          let page = 1, searchNoPage = [], currentPage = 1;

          for (let i = 0; i < search.length; i++) {
            if (search[i].indexOf('page=') + 1) {
              page = parseInt(search[i].replace('page=', ''));
              search.splice(i, 1);
            } else {
              searchNoPage.push(search[i]);
            }
          }

          currentPage = page;

          if (loadClickPage !== null) {
            loadClickPage++;
            page = loadClickPage;
          } else {
            loadClickPage = page;
          }

          search.push('page=' + (page + 1));

          search = searchURL + '&' + search.join('&amp;');

          const xmltype = new XMLHttpRequest();
          xmltype.onreadystatechange = function () {
            if (xmltype.readyState === 4 && xmltype.status === 200) {
              const data = JSON.parse(xmltype.responseText);

              if (parseInt(data.search_info.page) === data.search_info.pages) {
                button.parentNode.remove();
              } else {
                button.querySelector('output').innerHTML = (parseInt(data.search_info.page) + 1) * data.search_info.config.limit <= data.search_info.count ?
                  data.search_info.config.limit :
                  data.search_info.count - (parseInt(data.search_info.page) * data.search_info.config.limit);
              }
              const start = parseInt(data.search_info.page) - 2 > 0 ? parseInt(data.search_info.page) - 2 : 1;
              const end = parseInt(data.search_info.page) + 2 <= data.search_info.pages ? parseInt(data.search_info.page) + 2 : data.search_info.pages;

              let li_html = [];
              if (parseInt(data.search_info.page) > 1) {
                li_html.push('<a class="pagination__button pagination__button--prev button-corner" href="' + (location.pathname) + '?' + getPageUrl(parseInt(data.search_info.page) - 1, searchNoPage).join('&amp;') + '"><span class="visually-hidden">Предыдущая страница</span></a>');
              }
              li_html.push('<ul class="pagination__page-list">');
              for (let i = start; i <= end; i++) {
                li_html.push('<li class="pagination__page-item ' + (i >= currentPage && i <= parseInt(data.search_info.page) ? 'pagination__page-item--current' : '') + '"><a ' + (i < currentPage || i > parseInt(data.search_info.page) ? 'href="' + (location.pathname) + '?' + getPageUrl(i, searchNoPage).join('&amp;') + '"' : '') + '>' + i + '</a></li>');
              }
              li_html.push('</ul>');
              if (parseInt(data.search_info.page) < data.search_info.pages) {
                li_html.push('<a class="pagination__button pagination__button--next button-corner" href="' + (location.pathname) + '?' + getPageUrl(parseInt(data.search_info.page) + 1, searchNoPage).join('&amp;') + '"><span class="visually-hidden">Следующая страница</span></a>');
              }
              document.querySelector(paginationSelector).innerHTML = li_html.join("\r\n");
              document.querySelector(listSelector).innerHTML += data.html;
            }
          };
          xmltype.open("GET", search);
          xmltype.send();
        });
      } else {
        //console.error(`Selector '${selector}' not exist`);
      }
    }
    // to be continued
  };
})();

plugins.globalClick.init();

window.plugins.setMask({
  selector: 'input[name="phone"]',
  mask: '+38 (0##) ### ## ##'
});
window.plugins.setMask({
  selector: 'input[name="phone1"]',
  mask: '+38 (0##) ### ## ##'
});
