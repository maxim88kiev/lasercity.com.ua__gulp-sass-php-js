(function () {
  const multiPopup = document.querySelector('.multi-popup');

  const popupListsOpen = function (typeOpen, activeBlock, activeOpen) {
    for (let i = 0; i < typeOpen.length; i++) {
      typeOpen[i].addEventListener('click', function () {
        activeBlock[i].classList.toggle(activeOpen);
      });
    }
  };

  //FilterLocation
  const placeAreas = multiPopup.querySelectorAll('.popup-place__areas-item');
  const placeAreasButton = multiPopup.querySelectorAll('.popup-place__areas-button');
  const placeAreasTitle = multiPopup.querySelectorAll('.popup-place__areas-title');

  popupListsOpen(placeAreasButton, placeAreas, 'popup-place__areas-item--active');
  popupListsOpen(placeAreasTitle, placeAreas, 'popup-place__areas-item--active');
})();