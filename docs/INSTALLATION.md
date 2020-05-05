# Installation

The only thing you need to do is to specify a Jitsi Server in the module configuration.

If you do not have your own Jitsi server, the official jitsi server "meet.jit.si" is automatically used.

## CSP (Security Hardening)

In case you've overwritten the default [content security settings](https://docs.humhub.org/docs/admin/security#web-security-configuration), you should make sure following resources are allowed:

- Requires **https://meet.jit.si/external_api.js** in `script-src`
- Requires **https://meet.jit.si** in `frame-src`

Example  common.php snippet:

```php
//...
"frame-src" => [
  "self" => true,
  "allow" => [  
    "https://www.youtube.com",
    "https://meet.jit.si",
  ]                        
],
"script-src" => [
  "self" => true,
  "unsafe-inline" => true,
  "allow" => [
    'https://meet.jit.si/external_api.js'
  ],
//...
```

> Note: The default csp should not block any of this.