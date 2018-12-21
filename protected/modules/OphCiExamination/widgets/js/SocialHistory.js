var OpenEyes = OpenEyes || {};

OpenEyes.OphCiExamination = OpenEyes.OphCiExamination || {};

(function (exports) {
    function SocialHistoryController(options) {
        this.options = $.extend(true, {}, SocialHistoryController._defaultOptions, options);
        this.$tableSelector = $('#' + this.options.modelName + '_entry_table');
        this.$popupSelector = $('#add-to-social-history');

        this.initialiseTriggers();

    }

    SocialHistoryController._defaultOptions = {
        modelName: 'OEModule_OphCiExamination_models_SocialHistory'
    };


    SocialHistoryController.prototype.initialiseTriggers = function () {

        $('#OEModule_OphCiExamination_models_SocialHistory_occupation_id').on('change', function () {
            if ($('#OEModule_OphCiExamination_models_SocialHistory_occupation_id option:selected').attr('value') == 7/*Other*/) {
                $('#div_OEModule_OphCiExamination_models_SocialHistory_type_of_job').show();
            } else {
                $('#div_OEModule_OphCiExamination_models_SocialHistory_type_of_job').hide();
                $('#OEModule_OphCiExamination_models_SocialHistory_type_of_job').val('');
            }
        });

        let select_lists = ['occupation', 'alcohol', 'smoking_status', 'accommodation'];
        for (i in select_lists) {
            $('#add-to-social-history ul.' + select_lists[i] + ' li').on('click', function (e) {
                $(this).siblings('.selected').removeClass('selected');
            })
        }

    };

    SocialHistoryController.prototype.addEntry = function (selectedItems) {

        // reset textField input for driving statuses
        $('#textField_driving_statuses').html('');
        this.$tableSelector.find('.js-driving-status-item').remove();

        for (let i in selectedItems) {
            let item = selectedItems[i];
            let itemSetId = item.itemSet.options.id;
            let $field = this.$tableSelector.find('#' + this.options.modelName + '_' + itemSetId);
            let $textField = this.$tableSelector.find('#textField' + '_' + itemSetId);
            let $td = $textField.parent();

            $field.val(item.id);
            $field.change();

            if (itemSetId === "driving_statuses") {
                let $hidden = $("<input>",{"type":"hidden", "class":"js-driving-status-item", "name": this.options.modelName + "[driving_statuses][]"}).val(item.id);
                $td.append($hidden);
                // insert first driving status
                if ($textField.html() === '') {
                    $textField.html(item.label);
                } else {
                    // append the rest of the driving statuses to the first one
                    $textField.append(', ' + item.label);
                }
            } else if (itemSetId === "alcohol_intake") {
                $textField.html(item.id);
            } else {
                // for the rest of the elements, show the info in the textField
                $textField.html($field.find(":selected").text());
            }
        }

        this.$popupSelector.find('.selected').removeClass('selected');

    };

    exports.SocialHistoryController = SocialHistoryController;
})(OpenEyes.OphCiExamination);