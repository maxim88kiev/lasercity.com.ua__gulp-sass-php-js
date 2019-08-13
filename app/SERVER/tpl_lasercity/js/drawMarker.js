function drawMarker(item) {
  item['logo'] = item['logo'].split('.').slice(0, -1).join('.');

  let result = `
      <header class="point-popup__header">
        ${window.outerWidth <= 1170 ? `` 
                                    : `
                                      <picture>
                                        <source data-srcset="${item['logo']}_90x68.webp 1x, ${item['logo']}_180x136.webp 2x" type="image/webp">
                                         <img class="point-popup__img lazyload" data-src="${item['logo']}_90x68.jpg" data-srcset="${item['logo']}_180x136.jpg 2x" loading="lazy" title="Фотография салона ${item['title']}" alt="Фотография салона ${item['title']}">
                                      </picture>
                                      `}
        <div class="point-popup__title-wrapper">
          <h3 class="point-popup__title"><a href="${item['href']}">${item['title']}</a></h3>
          <p class="point-popup__description">${item['type']}</p>
        </div>
      </header>`;
  if (item['metro'] !== null || item['location_data'] !== null) {
    result += `<div class="point-popup__address salon-address">`;
    if (item['metro'] !== null) {
      result += `<p class="salon-address__underground">
          <svg xmlns="http://www.w3.org/2000/svg" fill="${item['metro_line']}" width="14" height="14" viewBox="337.5 232.3 125 85.9" aria-hidden="true">
            <polygon  points="453.9,306.2 424.7,232.3 400,275.5 375.4,232.3 346.1,306.2 337.5,306.2 337.5,317.4 381.7,317.4 381.7,306.2 375.1,306.2 381.5,287.8 400,318.2 418.5,287.8 424.9,306.2 418.3,306.2 418.3,317.4 462.5,317.4 462.5,306.2 "></polygon>
          </svg>
          ${item['metro']}
        </p>`;
    }
    if (item['location_data'] !== null) {
      result += `<p class="salon-address__street">${item['location_data']}</p>`;
    }
    result += `</div>`;
  }
  result += `<div class="point-popup__advantage">`;
  const countComforts = item['comforts'].length;
  if (countComforts) {
    result += `<ul class="point-popup__services-list services-list">`;
    for (let i = 0; i < countComforts; i++) {
      result += `<li class="services-list-item services-list-item--${item['comforts'][i].icon}" title="${item['comforts'][i].title}">
                          <span>${item['comforts'][i].title}</span>
                        </li>`;
    }
    result += `</ul>`;
  }
  result += `<a class="point-popup__rating rating-salon" href="#">
        <div class="rating-salon__point-wrapper">
          <output class="rating-salon__point">4,9</output>
          <ul class="rating-salon-stars">
            <li class="rating-salon__star rating-salon__star--silver">
              <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207">
                <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
              </svg>
              <span class="visually-hidden">Хорошо</span>
            </li>
            <li class="rating-salon__star rating-salon__star--silver">
              <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207">
                <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
              </svg>
              <span class="visually-hidden">Хорошо</span>
            </li>
            <li class="rating-salon__star rating-salon__star--silver">
              <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207">
                <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
              </svg>
              <span class="visually-hidden">Хорошо</span>
            </li>
            <li class="rating-salon__star rating-salon__star--silver">
              <svg fill="#adadad" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 482.207 482.207">
                <polygon points="482.207,186.973 322.508,153.269 241.104,11.803 159.699,153.269 0,186.973 109.388,308.108 92.094,470.404 241.104,403.803 390.113,470.404 372.818,308.108 "></polygon>
              </svg>
              <span class="visually-hidden">Хорошо</span>
            </li>
            <li class="rating-salon__star rating-salon__star--transparent">
              <svg fill="#cacaca" width="15" height="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487.222 487.222">
                <g>
                  <path d="M486.554,186.811c-1.6-4.9-5.8-8.4-10.9-9.2l-152-21.6l-68.4-137.5c-2.3-4.6-7-7.5-12.1-7.5l0,0c-5.1,0-9.8,2.9-12.1,7.6 l-67.5,137.9l-152,22.6c-5.1,0.8-9.3,4.3-10.9,9.2s-0.2,10.3,3.5,13.8l110.3,106.9l-25.5,151.4c-0.9,5.1,1.2,10.2,5.4,13.2 c2.3,1.7,5.1,2.6,7.9,2.6c2.2,0,4.3-0.5,6.3-1.6l135.7-71.9l136.1,71.1c2,1,4.1,1.5,6.2,1.5l0,0c7.4,0,13.5-6.1,13.5-13.5 c0-1.1-0.1-2.1-0.4-3.1l-26.3-150.5l109.6-107.5C486.854,197.111,488.154,191.711,486.554,186.811z M349.554,293.911 c-3.2,3.1-4.6,7.6-3.8,12l22.9,131.3l-118.2-61.7c-3.9-2.1-8.6-2-12.6,0l-117.8,62.4l22.1-131.5c0.7-4.4-0.7-8.8-3.9-11.9 l-95.6-92.8l131.9-19.6c4.4-0.7,8.2-3.4,10.1-7.4l58.6-119.7l59.4,119.4c2,4,5.8,6.7,10.2,7.4l132,18.8L349.554,293.911z"></path>
                </g>
              </svg>
              <span class="visually-hidden">Плохо</span>
            </li>
          </ul>
        </div>
        <p class="rating-salon__reviews"><output>2356</output> отзывов</p>
       </a>
      </div>
    `;
  return result;
}