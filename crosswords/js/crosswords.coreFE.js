/* global Drupal */
/* Script Written - Developed by Nirmalya Mondal */
/**
 * @file
 * Provides Ajax `Crosswords` data updating via Crosswords Module.
 */

(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.crosswords = {
        attach: function (context, settings) {
            var cw = settings.crosswordsData;
            var hor = settings.crosswordsHorizontal;
            var ver = settings.crosswordsVerticle;
            var cw_id = "crossword12345";

            var crossword_html = $.crosswordCreate({
                crossword_id: cw_id,
                crossword_val: cw,
                hor_val: hor,
                ver_val: ver,
                caption: settings.crosswordsCaption
            });
            $("#definitions")
                    .append("<h3>Horizontal/ Across</h3>")
                    .append(crossword_html.def[0])
                    .append("<h3>Verticale/ Down</h3>")
                    .append(crossword_html.def[1])
                    .before(crossword_html.schema);

            $("#" + cw_id).crossword();

            $("#crossword_check").click(function () {
                var check_crosswords = '';
		check_crosswords = $.crosswordCheck({
                    solution: cw,
                    crossword_id: cw_id,
                    level: 1
                });
		if(check_crosswords == false){
		    alert('Excellent! You\'ve solved this Crossword puzzle!');
		}
            });

        } // attach
    };
})(jQuery, Drupal, drupalSettings);
