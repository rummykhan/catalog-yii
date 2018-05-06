var Availability = function (globalRules, localRules) {

    this.availabilityGlobalRules = [];
    this.exceptionGlobalRules = [];
    this.availabilityLocalRules = [];
    this.exceptionLocalRules = [];

    var rulesByType = _.groupBy(globalRules, 'type');

    for (var key in rulesByType) {


        var rulesByKey = rulesByType[key];

        if (!rulesByKey) {
            continue;
        }

        if (key === 'Available') {
            this.availabilityGlobalRules = rulesByKey;
        } else if (key === 'Not Available') {
            this.exceptionGlobalRules = rulesByKey;
        }
    }

    var localRulesByType = _.groupBy(localRules, 'type');

    for (var key in localRulesByType) {


        var localRulesByKey = localRulesByType[key];

        if (!localRulesByKey) {
            continue;
        }

        if (key === 'Available') {
            this.availabilityLocalRules = localRulesByKey;
        } else if (key === 'Not Available') {
            this.exceptionLocalRules = localRulesByKey;
        }
    }

    console.log(this);
};

Availability.prototype.isDayAvailableGlobally = function (date) {
    var day = date.format('ddd');

    // find rule by day..
    var rules = _.filter(this.availabilityGlobalRules, {day: day});

    if (rules.length > 0) {
        return rules;
    }

    // find rule by All
    rules = _.filter(this.availabilityGlobalRules, {day: 'All'});

    return rules;
};


Availability.prototype.isDayNotAvailableGlobally = function (date) {
    var day = date.format('ddd');

    // find rule by day in exceptions..
    var rules = _.filter(this.exceptionGlobalRules, {day: day});

    if (rules.length > 0) {
        return rules;
    }

    // find rule by All
    rules = _.filter(this.exceptionGlobalRules, {day: 'All'});

    return rules;
};

Availability.prototype.isDayAvailableLocally = function (date) {

    // find rule by day..
    var rules = _.filter(this.availabilityLocalRules, {date: date.format('YYYY-MM-DD')});

    if (rules.length > 0) {
        return rules;
    }

    return [];
};


Availability.prototype.isDayNotAvailableLocally = function (date) {

    // find rule by day..
    var rules = _.filter(this.exceptionLocalRules, {date: date.format('YYYY-MM-DD')});

    if (rules.length > 0) {
        return rules;
    }

    return rules;
};


Availability.prototype.createRangeForRule = function (start, end, range) {
    for (var i = start; i <= end; i++) {
        range.push(i);
    }
    return range;
};

Availability.prototype.createRangeForRules = function (rules, range) {

    for (var i = 0; i < rules.length; i++) {
        var rule = rules[i];

        this.createRangeForRule(rule.start_time, rule.end_time, range);
    }

    if (range.length === 0) {
        return range;
    }

    return _.uniq(range);
};

Availability.prototype.getRangeDiff = function (availabilityRange, exceptionRange) {
    for (var i = 0; i < exceptionRange.length; i++) {

        var index = availabilityRange.indexOf(i);
        if (index > -1) {
            availabilityRange.splice(index, 1);
        }
    }

    return availabilityRange;
};

Availability.prototype.getAvailabilityData = function (date) {

    var globalAvailabilityRules = this.isDayAvailableGlobally(date);
    var availabilityRange = this.createRangeForRules(globalAvailabilityRules, []);

    var globalExceptionRules = this.isDayNotAvailableGlobally(date);
    var exceptionRange = this.createRangeForRules(globalExceptionRules, []);

    var localAvailabilityRules = this.isDayAvailableLocally(date);
    var localExceptionRules = this.isDayNotAvailableLocally(date);


    if (localAvailabilityRules.length > 0) {
        availabilityRange = this.createRangeForRules(localAvailabilityRules, availabilityRange);
    }

    if (localExceptionRules.length > 0) {
        exceptionRange = this.createRangeForRules(localExceptionRules, exceptionRange);
    }

    var merged = this.getRangeDiff(availabilityRange, exceptionRange);

    return {
        globalAvailability: globalAvailabilityRules,
        globalException: globalExceptionRules,
        localAvailability: localAvailabilityRules,
        locationExceptions: localExceptionRules,
        availabilityRange: availabilityRange,
        exceptionRange: exceptionRange,
        merged: merged,
    };
};