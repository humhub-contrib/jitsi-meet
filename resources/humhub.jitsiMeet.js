humhub.module('jitsiMeet', function (module, require, $) {
    var modal = require('ui.modal');
    var object = require('util').object;
    var Widget = require('ui.widget').Widget;

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

        this.modal = modal.get('jitsiMeet-modal');
        this.modal.$.on('hidden.bs.modal', function (evt) {
            that.modal.clear();
        });
    };

    Room.prototype.close = function (evt) {
        this.modal.clear();
        this.modal.close();
        evt.finish();

        this.jitsiApi.executeCommand('hangup');

    }

    Room.prototype.initJitsi = function () {
        var that = this;

        var mode = this.options.mode || 'self_hosted';
        var domain = this.options.jitsidomain;
        var roomName = this.options.roomname;

        // Enhanced console logging for debugging
        console.log('JitsiMeet Room Widget - Initializing Jitsi');
        console.log('Mode:', mode);
        console.log('Original domain:', domain);
        console.log('Original roomName:', roomName);
        console.log('JWT present:', !!this.options.jwt);

        if (typeof this.options.roomprefix === 'string' && this.options.roomprefix !== '') {
            roomName = this.options.roomprefix + this.options.roomname;
            console.log('Room name with prefix:', roomName);
        }

        if (mode === 'jaas') {
            // Override domain and roomName per JaaS
            domain = this.options.jaasdomain || '8x8.vc';
            var appId = this.options.jaasappid;
            if (appId) {
                roomName = appId + '/' + this.options.roomname;
                console.log('JaaS room name:', roomName);
            }
            console.log('JaaS domain:', domain);
        }

        jwt = this.options.jwt;

        // Get base URL and domain for generating correct invitation links
        var baseUrl = window.location.protocol + '//' + window.location.host;
        var inviteDomain = window.location.host;
        var originalRoomName = this.options.roomname; // Original room name without app ID prefix
        var conferenceUrl = baseUrl + '/conference/' + originalRoomName;
        
        // Custom invite service URL - this endpoint will return correct URL format
        var inviteServiceUrl = baseUrl + '/jitsi-meet-cloud-8x8/room/invite';

        // Check if startSilent is requested (for dial-in scenarios)
        var startSilent = this.options.startSilent === true || 
                         (typeof this.options.startSilent === 'string' && this.options.startSilent === 'true') ||
                         window.location.hash.indexOf('config.startSilent=true') !== -1;

        const options = {
            roomName: roomName,
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
                // Configure invite domain to use our domain
                inviteDomain: inviteDomain,
                // Configure custom invite service URL
                // This tells Jitsi Meet to use our endpoint for generating invitation URLs
                inviteServiceUrl: inviteServiceUrl,
                // Use brandingRoomAlias to customize invite link format
                // This ensures recording bot emails use /conference/{roomName} format
                brandingRoomAlias: 'conference/' + originalRoomName,
                // Configure deployment info
                deploymentInfo: {
                    shard: 'shard1',
                    region: 'us',
                    userRegion: 'us',
                    appId: mode === 'jaas' ? this.options.jaasappid : undefined
                },
                // Configure startSilent for dial-in scenarios
                startSilent: startSilent,
                startAudioMuted: startSilent,
                startVideoMuted: false,
            }
        };

        console.log('JitsiMeet API Options:', options);

        try {
            this.jitsiApi = new JitsiMeetExternalAPI(domain, options);
            console.log('JitsiMeet API initialized successfully');
            console.log('Conference URL for invitations:', conferenceUrl);
            console.log('Invite service URL:', inviteServiceUrl);
            
            // Override getRoomURL method to return correct URL format
            // This ensures share/invite functionality uses /conference/{roomName} format
            if (this.jitsiApi && typeof this.jitsiApi.getRoomURL === 'function') {
                var originalGetRoomURL = this.jitsiApi.getRoomURL.bind(this.jitsiApi);
                this.jitsiApi.getRoomURL = function() {
                    console.log('Overriding getRoomURL - returning:', conferenceUrl);
                    return conferenceUrl;
                };
            }
            
            // Also try to override the room URL property if it exists
            if (this.jitsiApi && this.jitsiApi._room) {
                // Store original room name but override URL generation
                console.log('JitsiMeet API - Room object found, attempting to override URL');
            }
            
            this.jitsiApi.addEventListeners({
                readyToClose: function () {
                    console.log('JitsiMeet API - readyToClose event');
                    that.close();
                },
                videoConferenceJoined: function () {
                    console.log('JitsiMeet API - videoConferenceJoined event');
                    
                    // After joining, try to override invitation URL generation
                    // Intercept any invitation/share actions
                    setTimeout(function() {
                        // Override getRoomURL again after API is fully initialized
                        if (that.jitsiApi && typeof that.jitsiApi.getRoomURL === 'function') {
                            that.jitsiApi.getRoomURL = function() {
                                console.log('Overriding getRoomURL after join - returning:', conferenceUrl);
                                return conferenceUrl;
                            };
                        }
                        
                        // Try to find and override invitation UI elements
                        // This is a workaround to ensure share links use correct format
                        var inviteButtons = document.querySelectorAll('[data-i18n*="invite"], [aria-label*="invite"], [title*="invite"]');
                        if (inviteButtons.length > 0) {
                            console.log('Found invite buttons, setting up click handlers');
                            inviteButtons.forEach(function(btn) {
                                btn.addEventListener('click', function(e) {
                                    console.log('Invite button clicked, conference URL:', conferenceUrl);
                                    // The invite service URL should handle this, but log for debugging
                                });
                            });
                        }
                    }, 2000); // Wait 2 seconds for UI to fully load
                },
                videoConferenceLeft: function () {
                    console.log('JitsiMeet API - videoConferenceLeft event');
                },
                error: function (error) {
                    console.error('JitsiMeet API Error:', error);
                }
            });
        } catch (error) {
            console.error('Failed to initialize JitsiMeet API:', error);
            // Display user-friendly error message
            document.querySelector('#jitsiMeetD').innerHTML = 
                '<div style="padding: 20px; text-align: center; color: red;">' +
                '<h3>Failed to load video conference</h3>' +
                '<p>Please check your configuration and try again.</p>' +
                '<p>Error: ' + error.message + '</p>' +
                '</div>';
        }
    }

    module.export({
        Room: Room,
    });

});
