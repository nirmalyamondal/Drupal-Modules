/* global $, Drupal, drupalSettings, Handsontable */
/* Script Written - Developed by Nirmalya Mondal typo3india AT gmail DOT com*/
/**
 * @file
 * Provides Ajax `Crosswords` data updating via Crosswords Module.
 */

(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.crosswords = {
        attach: function (context, settings) {
            var allHandsonData = JSON.parse('[' + settings.crosswordsData + ']');
            $(function () {
                var $ = function (id) {
                    return document.getElementById(id);
                },
                        crosswordsContainer = $('crosswordsContainerDiv'),
                        hot
                hot = new Handsontable(crosswordsContainer, {
                    data: allHandsonData,
                    minSpareCols: 0,
                    minSpareRows: 0,
                    rowHeaders: true,
                    colHeaders: function (index) {
                        return ++index;
                    },
                    contextMenu: true,
                    stretchH: true,
                    stretchV: true,
                    colWidths: [50],
                    minCols: 9,
                    minRows: 9,
                    maxRows: 20,
                    maxCols: 20,
                    width: 1,
                    afterChange: function (changes, source) {
                        //var tmpData = JSON.parse(JSON.stringify(data3));
                        if (changes == null) {
                            return false;
                        }
                        var allUpdatedData = hot.getData();
                        var num_cols = hot.countCols();
                        jQuery('#edit-field-crosswords-text-0-value').val(num_cols + '###' + allUpdatedData);
                    },
                    afterRemoveCol: function (index, amount) {
                        var allUpdatedData = hot.getData();
                        var num_cols = hot.countCols();
                        jQuery('#edit-field-crosswords-text-0-value').val(num_cols + '###' + allUpdatedData);
                    },
                    afterRemoveRow: function (index, amount) {
                        var allUpdatedData = hot.getData();
                        var num_cols = hot.countCols();
                        jQuery('#edit-field-crosswords-text-0-value').val(num_cols + '###' + allUpdatedData);
                    },
                    cells: function (row, col, prop) {
                        var cellProperties = {};
                        cellProperties.renderer = CrosswordsCellRenderer;
                        cellProperties.validator = CrosswordsCellValidator;
                        return cellProperties;
                    }
                });
            });

            /**
             * Render input field in Cell of Crosswords.
             * @param '{object}' instance
             * @param '{string}' td
             * @param '{string}' row 
             * @param '{string}' col
             * @param '{string}' prop
             * @param '{string}' value
             * @param '{object}' cellProperties
             */

            function CrosswordsCellRenderer(instance, td, row, col, prop, value, cellProperties) {
                Handsontable.renderers.TextRenderer.apply(this, arguments);
                if (!value || value === '' || value === null) {
                    td.innerHTML = " ";
                }
            } // CrosswordsRowRenderer()

            /**
             * Validate input field in Cell of Crosswords.
             * @param '{string}' value
             * @param '{string}' callback 
             */

            function CrosswordsCellValidator(value, callback) {
                if (value.length > 1) {
                    alert('Must be ONE character or less. Extra characters will be removed!');
                    this.instance.setDataAtCell(this.row, this.col, value.substring(0, 1), null);
                }
                callback(true);
            } // CrosswordsCellValidator()

            jQuery("#crosswordsContainerDivRowColumn").html(generate_hint_select_box());
            jQuery("#crosswordsContainerDivRowColumn ul.crosswords-horizontal-ul .crosswords-horizontal-r-class").change(function () {
                var horizontal_rowcolinpval = get_all_inputfrom_hrorizontal_hint();
                jQuery('#edit-field-crosswords-hrow-0-value').val(horizontal_rowcolinpval);
            });
            jQuery("#crosswordsContainerDivRowColumn ul.crosswords-horizontal-ul .crosswords-horizontal-c-class").change(function () {
                var horizontal_rowcolinpval = get_all_inputfrom_hrorizontal_hint();
                jQuery('#edit-field-crosswords-hrow-0-value').val(horizontal_rowcolinpval);
            });
            jQuery("#crosswordsContainerDivRowColumn ul.crosswords-horizontal-ul .crosswords-horizontal-i-class").focusout(function () {
                var horizontal_rowcolinpval = get_all_inputfrom_hrorizontal_hint();
                jQuery('#edit-field-crosswords-hrow-0-value').val(horizontal_rowcolinpval);
            });

            jQuery("#crosswordsContainerDivRowColumn ul.crosswords-verticle-ul .crosswords-verticle-r-class").change(function () {
                var verticle_rowcolinpval = get_all_inputfrom_verticle_hint();
                jQuery('#edit-field-crosswords-hcolumn-0-value').val(verticle_rowcolinpval);
            });
            jQuery("#crosswordsContainerDivRowColumn ul.crosswords-verticle-ul .crosswords-verticle-c-class").change(function () {
                var verticle_rowcolinpval = get_all_inputfrom_verticle_hint();
                jQuery('#edit-field-crosswords-hcolumn-0-value').val(verticle_rowcolinpval);
            });
            jQuery("#crosswordsContainerDivRowColumn ul.crosswords-verticle-ul .crosswords-verticle-i-class").focusout(function () {
                var verticle_rowcolinpval = get_all_inputfrom_verticle_hint();
                jQuery('#edit-field-crosswords-hcolumn-0-value').val(verticle_rowcolinpval);
            });
            jQuery("input.add_more_horizontal_row_hint_button").click(function () {
                add_horizontal_hint_row();
            });
            jQuery("input.remove_horizontal_row_hint_button").click(function () {
                remove_horizontal_hint_row();
            });
            jQuery("input.add_more_verticle_row_hint_button").click(function () {
                add_verticle_hint_row();
            });
            jQuery("input.remove_verticle_row_hint_button").click(function () {
                remove_verticle_hint_row();
            });
            if (settings.horizontal_row === undefined) {	// While Adding
                setting_horizontal_verticle_hint_row(10, 'h');
                setting_horizontal_verticle_hint_row(10, 'v');
            }
            jQuery.each(JSON.parse(settings.horizontal_row), function (index, value) {
                index += 1;
                jQuery('#crosswords-horizontal-r-' + index).val(value);
            });
            jQuery.each(JSON.parse(settings.horizontal_col), function (index, value) {
                index += 1;
                jQuery('#crosswords-horizontal-c-' + index).val(value);
            });
            var horizontal_to_show = 0;
            jQuery.each(JSON.parse(settings.horizontal_data), function (index, value) {
                index += 1;
                jQuery('#crosswords-horizontal-h-' + index).val(value);
                if (value !== '') {
                    horizontal_to_show = index;
                }
            });
            setting_horizontal_verticle_hint_row(horizontal_to_show, 'h');

            jQuery.each(JSON.parse(settings.verticle_row), function (index, value) {
                index += 1;
                jQuery('#crosswords-verticle-r-' + index).val(value);
            });
            jQuery.each(JSON.parse(settings.verticle_col), function (index, value) {
                index += 1;
                jQuery('#crosswords-verticle-c-' + index).val(value);
            });
            var verticle_to_show = 0;
            jQuery.each(JSON.parse(settings.verticle_data), function (index, value) {
                index += 1;
                jQuery('#crosswords-verticle-h-' + index).val(value);
                if (value !== '') {
                    verticle_to_show = index;
                }
            });
            setting_horizontal_verticle_hint_row(verticle_to_show, 'v');

            /**
             * Generate Hint Option Select Full box.
             * 
             */

            function generate_hint_select_box() {
                var template = '';
                template += '<div class="crosswords-horizontal-text-div"><div class="crosswords-hint-head-label-wrapper"><label for="edit-field-crosswords-horizontal-text">Write Hints: Horizontale/ Across</label><input type="button"  name="add_more_horizontal_row" value="&nbsp;Add&nbsp;" class="add_more_horizontal_row_hint_button" />&nbsp;&nbsp;<input type="button"  name="remove_horizontal_row" value="Remove" class="remove_horizontal_row_hint_button" />&nbsp;&nbsp;<span class="add_remove_horizontal_row_hint_msg"></span></div><ul class="crosswords-horizontal-ul-wrapper">';
                for (var i = 1; i <= 40; i++) {
                    template += '<li class="crosswords-hint-hrow-litag crosswords-hint-hrowline-' + i + '"><ul class="crosswords-horizontal-ul"><li><select name="crosswords-horizontal-r-' + i + '" id="crosswords-horizontal-r-' + i + '" class="crosswords-horizontal-r-class">' + generate_option_box(20, 'Row', '') + '</select></li><li><select name="crosswords-horizontal-c' + i + '" id="crosswords-horizontal-c-' + i + '" class="crosswords-horizontal-c-class" >' + generate_option_box(20, 'Col', '') + '</select></li><li><input type="text" name="crosswords-horizontal-h-' + i + '" id="crosswords-horizontal-h-' + i + '" size="" maxlength="200" class="crosswords-input-class crosswords-horizontal-i-class" /></li></ul></li>';
                }
                template += '</ul></div>';

                template += '<div class="crosswords-verticle-text-div"><div class="crosswords-hint-head-label-wrapper"><label for="edit-field-crosswords-verticle-text">Write Hints: Verticale/ Down</label><input type="button"  name="add_more_verticle_row_hint" value="&nbsp;Add&nbsp;" class="add_more_verticle_row_hint_button" />&nbsp;&nbsp;<input type="button"  name="remove_verticle_row_hint" value="Remove" class="remove_verticle_row_hint_button" />&nbsp;&nbsp;<span class="add_remove_verticle_row_hint_msg"></span></div><ul class="crosswords-verticle-ul-wrapper">';
                for (var j = 1; j <= 40; j++) {
                    template += '<li class="crosswords-hint-vrow-litag crosswords-hint-vrowline-' + j + '"><ul class="crosswords-verticle-ul"><li><select name="crosswords-verticle-r-' + j + '" id="crosswords-verticle-r-' + j + '"   class="crosswords-verticle-r-class">' + generate_option_box(20, 'Row', '') + '</select></li><li><select name="crosswords-verticle-c-' + j + '" id="crosswords-verticle-c-' + j + '"   class="crosswords-verticle-c-class">' + generate_option_box(20, 'Col', '') + '</select></li><li><input type="text" name="crosswords-verticle-h-' + j + '" size="" id="crosswords-verticle-h-' + j + '" maxlength="200" class="crosswords-input-class crosswords-verticle-i-class" /></li></ul></li>';
                }
                template += '</ul></div>';
                return template;
            }

            /**
             * Generate Option Select.
             * @param {int} number - Total option to generate.
             * @param {string} rowOrcol - 'Row or Col' as First option element.
             * @param {int} selectedIndex - Which one to select.
             */

            function generate_option_box(number, rowOrcol, selectedIndex) {
                var return_option_string = '<option value="0">' + rowOrcol + '</option>';
                for (var i = 1; i <= number; i++) {
                    if (i === selectedIndex) {
                        return_option_string += '<option value="' + i + '" selected="selected">' + i + '</option>';
                    } else {
                        return_option_string += '<option value="' + i + '">' + i + '</option>';
                    }

                }
                return return_option_string;
            }

            /**
             * Horizontal Hint Row data gathering and writing to textarea tag.
             * 
             */

            function get_all_inputfrom_hrorizontal_hint() {
                var horizontalRowValues;
                jQuery('#crosswordsContainerDivRowColumn ul.crosswords-horizontal-ul .crosswords-horizontal-r-class').each(function () {
                    if (jQuery('#' + this.id).is(":visible")) {
                        var valueIs = jQuery('#' + this.id).val();
                        if (horizontalRowValues) {
                            horizontalRowValues = horizontalRowValues + ',' + valueIs;
                        } else {
                            horizontalRowValues = valueIs;
                        }
                    }
                });
                var horizontalColValues;
                jQuery('#crosswordsContainerDivRowColumn ul.crosswords-horizontal-ul .crosswords-horizontal-c-class').each(function () {
                    if (jQuery('#' + this.id).is(":visible")) {
                        var valueIs = jQuery('#' + this.id).val();
                        if (horizontalColValues) {
                            horizontalColValues = horizontalColValues + ',' + valueIs;
                        } else {
                            horizontalColValues = valueIs;
                        }
                    }
                });
                var horizontalInputValues;
                jQuery('#crosswordsContainerDivRowColumn ul.crosswords-horizontal-ul .crosswords-horizontal-i-class').each(function () {
                    if (jQuery('#' + this.id).is(":visible")) {
                        var valueIs = jQuery('#' + this.id).val();
                        if (horizontalInputValues) {
                            horizontalInputValues = horizontalInputValues + '###' + valueIs;
                        } else {
                            horizontalInputValues = valueIs;
                        }
                    }
                });
                return horizontalRowValues + '###RANDC###' + horizontalColValues + '###RANDC###' + horizontalInputValues;
            } //get_all_impotfrom_hrorizontal_hint

            /**
             * Verticle Hint Row data gathering and writing to textarea tag.
             * 
             */

            function get_all_inputfrom_verticle_hint() {
                var verticleRowValues;
                jQuery('#crosswordsContainerDivRowColumn ul.crosswords-verticle-ul .crosswords-verticle-r-class').each(function () {
                    if (jQuery('#' + this.id).is(":visible")) {
                        var valueIs = jQuery('#' + this.id).val();
                        if (verticleRowValues) {
                            verticleRowValues = verticleRowValues + ',' + valueIs;
                        } else {
                            verticleRowValues = valueIs;
                        }
                    }
                });
                var verticleColValues;
                jQuery('#crosswordsContainerDivRowColumn ul.crosswords-verticle-ul .crosswords-verticle-c-class').each(function () {
                    if (jQuery('#' + this.id).is(":visible")) {
                        var valueIs = jQuery('#' + this.id).val();
                        if (verticleColValues) {
                            verticleColValues = verticleColValues + ',' + valueIs;
                        } else {
                            verticleColValues = valueIs;
                        }
                    }
                });
                var verticleInputValues;
                jQuery('#crosswordsContainerDivRowColumn ul.crosswords-verticle-ul .crosswords-verticle-i-class').each(function () {
                    if (jQuery('#' + this.id).is(":visible")) {
                        var valueIs = jQuery('#' + this.id).val();
                        if (verticleInputValues) {
                            verticleInputValues = verticleInputValues + '###' + valueIs;
                        } else {
                            verticleInputValues = valueIs;
                        }
                    }
                });
                return verticleRowValues + '###RANDC###' + verticleColValues + '###RANDC###' + verticleInputValues;
            } //get_all_inputfrom_verticle_hint

            /**
             * Add Horizontal Hint row.
             * 
             */

            function add_horizontal_hint_row() {
                var total_litags = 0;
                var total_limit = 1;
                jQuery('#crosswordsContainerDivRowColumn .crosswords-hint-hrow-litag').each(function (index, value) {
                    index += 1;
                    var valueIs = jQuery('.crosswords-hint-hrowline-' + index).is(":visible");
                    if (valueIs) {
                        total_litags += 1;
                    }
                    total_limit++;
                });
                total_litags += 1;
                if (total_litags < total_limit) {
                    jQuery('.crosswords-hint-hrowline-' + total_litags).show();
                } else {
                    jQuery('.add_remove_horizontal_row_hint_msg').html('Can\'t Add anymore!');
                    jQuery('.add_remove_horizontal_row_hint_msg').delay(5000).fadeOut(1600);
                }
                var horizontal_rowcolinpval = get_all_inputfrom_hrorizontal_hint();
                jQuery('#edit-field-crosswords-hrow-0-value').val(horizontal_rowcolinpval);
            }

            /**
             * Remove Horizontal Hint row.
             * 
             */

            function remove_horizontal_hint_row() {
                var total_litags = 0;
                jQuery('#crosswordsContainerDivRowColumn .crosswords-hint-hrow-litag').each(function (index, value) {
                    index += 1;
                    var valueIs = jQuery('.crosswords-hint-hrowline-' + index).is(":visible");
                    if (valueIs) {
                        total_litags += 1;
                    }
                });
                if (total_litags > 1) {
                    jQuery('.crosswords-hint-hrowline-' + total_litags).hide();
                } else {
                    jQuery('.add_remove_horizontal_row_hint_msg').html('Can\'t Remove anymore!');
                    jQuery('.add_remove_horizontal_row_hint_msg').delay(5000).fadeOut(1600);
                }
                var horizontal_rowcolinpval = get_all_inputfrom_hrorizontal_hint();
                jQuery('#edit-field-crosswords-hrow-0-value').val(horizontal_rowcolinpval);
            }

            /**
             * Add Verticle Hint row.
             * 
             */

            function add_verticle_hint_row() {
                var total_litags = 0;
                var total_limit = 1;
                jQuery('#crosswordsContainerDivRowColumn .crosswords-hint-vrow-litag').each(function (index, value) {
                    index += 1;
                    var valueIs = jQuery('.crosswords-hint-vrowline-' + index).is(":visible");
                    if (valueIs) {
                        total_litags += 1;
                    }
                    total_limit++;
                });
                total_litags += 1;
                if (total_litags < total_limit) {
                    jQuery('.crosswords-hint-vrowline-' + total_litags).show();
                } else {
                    jQuery('.add_remove_verticle_row_hint_msg').html('Can\'t Add anymore!');
                    jQuery('.add_remove_verticle_row_hint_msg').delay(5000).fadeOut(1600);
                }
                var verticle_rowcolinpval = get_all_inputfrom_verticle_hint();
                jQuery('#edit-field-crosswords-hcolumn-0-value').val(verticle_rowcolinpval);
            }

            /**
             * Remove Verticle Hint row.
             * 
             */

            function remove_verticle_hint_row() {
                var total_litags = 0;
                jQuery('#crosswordsContainerDivRowColumn .crosswords-hint-vrow-litag').each(function (index, value) {
                    index += 1;
                    var valueIs = jQuery('.crosswords-hint-vrowline-' + index).is(":visible");
                    if (valueIs) {
                        total_litags += 1;
                    }
                });
                if (total_litags > 1) {
                    jQuery('.crosswords-hint-vrowline-' + total_litags).hide();
                } else {
                    jQuery('.add_remove_verticle_row_hint_msg').html('Can\'t Remove anymore!');
                    jQuery('.add_remove_verticle_row_hint_msg').delay(5000).fadeOut(1600);
                }
                var verticle_rowcolinpval = get_all_inputfrom_verticle_hint();
                jQuery('#edit-field-crosswords-hcolumn-0-value').val(verticle_rowcolinpval);
            }
            /**
             * Setting Horizontal/ Verticle Hint row.
             * @param {int} loop_no
             * @param {string} h_or_v 
             */

            function setting_horizontal_verticle_hint_row(loop_no, h_or_v) {
                jQuery('.crosswords-hint-' + h_or_v + 'row-litag').hide();
                for (var i = 1; i <= loop_no; i++) {
                    jQuery('.crosswords-hint-' + h_or_v + 'rowline-' + i).show();
                }
            }

        } // attach
    };
})(jQuery, Drupal, drupalSettings);
