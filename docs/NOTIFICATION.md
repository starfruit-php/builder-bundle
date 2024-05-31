# PUSH NOTIFICATION

## Custom config

```bash
starfruit_builder:
    ...
    notification:
        enable: true # send mail without dev mode skip, default false
        service: 'onesignal' # push service, support OneSignal
        custom_config:
            onesignal:
                sdk_link: 'htpps://.............' # replace default sdk link
```

## Setup

### OneSignal

Setup with [original documents](https://documentation.onesignal.com/docs/web-push-quickstart), OneSignal scripts will be added automatically.
