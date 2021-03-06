/**
 * @file
 * Behaviors of Varbase hero slider media for vimeo embeded videos scripts.
 */

(function ($, _, Drupal, drupalSettings) {
  "use strict";

Drupal.behaviors.varbaseHeroSliderMedia_vimeo = {
    attach: function (context, settings) {

      // On before slide change.
      $('.slick--view--varbase-heroslider-media .slick__slider', context).on('beforeChange', function (event, slick, currentSlide, nextSlide) {
        var currentSlideObject = $('.slide--' + currentSlide + '.slick-active');
        var nextSlideObject = $('.slide--' + nextSlide);
        var currentIframe = currentSlideObject.find('.slick--view--varbase-heroslider-media .varbase-video-player iframe[src*="vimeo.com"] iframe', context);
        var nextIframe = nextSlideObject.find('.slick--view--varbase-heroslider-media .varbase-video-player iframe[src*="vimeo.com"] iframe', context);

        if (currentIframe.length !== 0) {
          var currentPlayer = new Vimeo.Player(currentIframe[0]);
          currentPlayer.pause();
        }

        if (nextIframe.length !== 0) {
          var nextPlayer = new Vimeo.Player(nextIframe[0]);
          nextPlayer.on('pause', onPause);
          nextPlayer.on('ended', onFinish);
          nextPlayer.on('play', onPlayProgress);
          nextPlayer.play();
        }
      });

      // When first slide has a video (Pause the slider and play the video).
      $('.slick--view--varbase-heroslider-media .varbase-video-player iframe[src*="vimeo.com"] iframe').on("load", function () {
        var firstSlideVideo = $('.slick__slider .slick-active').find('.media-video').length !== 0;
        if (firstSlideVideo) {
          $('.slick__slider').slick('slickPause');
          var iframe = $('.slick--view--varbase-heroslider-media .varbase-video-player iframe[src*="vimeo.com"] iframe').get(0);
          var player = new Vimeo.Player(iframe);
          player.play();
        }
      });

      // Vimeo variable.
      if ($('.slick--view--varbase-heroslider-media .varbase-video-player iframe[src*="vimeo.com"] iframe').length > 0) {
        var iframe = $('.slick--view--varbase-heroslider-media .varbase-video-player iframe[src*="vimeo.com"] iframe').get(0);
        var player = new Vimeo.Player(iframe);

        // When the player is ready, add listeners for pause, finish,
        // and playProgress.
        player.on('pause', onPause);
        player.on('ended', onFinish);
        player.on('play', onPlayProgress);
      }

      // Play when paused.
      function onPause() {
        $('.slick__slider').slick('slickPlay');
      }

      // Play when finished.
      function onFinish() {
        $('.slick__slider').slick('slickPlay');
      }

      // Pause on play prgress.
      function onPlayProgress() {
        $('.slick__slider').slick('slickPause');
      }
    }
  };
})(window.jQuery, window._, window.Drupal, window.drupalSettings);
