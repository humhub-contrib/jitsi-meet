# Installation

The only thing you need to do is to specify a Jitsi Server in the module configuration.

If you do not have your own Jitsi server, a list of some tested public servers is provided in the configuration.

## CSP (Security Hardening)

In case you've overwritten the default [content security settings](https://docs.humhub.org/docs/admin/security#web-security-configuration), you should make sure following resources are allowed:

- Requires **https://your-jitsi-server.tld/external_api.js** in `script-src`
- Requires **https://your-jitsi-server.tld** in `frame-src`

Example  common.php snippet:

```php
//...
"frame-src" => [
  "self" => true,
  "allow" => [  
    "https://www.youtube.com",
    "https://your-jitsi-server.tld",
  ]                        
],
"script-src" => [
  "self" => true,
  "unsafe-inline" => true,
  "allow" => [
    'https://your-jitsi-server.tld/external_api.js'
  ],
//...
```

> Note: The default csp should not block any of this.
