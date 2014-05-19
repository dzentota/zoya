var system = require('system');
if (system.args.length === 1) {
    console.log('Pass some args when invoking this script!');
}
var params = system.args;

var page = require('webpage').create(),
    response = {};
var headers = params[1] || {};
var settings = params[2] || {};
for (var i in settings) {
    page.settings[i] = settings[i];
}

page.onResourceTimeout = function (e) {
    response = e;
    response.status = e.errorCode;
};

page.onResourceReceived = function (r) {
    if (!response.status) response = r;
};
page.customHeaders = headers ? headers : {};

var url = params[3] || '';
var method = params[4] || 'GET';
var postBody = params[5] || null;

page.open(url, method, postBody, function (status) {
    if (status === 'success') {
        response.content = page.evaluate(function () {
            return document.getElementsByTagName('html')[0].innerHTML
        });
        console.log(JSON.stringify(response, undefined, 4));
        phantom.exit();
    } else {
        console.log(JSON.stringify(response, undefined, 4));
        phantom.exit();
    }
});
