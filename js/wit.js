/*! Widgets In Tabs 
 * Copyright (c) 2014-2016 - Anas H. Sulaiman
 * Licensed under the GPL2 or later
 */

(function(factory) {
  if (typeof define === 'function' && define.amd) {
    /*AMD. Register as an anonymous module.*/
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    /*Node/CommonJS style for Browserify*/
    module.exports = factory;
  } else {
    /*Browser globals*/
    factory(jQuery);
  }
}(function($) {
  $.fn.wit = function() {
    return this.each(function() {

      var $this = $(this),
          options = {},
          l10n = {},
          rotateHandle = null,
          heights = {
            'maxInt': 0
          };

      /*WIT_L10N is expected to come from the php part*/
      if (typeof WIT_L10N == 'object' && WIT_L10N !== null) {
        l10n = WIT_L10N;
      }

      /*WIT_DEFAULTS is expected to come from the php part*/
      if (typeof WIT_DEFAULTS == 'object' && WIT_DEFAULTS !== null) {
        options = WIT_DEFAULTS;
      }
      var $container = $this.find(".wit-tab-container").first();
      options = $.extend({}, options, $container.data());

      /*Join child widgets titles*/
      var $titles = $this.find(".wit-tab-title");
      var $witTitle = $this.find(":header").first().addClass('wit-title');
      $witTitle.text("");
      if (options.tab_style === "show_all")
        $witTitle.addClass('show-all');

      $titles.each(function() {
        var $titleEl = $(this);
        var titleText = $.trim($titleEl.text());
        var titleId = $titleEl.parent().attr("id");
        $titleEl.remove();

        if ("" === titleText)
          titleText = l10n.string_untitled;

        $witTitle.append(
          $(" <a/>", {
            href: "#wit_" + titleId,
            text: titleText,
            "class": "wit-tab-title"
          })
        );
      });

      /*Check if there is an untitled widget*/
      var $tabs = $this.find(".wit-tab-content");
      $tabs.each(function(index, el) {
        if ($witTitle.children('a[href="#wit_' + el.id + '"]').length == 0) {
          var missingTitle = $("<a/>", {
            href: "#wit_" + el.id,
            text: l10n.string_untitled,
            "class": "wit-tab-title"
          });

          if (index >= $witTitle.children().size()) {
            $witTitle.append(missingTitle);
          } else {
            $witTitle.children().eq(index).before(missingTitle);
          }
        }

        heights[el.id] = $(el).css('height');
        if (parseInt(heights[el.id].replace('px', '')) > heights['maxInt']) {
          heights['maxInt'] = parseInt(heights[el.id].replace('px', ''));
          heights['max'] = heights[el.id];
        }
      });
      $titles = $witTitle.children();
      $tabs.hide();

      /*register hash event*/
      $(window).hashchange(function(){
        if (location.hash) {
          rotate($('a[href="' + location.hash + '"]'));
        }
      });

      /*Height Feature*/
      function updateMaxHeight(el) {
        if (!el) return;

        heights[el.id] = $(el).css('height');
        if (parseInt(heights[el.id].replace('px', '')) > heights['maxInt']) {
          heights['maxInt'] = parseInt(heights[el.id].replace('px', ''));
          heights['max'] = heights[el.id];
        }
      }

      function rotate($el) {
        if ($el.length == 0) {
          return false;
        }

        var effect,
          fnShow,
          $previousTab = $tabs.filter(':visible'),
          $nextTab = $tabs.filter($el.attr("href").replace("wit_", ""));

        if ($nextTab.length === 0) {
          return false;
        }

        if ($nextTab[0] === $previousTab[0]) {
          /*Feature: instead of rtotating to the same tab, toggle it*/
          if (options.extra_closable_tabs) {
            $nextTab = $('');
          } else {
            return false;
          }
        }

        if (options.extra_remember_last) {
          if ($nextTab.length == 0) {
            docCookies.removeItem('wit_'+$this.attr('id'), '/', undefined);
          } else {
            docCookies.setItem('wit_'+$this.attr('id'), $nextTab.attr('id'), undefined, '/', undefined, false);
          }
        }

        /*CSS*/
        $titles.filter(".wit-selected").removeClass("wit-selected");
        if ($nextTab.length > 0) {
          $el.addClass("wit-selected");
        }

        $previousTab.removeClass('wit-selected');

        /*No animation*/
        if (!options.animate || options.duration === 0) {
          if (options.height == 'adaptive') {
            /*$container.css('height', $nextTab.css('height'));*/
          } else {
            updateMaxHeight($nextTab[0]);
            $container.css('height', heights['max']);
          }

          $previousTab.hide();
          $nextTab.show();

          scrollTitle($el);

          return true;
        }

        /*Animation*/

        /*Height*/
        if (options.height == 'adaptive') {
          /*$container.animate({
            height: $nextTab.css('height')
          }, options.duration);*/
        } else {
          updateMaxHeight($nextTab[0]);
          $container.animate({
            height: heights['max']
          }, options.duration);
        }

        /*Parallel*/
        if (options.effect_style === "prll") {
          /*Hide*/
          if (options.hide_effect.indexOf('classic') == 0) {
            /*Classic*/
            if (options.hide_effect === 'classic_fade') {
              $previousTab.fadeOut(options.duration);
            } else if (options.hide_effect === 'classic_slide') {
              $previousTab.slideUp(options.duration);
            }
          } else {
            /*Fancy*/
            effect = getEffect(options.hide_effect);
            $previousTab.hide(effect.name, effect.options, options.duration);
          }
          /*Show*/
          if (options.show_effect.indexOf('classic') == 0) {
            /*Classic*/
            if (options.show_effect === 'classic_fade') {
              $nextTab.addClass('wit-selected').fadeIn(options.duration);
            } else if (options.show_effect === 'classic_slide') {
              $nextTab.addClass('wit-selected').slideDown(options.duration);
            }
          } else {
            /*Fancy*/
            effect = getEffect(options.show_effect);
            $nextTab.addClass('wit-selected').show(effect.name, effect.options, options.duration);
          }

          scrollTitle($el);

          return true;
        }

        /*Sequential*/
        /*if (options.effect_style === "seq")*/
        /*Prepare show effect*/
        if (options.show_effect.indexOf('classic') == 0) {
          /*Classic*/
          if (options.show_effect === 'classic_fade') {
            fnShow = function() {
              $nextTab.addClass('wit-selected').fadeIn(options.duration / 2);
            }
          } else if (options.show_effect === 'classic_slide') {
            fnShow = function() {
              $nextTab.addClass('wit-selected').slideDown(options.duration / 2);
            }
          }
        } else {
          /*Fancy*/
          effect = getEffect(options.show_effect);
          fnShow = function() {
            $nextTab.addClass('wit-selected').show(effect.name, effect.options, options.duration / 2);
          }
        }
        /*Hide*/
        if ($previousTab.length !== 0) {
          /*We have a previous tab*/
          if (options.hide_effect.indexOf('classic') == 0) {
            /*Classic*/
            if (options.hide_effect === 'classic_fade') {
              $previousTab.fadeOut(options.duration / 2, fnShow);
            } else if (options.hide_effect === 'classic_slide') {
              $previousTab.slideUp(options.duration / 2, fnShow);
            }
          } else {
            /*Fancy*/
            effect = getEffect(options.hide_effect);
            $previousTab.hide(effect.name, effect.options, options.duration / 2, fnShow);
          }
        } else {
          /*1st rotation or some weird state*/
          fnShow();
        }

        scrollTitle($el);

        return true;
      }

      /*register click events*/
      $this.on("click", "a.wit-tab-title", function(e) {
        e.preventDefault();
        rotate($(this));
      });

      function getEffect(effectParam) {
        var effect = {
          "name": "",
          "options": {}
        };
        switch (effectParam) {
          case "blind_up":
            effect.name = "blind";
            effect.options = {
              "direction": "up"
            };
            break;
          case "blind_down":
            effect.name = "blind";
            effect.options = {
              "direction": "down"
            };
            break;
          case "blind_left":
            effect.name = "blind";
            effect.options = {
              "direction": "left"
            };
            break;
          case "blind_right":
            effect.name = "blind";
            effect.options = {
              "direction": "right"
            };
            break;
          case "blind_ver":
            effect.name = "blind";
            effect.options = {
              "direction": "vertical"
            };
            break;
          case "blind_hor":
            effect.name = "blind";
            effect.options = {
              "direction": "horizontal"
            };
            break;
          case "bounce":
            effect.name = "bounce";
            effect.options = {
              "distance": 20,
              "times": 5
            };
            break;
          case "clip_ver":
            effect.name = "clip";
            effect.options = {
              "direction": "vertical"
            };
            break;
          case "clip_hor":
            effect.name = "clip";
            effect.options = {
              "direction": "horizontal"
            };
            break;
          case "drop_up":
            effect.name = "drop";
            effect.options = {
              "direction": "up"
            };
            break;
          case "drop_down":
            effect.name = "drop";
            effect.options = {
              "direction": "down"
            };
            break;
          case "drop_left":
            effect.name = "drop";
            effect.options = {
              "direction": "left"
            };
            break;
          case "drop_right":
            effect.name = "drop";
            effect.options = {
              "direction": "right"
            };
            break;
          case "explode":
            effect.name = "explode";
            effect.options = {
              "pieces": 9
            };
            break;
          case "fade":
            effect.name = "fade";
            effect.options = {};
            break;
          case "fold_ver_hor":
            effect.name = "fold";
            effect.options = {
              "size": 15,
              "horizFirst": false
            };
            break;
          case "fold_hor_ver":
            effect.name = "fold";
            effect.options = {
              "size": 15,
              "horizFirst": true
            };
            break;
          case "puff":
            effect.name = "puff";
            effect.options = {
              "percent": 150
            };
            break;
          case "pulsate":
            effect.name = "pulsate";
            effect.options = {
              "times": 5
            };
            break;
          case "scale_ver":
            effect.name = "scale";
            effect.options = {
              "direction": "vertical",
              "origin": ["middle", "center"],
              "scale": "both"
            };
            break;
          case "scale_hor":
            effect.name = "scale";
            effect.options = {
              "direction": "horizontal",
              "origin": ["middle", "center"],
              "scale": "both"
            };
            break;
          case "scale_ver_hor":
            effect.name = "scale";
            effect.options = {
              "direction": "both",
              "origin": ["middle", "center"],
              "scale": "both"
            };
            break;
          case "shake_up":
            effect.name = "shake";
            effect.options = {
              "direction": "up",
              "distance": 20,
              "times": 3
            };
            break;
          case "shake_down":
            effect.name = "shake";
            effect.options = {
              "direction": "down",
              "distance": 20,
              "times": 3
            };
            break;
          case "shake_left":
            effect.name = "shake";
            effect.options = {
              "direction": "left",
              "distance": 20,
              "times": 3
            };
            break;
          case "shake_right":
            effect.name = "shake";
            effect.options = {
              "direction": "right",
              "distance": 20,
              "times": 3
            };
            break;
          case "slide_up":
            effect.name = "slide";
            effect.options = {
              "direction": "up"
            };
            break;
          case "slide_down":
            effect.name = "slide";
            effect.options = {
              "direction": "down"
            };
            break;
          case "slide_left":
            effect.name = "slide";
            effect.options = {
              "direction": "left"
            };
            break;
          case "slide_right":
            effect.name = "slide";
            effect.options = {
              "direction": "right"
            };
            break;
          default:
            effect.name = "fade";
            effect.options = {};
        }
        return effect;
      }

      function scrollTitle($tab) {
        var hiddenPortion = $witTitle.scrollLeft();
        var witTitleLeft = $witTitle.offset().left;
        var witTitleRight = witTitleLeft + $witTitle.outerWidth();

        var tabLeft = $tab.offset().left;
        var tabRight = tabLeft + $tab.outerWidth();

        if (tabRight > witTitleRight) {
          $witTitle.animate({
            scrollLeft: (hiddenPortion + (tabRight - witTitleRight))
          }, 400);
        } else if (tabLeft < witTitleLeft) {
          $witTitle.animate({
            scrollLeft: (hiddenPortion - (witTitleLeft - tabLeft))
          }, 400);
        }
      }

      /*Handle user interaction*/
      $this.hover(function(e) {
        e.preventDefault();
        clearInterval(rotateHandle);
      }, function(e) {
        e.preventDefault();
        beginAutoRotate();
      });

      /*Auto rotate*/
      function autoRotate() {
        var i = $titles.index($titles.filter(".wit-selected"));
        var j = i + 1;
        if (i >= $titles.length - 1) {
          i = $titles.length - 1;
          j = 0;
        }

        rotate($titles.eq(j));
      };

      function beginAutoRotate() {
        if (options.interval !== 0) {
          rotateHandle = setInterval(autoRotate, options.interval * 1000);
        }
      }

      /* Init */
      $witTitle.perfectScrollbar({
        wheelSpeed: 5,
        wheelPropagation: false,
        useBothWheelAxes: true,
        useKeyboard: true,
        suppressScrollY: true
      });

      if (!options.extra_closable_tabs 
          || !options.extra_closable_tabs_init) {
        // We should display a tab

        // check hash
        if (!location.hash 
          || !rotate($('a[href="' + location.hash + '"]'))) {
          // No hash, check extra feature: remember last shown
          if (options.extra_remember_last) {
            rotate($('a[href="#wit_'+docCookies.getItem('wit_'+this.id)+'"]'));
          } else {
            // No extra feature, display first
            rotate($titles.first());
          }
        }
      }
      
      beginAutoRotate();

    });
  };
}));

jQuery(function($) {
  /* Bugfix: Invalid HTML when using multiple WITs with the same area. */
  var colA = $('.wit-title a');
  var colLi = $('.wit-tab-container li');
  colLi.each(function() {
    var $wit,
      $currElem,
      id = $(this).attr('id'),
      $elems = colLi.filter('[id="' + id + '"]'),
      i,
      match;

    if ($elems.length <= 1) {
      return;
    }

    for (i = 0; i < $elems.length; i++) {
      $currElem = $elems.eq(i);
      $wit = $currElem.closest('.widget_widgets_in_tabs');


      /*WIT might be displayed using a shortcode, in which case there might not be an id!*/
      if (!$wit.attr('id')) {
        $wit.attr('id', 'wit-'+(i+99)); /*assuming there won't be 99 WITs on the same page!*/
        match = [$wit.attr('id')];
      } else if ($wit.hasClass('witsc') || !(match = $wit.attr('id').match(/\d+/))) {
        match = [$wit.attr('id')];
      }

      $currElem.attr('id', id + '-' + match[0]);
      colA.filter('[id="#wit_' + id + '"]').attr('id', '#wit_' + $currElem.attr('id'));
    }
  });

  /* Init */
  $('.widget_widgets_in_tabs').wit();
});
