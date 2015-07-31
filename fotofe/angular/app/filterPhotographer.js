onmessage = function (e) {
    var result = e.data;

    this.fotografengattungen = '';
    this.bildgattungen = '';
    this.kanton = '';
    this.venues = '';

    result.forEach(addFilters);

    // return filters in array
    postMessage([this.fotografengattungen.split(',').filter(onlyUnique),
        this.bildgattungen.split(',').filter(onlyUnique),
        this.kanton.split(',').filter(onlyUnique),
        this.venues.split(',').filter(onlyUnique)]);
}

// extract filter values from answer array & add to string
addFilters = function (element, index, array) {
    if (element.fotografengattungen != '') {
        this.fotografengattungen = this.fotografengattungen + element.fotografengattungen + ',';
    }
    if (element.bildgattungen != '') {
        this.bildgattungen = this.bildgattungen + element.bildgattungen + ',';
    }
    if (element.kanton != '') {
        this.kanton = this.kanton + element.kanton + ',';
    }
    if (element.arbeitsperioden != '') {
        this.venues = this.venues + element.arbeitsperioden + ',';
    }
}

// unique filter on arrays, needs most of time, according to http://stackoverflow.com/questions/1960473/unique-values-in-an-array/14438954#14438954
function onlyUnique(value, index, self) {
    return self.indexOf(value) === index && value;
}