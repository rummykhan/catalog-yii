(function ($) {

    var ProviderCalendar = function (element, options) {
        this.element = element;

        this._initializeOptions(options);
        this._initializeEvents(options);
        this._initializeAvailabilityManager();
        this._render();
    };


    ProviderCalendar.prototype = {

        constructor: ProviderCalendar,


        _render: function () {
            var that = this;

            that.refreshRulesDisplay(this.options.GlobalAvailabilityRules);

            $('#year-calendar').calendar({
                startYear: moment().format('Y'),
                minDate: that.options.startDateMoment.toDate(),
                maxDate: that.options.endDateMoment.toDate(),
                startMonth: that.options.startDateMoment.format('MM'),
                clickDay: function (e) {

                    var rules = $(e.element).find('div.day-content').attr('data-rules');

                    that.onDayClick(e.date, rules);

                },
                customDayRenderer: function (element, date) {
                    var momentDate = moment(date);

                    if (momentDate.isBefore(that.options.startDateMoment)) {
                        return true;
                    }

                    var data = that.manager.getAvailabilityData(momentDate);

                    // mean we have availability after all the merges..
                    if (data.merged.length > 0) {

                        var opacity = (data.merged.length / 9).toFixed(1);

                        $(element).parent().css('box-shadow', 'rgb(156, 183, 3) 0px -4px 0px 0px inset');
                        $(element).attr('data-rules', JSON.stringify(data));

                    }
                }
            });
        },

        _initializeAvailabilityManager: function () {
            this.manager = new Availability(this.options.GlobalAvailabilityRules, this.options.DateAvailabilityRules);
        },

        _initializeEvents: function (opt) {
            this.onGlobalModalTrigger();
            this.onAddingAvailabilityRule();
            this.onAddingDateRule();
            this.onGlobalAvailabilitySelection();
            this.onGlobalRuleSelection();
            this.onGlobalRuleDelete();
        },

        _initializeOptions: function (opt) {
            this.options = $.extend({
                startDate: null,
                endDate: null,
                startDateMoment: null,
                endDateMoment: null,

                // Global Availability Type Selector
                GlobalAvailabilityTypeSelector: null,
                GlobalDaySelector: null,
                GlobalStartTimeSelector: null,
                GlobalEndTimeSelector: null,
                GlobalPriceValueContainer: null,
                GlobalRulePriceTypeSelector: null,
                GlobalRuleUpdateAsSelector: null,
                GlobalRuleUpdatePriceSelector: null,
                GlobalRulesContainer: null,
                GlobalModalTriggerSelector: null,
                GlobalModal: null,
                GlobalRulesListTitle: null,
                GlobalRulesList: null,
                GlobalRuleRemoveSelector: null,
                GlobalAvailabilityRules: [],

                // Date Availability Type Selectors
                DateRuleTypeSelector: null,
                DatePriceValueContainerSelector: null,
                DateRulesTableSelector: null,
                DateRuleStartTimeSelector: null,
                DateRuleEndTimeSelector: null,
                DateRulePriceTypeSelector: null,
                DateRuleUpdateAsSelector: null,
                DateRuleUpdatePriceSelector: null,
                DateRuleModalDateSelector: null,
                DateRulesSelector: null,
                DateRulesDateSelector: null,
                DateRuleAvailabilityHoursSelector: null,
                DateRulesListTitle: null,
                DateRulesList: null,
                DateRuleAppliedRules: [],
                DateAvailabilityRules: [],
                DateRuleRemoveSelector: null,
            }, opt);

            this.options.startDateMoment = moment(this.options.startDate, 'YYYY-MM-DD');
            this.options.endDateMoment = moment(this.options.endDate, 'YYYY-MM-DD');

            for (var option in this.options) {
                if (this.options[option] === null) {
                    throw new Error(option + ' value cannot be null');
                }
            }
        },

        // Events

        // When user clicks on add availability rule..
        onGlobalModalTrigger: function () {
            var that = this;
            $(that.options.GlobalModalTriggerSelector).click(function (e) {
                $(that.options.GlobalModal).modal();
            });
        },

        // whenever a day is clicked.
        onDayClick: function (date, rules) {

            var momentDate = moment(date);

            $('#date-modal').modal();
            $(this.options.DateRuleModalDateSelector).text(momentDate.format('YYYY-MM-DD'));
            $(this.options.DateRulesDateSelector).val(momentDate.format('YYYY-MM-DD'));
            this.refreshDateRuleTableDisplay(this.options.DateAvailabilityRules);
            this.showDateAvailability(rules);
            //showDateAppliedRules(rules);
        },


        onAddingAvailabilityRule: function () {
            var that = this;
            $('#add-ar-rule').click(function (e) {
                var type = $(that.options.GlobalAvailabilityTypeSelector).val();

                if (!type) {
                    return false;
                }

                var day = $(that.options.GlobalDaySelector).val();
                var startTime = $(that.options.GlobalStartTimeSelector).val();
                var endTime = $(that.options.GlobalEndTimeSelector).val();

                if (!!day && !!startTime && !!endTime) {

                    var identifier = day + startTime + endTime + type;

                    if (!that.isAlreadyAdded(identifier, that.options.GlobalAvailabilityRules)) {
                        that.options.GlobalAvailabilityRules.push({
                            day: day,
                            start_time: startTime,
                            end_time: endTime,
                            type: type,
                            identifier: identifier,
                            price_type: $(that.options.GlobalRulePriceTypeSelector).val(),
                            update_as: $(that.options.GlobalRuleUpdateAsSelector).val(),
                            value: $(that.options.GlobalRuleUpdatePriceSelector).val(),
                        });
                        that.refreshRulesDisplay(that.options.GlobalAvailabilityRules);
                    }
                }

                $(that.options.GlobalDaySelector).val('').trigger('change');
                $(that.options.GlobalStartTimeSelector).val('').trigger('change');
                $(that.options.GlobalEndTimeSelector).val('').trigger('change');
                $(that.options.GlobalAvailabilityTypeSelector).val('').trigger('change');
                $(that.options.GlobalRulePriceTypeSelector).val('').trigger('change');
                $(that.options.GlobalRuleUpdateAsSelector).val('').trigger('change');
                $(that.options.GlobalRuleUpdatePriceSelector).val('');

                $('#availability-rules').val(JSON.stringify(that.options.GlobalAvailabilityRules));
            });

        },

        onGlobalAvailabilitySelection: function () {
            var that = this;
            $(that.options.GlobalAvailabilityTypeSelector).on('select2:select', function (e) {

                var data = e.params.data;

                if (data.id === 'Available') {
                    $(that.options.GlobalPriceValueContainer).removeClass('hidden');
                } else {
                    if (!$(that.options.GlobalPriceValueContainer).hasClass('hidden')) {
                        $(that.options.GlobalPriceValueContainer).addClass('hidden');
                    }
                }
            });
        },

        onGlobalRuleSelection: function () {
            var that = this;
            $(that.options.DateRuleTypeSelector).on('select2:select', function (e) {

                var data = e.params.data;

                if (data.id === 'Available') {
                    $(that.options.DatePriceValueContainerSelector).removeClass('hidden');
                } else {
                    if (!$(that.options.DatePriceValueContainerSelector).hasClass('hidden')) {
                        $(that.options.DatePriceValueContainerSelector).addClass('hidden');
                    }
                }
            });
        },

        onAddingDateRule: function () {
            var that = this;
            $('#add-date-rule').click(function (e) {
                var type = $(that.options.DateRuleTypeSelector).val();

                if (!type) {
                    return false;
                }

                if (type === 'Available') {
                    that.addDateAvailableRule();
                } else {
                    that.addDateNotAvailableRule();
                }

                if (that.options.DateAvailabilityRules.length > 0) {
                    $(that.options.DateRulesSelector).val(JSON.stringify(that.options.DateAvailabilityRules));
                }
            });
        },


        onGlobalRuleDelete: function () {
            var that = this;
            $(document).on('click', this.options.GlobalRuleRemoveSelector, function () {
                var element = $(this);
                var id = element.attr('data-id');
                that.options.GlobalAvailabilityRules = that.removeRuleByIdentifier(that.options.GlobalAvailabilityRules, id);
                that.refreshRulesDisplay(that.options.GlobalAvailabilityRules);
            });
        },

        onDateRuleDelete: function () {
            var that = this;
            $(document).on('click', that.options.DateRuleRemoveSelector, function () {
                var element = $(this);
                var id = element.attr('data-id');
                that.options.DateAvailabilityRules = that.removeRuleByIdentifier(that.options.DateAvailabilityRules, id);
                that.refreshDateRuleTableDisplay(that.options.DateAvailabilityRules);
            });
        },


        // Functions
        refreshRulesDisplay: function (rules) {
            var that = this;

            var globalRulesBody = $(that.options.GlobalRulesContainer);
            globalRulesBody.empty();

            var globalRulesList = $(that.options.GlobalRulesList);
            globalRulesList.empty();

            $.each(rules, function (index, value) {

                var identifier = value.day + value.start_time + value.end_time + value.type;

                var tableRow =
                    '<tr>' +
                    '<td><button class="btn btn-danger btn-xs delete-global-rule" data-id="' + identifier + '"><i class="glyphicon glyphicon-trash"></i></button></td>' +
                    '<td>' + value.type + '</td>' +
                    '<td>' + value.day + '</td>' +
                    '<td>' + value.start_time + '</td>' +
                    '<td>' + value.end_time + '</td>' +
                    (!!value.price_type ? '<td>' + value.price_type + '</td>' : '<td></td>') +
                    (!!value.update_as ? '<td>' + value.update_as + '</td>' : '<td></td>') +
                    (!!value.value ? '<td>' + value.value + '</td>' : '<td></td>') +
                    '</tr>';
                globalRulesBody.append(tableRow);

                var ruleListItem =
                    '<li class="list-group-item">' +
                    '<button class="btn btn-danger btn-xs delete-global-rule" style="margin-right:10px;" data-id="' + identifier + '"><i class="glyphicon glyphicon-trash"></i></button> ' +
                    value.type + ' ON ' + value.day + ' Day(s) From ' + value.start_time + ' To ' + value.end_time +
                    '</li>';

                globalRulesList.append(ruleListItem);
            });

            var globalRulesListTitle = $(that.options.GlobalRulesListTitle);

            if (rules.length > 0) {
                globalRulesListTitle.removeClass('hidden');
            } else {
                globalRulesListTitle.addClass('hidden');
            }
        },

        isAlreadyAdded: function (identifier, rules) {
            for (var i = 0; i < rules.length; i++) {
                var rule = rules[i];
                if (!rule) {
                    continue;
                }

                if (rule['identifier'] === identifier) {
                    return true;
                }
            }
            return false;
        },

        refreshDateRuleTableDisplay: function (rules) {
            var that = this;
            $(that.options.DateRulesTableSelector).empty();

            var dateRulesList = $(that.options.DateRulesList);
            dateRulesList.empty();

            rules = _.filter(rules, {date: $(that.options.DateRuleModalDateSelector).text()}) || [];


            $.each(rules, function (index, value) {

                var identifier = value.start_time + value.end_time + 'Available' + $(that.options.DateRuleModalDateSelector).text();

                var tableRow = '<tr>' +
                    '<td><button class="btn btn-danger btn-xs delete-date-rule" style="margin-right:10px;" data-id="' + identifier + '"><i class="glyphicon glyphicon-trash"></i></button> </td>' +
                    '<td>' + value.type + '</td>' +
                    '<td>' + value.start_time + '</td>' +
                    '<td>' + value.end_time + '</td>' +
                    (!!value.price_type ? '<td>' + value.price_type + '</td>' : '<td></td>') +
                    (!!value.update_as ? '<td>' + value.update_as + '</td>' : '<td></td>') +
                    (!!value.value ? '<td>' + value.value + '</td>' : '<td></td>') +
                    '</tr>';

                $(that.options.DateRulesTableSelector).append(tableRow);

                var ruleListItem =
                    '<li class="list-group-item">' +
                    '<button class="btn btn-danger btn-xs delete-date-rule" style="margin-right:10px;" data-id="' + identifier + '"><i class="glyphicon glyphicon-trash"></i></button> ' +
                    value.type + ' on ' + value.date + ' Day, from ' + value.start_time + ' to ' + value.end_time +
                    '</li>';

                dateRulesList.append(ruleListItem);

            });

            var dateRulesListTitle = $(that.options.DateRulesListTitle);
            // TODO: we have to remove the both bodies and instead we should show and hide the list titles in the new format
            if (rules.length > 0) {
                dateRulesListTitle.removeClass('hidden');
            } else {
                dateRulesListTitle.addClass('hidden');
            }
        },

        refreshDateControls: function () {
            var that = this;
            $(that.options.DateRuleStartTimeSelector).val('').trigger('change');
            $(that.options.DateRuleEndTimeSelector).val('').trigger('change');
            $(that.options.DateRulePriceTypeSelector).val('').trigger('change');
            $(that.options.DateRuleUpdateAsSelector).val('').trigger('change');
            $(that.options.DateRuleUpdatePriceSelector).val('').trigger('change');
            $(that.options.DateRuleTypeSelector).val('').trigger('change');
        },

        addDateAvailableRule: function () {
            var that = this;
            var startTime = $(that.options.DateRuleStartTimeSelector).val();
            var endTime = $(that.options.DateRuleEndTimeSelector).val();
            var priceType = $(that.options.DateRulePriceTypeSelector).val();
            var updateAs = $(that.options.DateRuleUpdateAsSelector).val();
            var value = $(that.options.DateRuleUpdatePriceSelector).val();

            if (!!startTime && !!endTime) {
                var identifier = startTime + endTime + 'Available' + $(DateRuleModalDateSelector).text();
                var obj = {
                    start_time: startTime,
                    end_time: endTime,
                    type: 'Available',
                    price_type: null,
                    update_as: null,
                    value: null,
                    date: $(that.options.DateRuleModalDateSelector).text(),
                    identifier: identifier
                };

                if (!!priceType && !!updateAs && !!value) {
                    obj['price_type'] = priceType;
                    obj['update_as'] = updateAs;
                    obj['value'] = value;
                }

                if (!that.isAlreadyAdded(identifier, that.options.DateAvailabilityRules)) {
                    that.options.DateAvailabilityRules.push(obj);
                }
            }

            that.refreshDateRuleTableDisplay(that.options.DateAvailabilityRules);
            that.refreshDateControls();
        },

        addDateNotAvailableRule: function () {

            var that = this;

            var startTime = $(that.options.DateRuleStartTimeSelector).val();
            var endTime = $(that.options.DateRuleEndTimeSelector).val();

            if (!!startTime && !!endTime) {

                var identifier = startTime + endTime + 'Not Available' + $(that.options.DateRuleModalDateSelector).text();

                var obj = {
                    start_time: startTime,
                    end_time: endTime,
                    type: 'Not Available',
                    price_type: null,
                    update_as: null,
                    value: null,
                    date: $(that.options.DateRuleModalDateSelector).text(),
                    identifier: identifier
                };

                if (!that.isAlreadyAdded(identifier, that.options.DateAvailabilityRules)) {
                    that.options.DateAvailabilityRules.push(obj);
                }
            }

            that.refreshDateRuleTableDisplay(that.options.DateAvailabilityRules);
            that.refreshDateControls();
        },

        showDateAvailability: function (rules) {
            var that = this;
            $(that.options.DateRuleAvailabilityHoursSelector).empty();
            rules = JSON.parse(rules);

            for (var i = 0; i <= 23; i++) {

                var elementClass = '';

                if (rules.merged.indexOf(i) > -1) {
                    elementClass = 'active';
                } else {
                    elementClass = 'disabled';
                }

                var elementHtml = '<li class="' + elementClass + '"><a href="#">' + i + ' <span class="sr-only">' + i + '</span></a></li>';

                $(that.options.DateRuleAvailabilityHoursSelector).append(elementHtml);
            }
        },

        removeRuleByIdentifier: function (rules, id) {
            return rules.filter(function (rule) {
                return rule.identifier !== id;
            });
        }
    };


    $.fn.providerCalendar = function (options) {
        return new ProviderCalendar($(this), options);
    };

}(jQuery));