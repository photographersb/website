<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Authentication Complete</title>
  </head>
  <body>
    <p>Completing sign-in...</p>
    <script>
      (function () {
        var payload = {
          type: '{{ $status === "success" ? "oauth_success" : "oauth_error" }}',
          provider: '{{ $provider ?? "" }}',
          user: {!! json_encode($user ?? null) !!},
          error: '{{ $message ?? "" }}'
        };

        if (window.opener) {
          window.opener.postMessage(payload, window.location.origin);
        }

        window.close();
      })();
    </script>
  </body>
</html>
