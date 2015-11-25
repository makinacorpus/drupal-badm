(function (Drupal, $) {
  "use strict";

  Drupal.media = Drupal.media || {};
  Drupal.media.popups = Drupal.media.popups || {};

  Drupal.media.popups.getDialogOptions = function () {
    return {
      buttons: {},
      dialogClass: 'media-wrapper',
      modal: true,
      draggable: false,
      resizable: false,
      minWidth: "900px",
      width: "900px",
      position: 'center',
      overlay: {
        backgroundColor: '#000000',
        opacity: 0.4
      },
      zIndex: 10000
    };
  };

  Drupal.media.popups.getIframeOptions = function () {
    return {
      buttons: {},
      dialogClass: 'media-wrapper',
      modal: true,
      draggable: false,
      resizable: false,
      minWidth: "100%",
      width: "100%",
      height: "570px",
      minHeight: "570px",
      position: 'center',
      overlay: {
        backgroundColor: '#000000',
        opacity: 0.4
      },
      zIndex: 10000
    };
  };

  /**
   * Get an iframe to serve as the dialog's contents. Common to both plugins.
   */
  Drupal.media.popups.getPopupIframe = function (src, id, options) {
    var defaults = {width: '800px', scrolling: 'auto'};
    var options = $.extend({}, defaults, options, Drupal.media.popups.getIframeOptions());

    return $('<iframe class="media-modal-frame"/>')
      .attr('src', src)
      .attr('width', options.width)
      .attr('min-width', options.minWidth)
      .attr('height', options.height)
      .attr('min-height', options.minHeigth)
      .attr('id', id)
      .attr('seamless', 'seamless')
      .attr('frameBorder', 0)
      .attr('border', 0)
      .attr('scrolling', options.scrolling)
    ;
  };
}(Drupal, jQuery));
