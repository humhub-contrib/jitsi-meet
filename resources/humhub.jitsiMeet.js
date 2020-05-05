humhub.module('jitsiMeet', function (module, require, $) {

    var client = require('client');
    var modal = require('ui.modal');
    var object = require('util').object;
    var Widget = require('ui.widget').Widget;
    var event = require('event');
    var loader = require('ui.loader');
    var ooJSLoadRetries = 0;

    var Room = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(Room, Widget);

    Room.prototype.getDefaultOptions = function () {
        return {
            'roomName': 'unnamed',
            'jwt': '',
        };
    };

    Room.prototype.init = function () {
        var that = this;

        this.initJitsi();

        this.modal = modal.get('#jitsiMeet-modal');
        this.modal.$.on('hidden.bs.modal', function (evt) {
            that.modal.clear();
        });

    };

    Room.prototype.close = function (evt) {
        var that = this;

        this.modal.clear();
        this.modal.close();
        evt.finish();

        this.jitsiApi.executeCommand('hangup');

    }

    Room.prototype.initJitsi = function () {
        var that = this;
        const domain = this.options.jitsidomain;

        r = this.options.roomname;
        if (typeof this.options.roomprefix === 'string' && this.options.roomprefix != '') {
            r = this.options.roomprefix + this.options.roomname;
        }

        jwt = this.options.jwt;

        const options = {
            roomName: r,
            parentNode: document.querySelector('#jitsiMeetD'),
            //Todo: Fixme
            height: window.innerHeight - 160,
            jwt: jwt,
            nossl: jwt == '',
            interfaceConfigOverwrite: {
                RECENT_LIST_ENABLED: false,
                GENERATE_ROOMNAMES_ON_WELCOME_PAGE: false,
                DISPLAY_WELCOME_PAGE_CONTENT: false,
                //filmStripOnly: true,
            },
            userInfo: {
                fullName: this.options.userdisplayname,
                displayName: this.options.userdisplayname,
            },
            configOverwrite: {
                // Workaround for broken "open in app" link on Android
                disableDeepLinking: true,
            }
        };

        this.jitsiApi = new JitsiMeetExternalAPI(domain, options);
        this.jitsiApi.executeCommand('displayName', this.options.userdisplayname);
        this.jitsiApi.addEventListeners({
            readyToClose: function () {
                that.close();
            },
        });
    }


    module.export({
        //init: init,
        Room: Room,
    });

});
