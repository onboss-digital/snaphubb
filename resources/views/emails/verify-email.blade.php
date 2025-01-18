<!doctype html>
<html lang="pt">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body
    style="
      box-sizing: border-box;
      padding: 50px 0;
      padding-left: 15px;
      padding-right: 15px;
      width: 100%;
      font-family: Arial, sans-serif;
      font-size: 15px;
      background: #000000;
      color: #ffffff;
    ">
    <div
        style="
        box-sizing: border-box;
        max-width: 80%;
        width: 600px;
        margin: auto;
        padding: 20px 30px;
        font-size: 14px;
        line-height: 22px;
        border-radius: 10px;
        background: #171a1a;
        color: #ffffff;
      ">
        <p style="text-align: center;">
            <div
                style="
                display: block;
                width: 250px;
                height: auto;
                margin: 0px auto 70px;
                ">
                <a href="{{ url('/') }}">
                    <img style="width: 100%; height: auto" src="{{ asset(setting('logo')) }}" alt="SNAPHUB" />
                </a>
            </div>
        </p>
        <p style="text-align: center; font-size: 17px; font-weight: 900">
            {{ __('email.welcome') }}
        </p>
        <p style="text-align: center">
            {{ __('email.happy_to_have_you') }}
        </p>
        <p style="text-align: center; margin: 20px 0px 70px 0;">
            <a style="
            text-decoration: none;
            background-color: #ff0000;
            color: #ffffff;
            padding: 15px 30px;
            margin: 40px 0;
            border-radius: 5px;
          "
                href="{{ $actionUrl }}">{{ __('email.confirm_account') }}</a>
        </p>
        <p style="text-align: center">
            {{ __('email.copy_link') }}
        </p>
        <p style="text-align: center; color: #ff0000; word-wrap: break-word;">{{ $actionUrl }}</p>
        <p style="margin-top: 20px; text-align: center; font-size: 13px">
            {{ __('email.link_expires') }}
        </p>
        <p style="text-align: center">
            {{ __('email.ignore_email') }}
        </p>
        <div
            style="
          font-size: 15px;
          border-top: 1px solid #727171;
          padding-top: 20px;
        ">
            <p style="color: #ffffff; text-align: center;">
                {{ __('email.contact_us') }} <a style="color: #ff0000" href="mailto:contato@snaphubb.com">contato@snaphubb.com</a>.
            </p>
            <p style="text-align: center;">{{ __('email.best_regards') }}</p>
        </div>
    </div>
    <div
        style="
        padding-top: 30px;
        color: #7f7f7f;
        font-size: 14px;
        text-align: center;
      ">
        <p>{{ __('email.do_not_reply') }}</p>
        <p>{{ __('email.published_on') }} current_date_and_time</p>
    </div>
</body>

</html>
