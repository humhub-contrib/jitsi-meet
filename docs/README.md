# Jitsi Meet

Secure, fully featured, and completely free video conferencing.

Jitsi Meet is a fully encrypted, 100% open source video conferencing solution that you can use all day, every day, for free — with no account needed.

- No client software necessary, just pick a name for your room and you are ready to go
- Contacts can join via Brower or dial in via phone
- Screensharing makes your presentation easy
- Use the official Jitsi server or use your own for additional privacy

See (https://meet.jit.si/) for more details.

### CSP

In case you've overwritten the default [content security settings](https://docs.humhub.org/docs/admin/security#web-security-configuration). You should make sure following resources are allowed:

- Requires **https://meet.jit.si/external_api.js** in `script-src`
- Requires **https://meet.jit.si/external_api.js** in `frame-src`

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
  "allow" => [
    'https://meet.jit.si/external_api.js'
  ],
//...
```

> Note: The default csp should not block any of this.
